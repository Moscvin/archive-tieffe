<?php

namespace App\Http\Controllers\Api\Technician;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoreUsers as User;

class ListController extends Controller
{
    public function main(Request $request)
    {
        try {
            $technicians = User::where([['isactive', 1], ['id_group', 9]])->get()->map(function($item) {
                return [
                    'id_user' => $item->id_user,
                    'name' => $item->fullName
                ];
            });
            return response()->json([
                'status' => 'ok',
                'tecnici' => $technicians
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
