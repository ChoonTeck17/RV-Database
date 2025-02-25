<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Models\Bnb;
use App\Exports\DataExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ExcelController extends Controller
{
    // public function upload(Request $request)

    public function upload(Request $request)
        {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            ]);

            $updateMFM = $request->has('mfm_segment');
            $updateTR = $request->has('tr_segment');
            $updateNYSS = $request->has('nyss_segment');
            // dd($updateMFM, $updateTR, $updateNYSS);

            Excel::import(new ExcelImport($updateMFM, $updateTR, $updateNYSS), $request->file('file'));

            // Excel::import(new ExcelImport(), $request->file('file'));

            return redirect()->back()->with('success', 'Data Imported Successfully!');
        }






    // {
    //     $npsOnly = $request->has('nps_only'); // Check if NPS Only is selected

    //     // Validate based on selection
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    //         'file_type' => 'required|in:TADA,RFM,NPS',

    //         // 'mfm_segment' => $npsOnly ? 'nullable' : 'required_without_all:tr_segment,nyss_segment',
    //         // 'tr_segment' => $npsOnly ? 'nullable' : 'required_without_all:mfm_segment,nyss_segment',
    //         // 'nyss_segment' => $npsOnly ? 'nullable' : 'required_without_all:mfm_segment,tr_segment',
    //     // ], [
    //     //     'mfm_segment.required_without_all' => 'At least one segment must be selected unless using NPS Only.',
        
    // ]);
    // $isNPS = $request->file_type === 'NPS';


    //     // Determine what to update
    //     // $updateMFM = !$npsOnly && $request->has('mfm_segment');
    //     // $updateTR = !$npsOnly && $request->has('tr_segment');
    //     // $updateNYSS = !$npsOnly && $request->has('nyss_segment');
    //     $updateMFM = $request->has('mfm_segment') && !$isNPS;
    //     $updateTR = $request->has('tr_segment') && !$isNPS;
    //     $updateNYSS = $request->has('nyss_segment') && !$isNPS;

    //     // Log::info("Processing Upload | NPS Only: {$npsOnly} | MFM: {$updateMFM} | TR: {$updateTR} | NYSS: {$updateNYSS}");
    //     Log::info("MFM: {$updateMFM}, TR: {$updateTR}, NYSS: {$updateNYSS}, File Type: {$request->file_type}");


    //     // Pass data to Excel Import
    //     // Excel::import(new ExcelImport($updateMFM, $updateTR, $updateNYSS, $npsOnly), $request->file('file'));
    //     Excel::import(new ExcelImport($updateMFM, $updateTR, $updateNYSS, $isNPS), $request->file('file'));

    //     return redirect()->back()->with('success', 'Data Imported Successfully!');
    // }


    public function showData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
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
