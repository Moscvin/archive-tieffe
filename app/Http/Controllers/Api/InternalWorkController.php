<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InternalWork;
use App\Models\Operation;
use App\CoreUsers;

class InternalWorkController extends Controller {
    public function setInternalWork(Request $request) {
        try {
            $rules = [
                'tipo_lavoro' => 'required|in:1,2',
                'data_ora_inizio' => 'required|date_format:Y-m-d H:i:s',
                'data_ora_fine' => 'required|date_format:Y-m-d H:i:s'
            ];
            if(isset($request->tipo_lavoro) && $request->tipo_lavoro == 2) {
                $rules['id_intervento'] = 'required|exists:interventi,id_intervento';
            }
            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => [
                        'error_code' => 400,
                        'messages' => $validator->errors()
                    ]
                ], 400);
            }
            $tehnician = CoreUsers::where('app_token', $request->header('userToken'))->first();
            $data = [
                'id_tecnico' => $tehnician->id_user,
                'tipo' => $tehnician->user_type,
                'tipo_lavoro' => $request->tipo_lavoro,
                'data_ora_inizio' => $request->data_ora_inizio ? $request->data_ora_inizio : null,
                'data_ora_fine' => $request->data_ora_fine ? $request->data_ora_fine : null,
                'note' => $request->note,
            ];
            if($request->tipo_lavoro == 2) {
                $data['id_intervento'] = $request->id_intervento;
            }
            InternalWork::create($data);
            return response()->json([
                'status' => 'ok',
                'data' => [
                    'message' => 'Record created'
                ]
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function sendInternalWorks(Request $request) {
        try {
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
            $works = InternalWork::where(function($query) use($request) {
                return $query->where('data_ora_inizio', 'like', $request->date . '%')->orWhere('data_ora_fine', 'like', $request->date . '%');
            })->where('id_tecnico', $user->id_user)->with(['operation' => function($query) {
                return $query->with('client');
            }])->get();
            $response = [];
            foreach($works as $work) {
                $response[] = [
                    'id_lavori_interni' => $work->id_lavori_interni,
                    'client_name' => $work->tipo_lavoro == 2 ? $work->operation->client->clientName : '',
                    'tipo_lavoro' => $work->tipo_lavoro,
                    'data_ora_inizio' => $work->data_ora_inizio,
                    'data_ora_fine' => $work->data_ora_fine,
                    'note' => $work->note,
                ];
            }
            return response()->json([
                'status' => 'ok',
                'data' => $response
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        } 
    }

    public function sendNotFinishedOperations(Request $request) {
        try {
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();

            $operations = Operation::where('tecnico', 'like', $user->id_user . ';%')->where(function ($query) {
                return $query->where('stato', 3)->orDoesntHave('report');
            })->with('client')->get();
            $response = [];
            foreach($operations as $operation) {
                $response[] = [
                    'id_intervento' => $operation->id_intervento,
                    'descrizione_intervento' => $operation->descrizione_intervento,
                    'data' => $operation->data,
                    'cliente_denominazione' => $operation->client->client_name,
                ];
            }
            return response()->json([
                'status' => 'ok',
                'data' => $response
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }
}
