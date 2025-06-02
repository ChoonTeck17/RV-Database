<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NPSImport;
use App\Imports\RAWImport;
use App\Imports\RFMImport;
use App\Models\Bnb;
use App\Exports\DataExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExcelController extends Controller
{

    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    //     ]);

    //     $fileType = $request->input('file_type');

    //     if ($fileType === 'nps') {
    //         Excel::import(new NPSImport(), $request->file('file'));
    //         $data = Bnb::whereNotNull('last_transaction_date')->paginate(10);
    //     } elseif ($fileType === 'raw') {
    //         Excel::import(new RAWImport(), $request->file('file'));
    //         $data = Bnb::paginate(10);
    //     } elseif ($fileType === 'rfm') {
    //         Excel::import(new RFMImport(), $request->file('file'));
    //         $data = Bnb::paginate(10);
    //     } else {
    //         return back()->withErrors(['file_type' => 'Invalid file type selected.']);
    //     }

    //     return view('upload', compact('data'))->with('success', ucfirst($fileType) . ' file uploaded successfully!');
    // }


    // public function showData(Request $request)
    // {
    //     $perPage = $request->input('per_page', 10);
    //     $data = Bnb::paginate($perPage);
    //     return view('upload', compact('data'));
    // }

     //this is for if i have the radio buttons to validate which file to upload


    public function uploadNPS(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        Excel::import(new NPSImport(), $request->file('file'));

        $data = Bnb::whereNotNull('last_transaction_date')->paginate(10);
        // $data = Bnb::paginate(10); // Adjust filtering if RFM has specific criteria
        return view('NpsUpload', compact('data'))->with('success', 'NPS file uploaded successfully!');
    }

    public function uploadRFM(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'segments' => 'nullable|array', // Optional, ensures segments is an array
        ]);
    
        $segments = $request->input('segments', []); // e.g., ['mfm', 'tr']
        Excel::import(new RFMImport($segments), $request->file('file'));
        return redirect()->back()->with('success', 'RFM data uploaded successfully');
    }

    public function uploadRAW(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        Excel::import(new RAWImport, $request->file('file'));
        $data = Bnb::paginate(10); // Adjust filtering if RFM has specific criteria
        $data = Bnb::whereNotNull('points_last_updated_month')->paginate(10);
        return redirect()->back()->with('success', 'Raw data uploaded successfully');
        }

    public function showNPSUpload(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $data = Bnb::whereNotNull('last_transaction_date')->paginate($perPage);
        return view('NpsUpload', compact('data')); // Note the case sensitivity
    }


    public function showRFMUpload(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $data = Bnb::whereNotNull('brand')->paginate($perPage); // Adjust filtering if RFM has specific criteria
        return view('RFMUpload', compact('data'));
    }

    public function showRAWUpload(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $data = Bnb::whereNotNull('points_last_updated_month')->paginate($perPage); // Adjust filtering if RFM has specific criteria
        return view('RAWUpload', compact('data'));
    }

    
    public function downloadExcel()
    {
        $exportType = request()->query('type', 'all'); // Get 'type' from query, default to 'all'
        return Excel::download(new DataExport($exportType), 'data.xlsx');
    }
    public function downloadPDF()
    {
        $data = Bnb::all();
        $pdf = Pdf::loadView('pdf.export', compact('data'));
        return $pdf->download('data.pdf');
    }
}