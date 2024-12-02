<?php

namespace App\Http\Controllers\Api\Technician;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoreUsers as User;
use App\Http\Requests\Api\Technician\CoordinateRequest;
use App\Models\CoordinateHistory;

class CoordinateController extends Controller
{
    public function main(CoordinateRequest $request)
    {
        try {
            $user = User::where('app_token', $request->header('userToken'))->first();
            $user->update([
                'lat' => $request->lat,
                'lng' => $request->lng,
                'coord_updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->saveHistory($user->id_user, $request->lat, $request->lng);
            return response()->json([], 204);
        } catch(\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function saveHistory($id, $lat, $lng)
    {
        return CoordinateHistory::create([
            'user_id' => $id,
            'lat' => $lat,
            'lng' => $lng
        ]);
    }
}
