<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Models\Bnb;
use App\Exports\DataExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExcelController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        Excel::import(new ExcelImport(), $request->file('file'));

        return redirect()->back()->with('success', 'Data Imported Successfully!');
    }

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
