<?php

namespace App\Http\Controllers\Api\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoreUsers;
use App\Models\Operation\Operation;

class EditNotAssignedController extends Controller
{
    public function main(Request $request, $id)
    {
        try {
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
            $technicians = ($user->id_user . ';' . $request->tecnico);
            Operation::where('id_intervento', $id)->update([
                'data' => $request->data,
                'tipologia' => $request->tipologia,
                'ora_dalle' => $request->ora_dalle,
                'ora_alle' => $request->ora_alle,
                'incasso' => $request->incasso,
                'tecnico' => $technicians,
                'pronto_intervento' => 0,
                'stato' => 1,
            ]);
            return response()->json([
                'status' => 'ok',
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
