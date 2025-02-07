<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // Display the list of uploaded files
    public function index()
    {
        $files = Storage::files('public/files');  // You can change the folder name if needed
        return view('index', compact('files'));
    }

    // Handle file import (upload)
    public function import(Request $request)
    {
        // Validate the file
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,pdf' // Adjust the allowed file types
        ]);

        // Store the uploaded file
        $path = $request->file('file')->store('public/files');
        
        return back()->with('success', 'File uploaded successfully!');
    }

    // Handle file download
    public function download($file)
    {
        // Check if the file exists in the storage
        $filePath = storage_path('app/public/files/' . $file);
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return abort(404);
    }
}

