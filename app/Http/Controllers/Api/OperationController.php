<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoreUsers;
use App\Models\Operation\Operation;
use App\Models\Report;
use App\Models\Clienti;
use DB;

class OperationController extends Controller
{
    public function getOperationData(Request $request) {
        $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
        $operation = Operation::where('id_intervento', $request->id_intervento)->with(['headquarter.client', 'machineries'])->first();
        if(in_array($user->id_user, $operation->techniciansArray)) {
            return response()->json([
                'status' => 'ok',
                'data' => $operation
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 400,
                    'message' => 'Access denied to choosen data'
                ]
            ], 400);
        }
    }

    public function createNewOperation(Request $request) {
        if(Clienti::where([['id', $request->id_client], ['cliente_visibile', 1]])->first()) {
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
            $type = $user->id_group == 9 ? 2 : ($user->id_group == 10 ? 1 : 0);
            $data = [
                'id_clienti' => $request->id_client,
                'descrizione_intervento' => substr($request->descrizione_intervento, 0, 255),
                'data' => $request->data,
                'stato' => 1,
                'tecnico' => $request->tecnici,
                'note' => NULL,
                'tipo' => $type,
                'fatturazione' => 0,
                'conto_di' => $request->conto_di,
                'pronto_intervento' => 0,
                'incasso' => $request->incasso,
            ];

            Operation::create($data);

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'success' => 1
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 2,
                    'message' => 'Wrong client id'
                ]
            ], 200);
        }
    }

    public function getUrgentOperationData(Request $request) {
        $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
        $type = $user->id_group == 9 ? 2 : ($user->id_group == 10 ? 1 : 0);
        $operations = Operation::where([['pronto_intervento', 1], ['stato', 0], ['tipo', $type]])->with(['client'])->get();
        $data = [];

        foreach($operations as $operation) {
            $data[] = [
                'id_intervento' => $operation->id_intervento,
                'descrizione_intervento' => $operation->descrizione_intervento,
                'cliente' => $this->getClientData($operation->client),
                'note' => $operation->note,
                'conto_di' => $operation->conto_di == 1 ? 'Cliente' : ($operation->conto_di == 2 ? 'Interporto' :
                    ($operation->conto_di == 3 ? 'Consorzio' : 'Non clarificato')),
                'updated_at' => date('Y-m-d H:i:s', strtotime($operation->updated_at)),
                'created_at' => date('Y-m-d H:i:s', strtotime($operation->created_at)),
                'stato' => $operation->stato,
                'tipo' => $operation->tipo,
                'incasso' => $operation->incasso,
            ];
        }
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function assumeUrgentOperation(Request $request) {
        $data = [
            'data' => $request->data,
            'tecnico' => $request->tecnici,
            'stato' => 1
        ];
        Operation::where('id_intervento', $request->id_intervento)->update($data);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'success' => 1
            ]
        ], 200);
    }

    public function updateOperationTehnicians(Request $request) {
        try {
            Operation::where('id_intervento', $request->id_intervento)->update([
                'tecnico' => $request->tecnici,
            ]);

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'success' => 1
                ]
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 999,
                    'message' => $e->getMessage()
                ]
            ], 400);
        }

    }

    private function getClientData($client) {
        $name = $client->azienda == 1 ? $client->ragione_sociale : $client->cognome . ' ' . $client->nome;
        $address = $client->indirizzo_sl ? $client->indirizzo_sl : '';
        $address .= $client->numero_civico_sl ? ', '.$client->numero_civico_sl . ' - ' : '';
        $address .= $client->cap_sl ? $client->cap_sl : '';
        $address .= $client->comune_sl ? $client->comune_sl : '';
        $address .= $client->provincia_sl ? ' (' . $client->provincia_sl .')' : '';
        $phone = [];
        if($client->telefono_1) {
            array_push($phone, $client->telefono_1);
        }
        if($client->telefono_2) {
            array_push($phone, $client->telefono_2);
        }
        return (object)[
            'denominazione' => $name,
            'address' => $address,
            'phone' => $phone
        ];
    }

    private function getTehniciansData($array) {
        if(count($array)) {
            $tehnicians = CoreUsers::whereIn('id_user', $array)->orderBy(DB::raw('FIELD(`id_user`, '.implode(',', $array).')'))->get();
            $response = [];
            foreach($tehnicians as $tehnician) {
                $response[] = $tehnician->family_name . ' ' . $tehnician->name;
            }
            return $response;
        }
        return [];
    }

    private function getReportId($id) {
        $report = Report::where('id_intervento', $id)->first();
        return $report ? $report->id_rapporto : 0;
    }
}
