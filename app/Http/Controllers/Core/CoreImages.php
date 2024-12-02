<?php
namespace App\Http\Controllers\Core;

use Carbon\Carbon;
use File;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
 
use Storage;

class CoreImages extends Controller {
    public function show($type, $id, $filename){
        $path = storage_path("app/$type/$id/$filename");
        Session::put('path', $path);
        if(!file_exists($path)) {
            return redirect('/error');
        }
        return response()->download($path);  
    }
}