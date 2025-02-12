<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Models\Bnb;

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
}
