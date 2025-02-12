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
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new ExcelImport, $request->file('file'));

        return redirect()->back()->with('success', 'Excel data uploaded successfully!');
    }

    public function showData()
    {
        $data = Bnb::all();
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
