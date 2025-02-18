<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Models\Bnb;
use App\Exports\DataExport; // You need to create this file
use Barryvdh\DomPDF\Facade\Pdf;


class ExcelController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'mfm_segment' => 'required_without_all:tr_segment,nyss_segment',
            'tr_segment' => 'required_without_all:mfm_segment,nyss_segment',
            'nyss_segment' => 'required_without_all:mfm_segment,tr_segment',
        ], [
            'mfm_segment.required_without_all' => 'At least one segment must be selected.',
            'tr_segment.required_without_all' => '',
            'nyss_segment.required_without_all' => '',
        ]);
    
        // Get selected segments from form (checkbox values)
        $updateMFM = $request->has('mfm_segment');
        $updateTR = $request->has('tr_segment');
        $updateNYSS = $request->has('nyss_segment');
        
        Log::info("MFM: {$updateMFM}, TR: {$updateTR}, NYSS: {$updateNYSS}");

        // Pass segment selections to the import class
        Excel::import(new ExcelImport($updateMFM, $updateTR, $updateNYSS), $request->file('file'));
    
        return redirect()->back()->with('success', 'Data Imported Successfully!');
    }

    // public function showData()
    // {
    //     $data = Bnb::all();
    //     return view('upload', compact('data'));
    // }

    public function showData(Request $request)
    {
        $perPage = $request->input('per_page', 5); // Default to 10 records per page
        $data = Bnb::paginate($perPage);

        return view('upload', compact('data'));
    }


    public function downloadExcel()
    {
        return Excel::download(new DataExport, 'data.xlsx');
    }
    public function downloadPDF()
    {
        $data = Bnb::all();
        $pdf = PDF::loadView('pdf.export', compact('data'));

        return $pdf->download('data.pdf');
    }
}
