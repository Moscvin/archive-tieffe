<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InternalWork;
use App\Models\OrdersWork;
use App\Models\Operation;
use App\Models\InterventEquipmentOrders;
use App\CoreUsers;

class OrdersWorkController extends Controller {
    public function list(Request $request) {
        try {


            $tehnician = CoreUsers::where('app_token', $request->header('userToken'))->first();

            $rules = [
                'data_inizio' => 'nullable|date_format:d-m-Y',
                'data_fine' => 'nullable|date_format:d-m-Y'
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'data' => [
                        'error_code' => 400,
                        'messages' => $validator->errors()
                    ]
                ]);
            }

            $OrdersWork = new OrdersWork();
            $OrdersWork = $OrdersWork->where('tecnico', $tehnician->id_user);



            if($request->data_inizio){

                $data_inizio = date('Y-m-d', strtotime($request->data_inizio));

                $OrdersWork = $OrdersWork->whereDate('data','>=',$data_inizio);
            }
            if($request->data_fine){

                $data_fine = date('Y-m-d', strtotime($request->data_fine));

                $OrdersWork = $OrdersWork->whereDate('data','<=',$data_fine);
            }

            $OrdersWork = $OrdersWork->with(['order'])->orderBy('data','desc')->limit(30)->get();

            $commesse_lavori_list = [];

            foreach($OrdersWork as $OW){

                $commesse_lavori_list[] = [
                    'nome' => (isset($OW->order) ? $OW->order->nome : ""),
                    'data' => (isset($OW->data) ? date('d/m/Y',strtotime($OW->data)) : ""),
                    'ore_lavorate' => $this->formatHours($OW->ore_lavorate)

                ];

            }

            return response()->json([
                'commesse_lavori_list' => $commesse_lavori_list
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

    function add(Request $request){

        try {

            $tehnician = CoreUsers::where('app_token', $request->header('userToken'))->first();

            $rules = [
                'id_commessa' => 'required|numeric|integer',
                'data' => 'required|date_format:d/m/Y',
                'ore_lavorate' => 'required',
                'descrizione' => 'nullable|string'
            ];



            $validator = \Validator::make($request->all(), $rules);



            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'data' => [
                        'error_code' => 400,
                        'messages' => $validator->errors()
                    ]
                ],400);
            }

            $OrdersWork = new OrdersWork();



            $hours_request = explode(':', $request->ore_lavorate);
            $hours = floor($hours_request[0]);
            $minutes = floor( ($hours_request[1] / 60) * 10 );

            $WorkedHours = $hours. '.' .$minutes;

            $data = [
                'id_commessa' => $request->id_commessa,
                'tecnico' => $tehnician->id_user,
                'data' => date('Y-m-d', strtotime(str_replace('/','-', $request->data) ) ),
                'ore_lavorate' => $WorkedHours,
                'descrizione' => $request->descrizione ?? null
            ];


            $OrdersWork = $OrdersWork->create($data);



            return response()->json([
                'id_lavoro' => $OrdersWork->id_lavoro ?? '',
                'esito' => '1'
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

        //$OrdersWork = $OrdersWork->where('tecnico', $tehnician->id_user);

    }


    public function add_material(Request $request){

        try {

            $tehnician = CoreUsers::where('app_token', $request->header('userToken'))->first();

            $rules = [
                'id_intervento' => 'nullable|numeric|integer',
                'id_lavoro' => 'nullable|numeric|integer',
                'materiali_list' => 'required'
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'data' => [
                        'error_code' => 400,
                        'messages' => $validator->errors()
                    ]
                ]);
            }

            //$request = json_decode($request);

            $list_rules = [
                'codice' => 'nullable',
                'descrizione' => 'required',
                'quantita' => 'required'
            ];

            $OrdersWork = new OrdersWork();

            if(isset($request->id_intervento) && !empty($request->id_intervento) ){
                InterventEquipmentOrders::where('id_intervento',$request->id_intervento)->delete();
            }
            if(isset($request->id_lavoro) && !empty($request->id_lavoro) ){
                InterventEquipmentOrders::where('id_lavoro', $request->id_lavoro)->delete();
            }

            //$request->materiali_list = json_decode($request->materiali_list,true);

            if( isset($request->materiali_list) && count($request->materiali_list) > 0){

                foreach($request->materiali_list as $item){

                    $validator2 = \Validator::make($item, $list_rules);

                    if($validator2->fails()){

                        return response()->json([
                            'status' => 'error',
                            'data' => [
                                'error_code' => 400,
                                'messages' => $validator2->errors()
                            ]
                        ]);
                        
                    }
        
                    $codice = $item['codice'] ?? '';
                    $descrizione = $item['descrizione'] ?? '';
                    $quantita = $item['quantita'] ?? '';

                    $data = [
                        'id_lavoro' => $request->id_lavoro ?? 0,
                        'id_intervento' => $request->id_intervento ?? 0,
                        'codice' => $codice,
                        'descrizione' => $descrizione,
                        'quantita' => $quantita
                    ];

                    InterventEquipmentOrders::create($data);
                }


            }




/*            $hours_request = explode(':', $request->ore_lavorate);
            $hours = floor($hours_request[0]);
            $minutes = floor( ($hours_request[1] / 60) * 10 );

            $WorkedHours = $hours. '.' .$minutes;

            $data = [
                'id_commessa' => $request->id_commessa,
                'tecnico' => $tehnician->id_user,
                'data' => date('Y-m-d', strtotime(str_replace('/','-', $request->data) ) ),
                'ore_lavorate' => $WorkedHours,
                'descrizione' => $request->descrizione ?? null
            ];


            $OrdersWork = $OrdersWork->create($data);*/



            return response()->json([
                'esito' => '1'
            ], 200);


        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 500,
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
            ], 500);

/*            return response()->json([
                'esito' => $e
            ], 400);*/
        }

    }

    private function formatHours($val){

        $hours = sprintf("%02d", floor($val));
        $mins = sprintf("%02d", round(($val - $hours) * 60));

        return $hours. ':' . $mins;

    }

    /*public function sendInternalWorks(Request $request) {
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
    }*/
}
