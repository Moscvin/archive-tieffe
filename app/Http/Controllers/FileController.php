<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CoreUsers;

class FileController extends Controller {
    private $allowedPaths = [
        'brand'
    ];

    public function downloadFile($params, Request $request) {
        $path = storage_path("app/$params");
        if(!file_exists($path)) {
            return redirect()->back();
        }
        return response()->download($path);
    }

    public function previewFile($params, Request $request) {
        $path = storage_path("app/$params");
        if(!file_exists($path)) {
            return redirect()->back();
        }
        return response()->file($path);
    }

    public function downloadApiFile(Request $request) {
        try {
            $token = $request->header('userToken') ?? $request->cookie('userToken');
            $user = CoreUsers::where('app_token', $token)->first();
            if($user) {
                $route = preg_split('/v1\/file\//', $request->path);
                $path = storage_path("app/$route[1]");
                if(!file_exists($path)) {
                    return ApiResponse::error(500, 999, ['File not found']);
                }
                return response()->download($path);
            } else {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 1
                    ]
                ];
                return response()->json($response, 400);
            }
        } catch (\Throwable $e) {
            return ApiResponse::serverError($e);
        }
         
    }

    public function showImage($params, Request $request) {
        try {
            $path = storage_path("app/$params");
            if(!file_exists($path)) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 999
                    ]
                ];
                return response()->json($response, 400);
            }
            return response()->download($path);
        } catch (\Throwable $e) {
            $response = [
                'status' => 'error',
                'data' => [
                    'error_code' => 999
                ]
            ];
            return response()->json($response, 400);
        }
    }
}