<?php

namespace App\Http\Controllers\Imports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\ClientiImport;
use Excel;

class ClientiImportController extends Controller
{
    public function index()
    {
        return view('imports.clienti_import');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('select_file');
        $data = Excel::import(new ClientiImport, $path);
        return back()->with('success', 'Excel Data Imported successfully.');
    }
}
