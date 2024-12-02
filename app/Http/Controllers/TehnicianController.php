<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report\Report;
use App\Models\InternalWork;
use App\Models\Operation;
use App\CoreUsers;

class TehnicianController extends Controller {
    public function dailyWork() {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        return view('daily_work', $this->data);
    }

    public function getWorksByDate($date = null, Request $request) {
        $user = Auth::user();
        $date = isset($request->date) && $request->date ? $request->date : date('Y-m-d');
        $reports = Report::where([['data_inizio', '<=', $date . ' 24:00:00'], ['data_fine', '>=', $date . ' 00:00:00']])->whereHas('operation', function($query) use($user) {
            return $query->where('tecnico', 'like', '%' . $user->id_user . '%');
        })->with(['operation' => function($query) {
            return $query->with('client');
        }])->get();
        $response = [];
        $key = 0;
        if($reports) {
            foreach($reports as $report) {
                $start = date('Y-m-d', strtotime($report->data_inizio)) == $date ? date('Y-m-d H:i', strtotime($report->data_inizio)) : $date . ' 08:00:00';
                $end = date('Y-m-d', strtotime($report->data_fine)) == $date ? date('Y-m-d H:i', strtotime($report->data_fine)) : $date . ' 17:00:00';
                if(in_array($user->id_user, preg_split('/;/', $report->operation->tecnico))) {
                    if($report->data_inizio && $report->data_fine) {
                        $response[] = [
                            'id' => $report->id_rapporto,
                            'title' => "<h3>Intervento (" . $report->operation->client->client_name . "-" .  $report->operation->descrizione_intervento .")</h3>",
                            'content' => $report->descrizione_intervento,
                            'type' => 1,
                            'tipeOfWork' => 0,
                            'start' => $start,
                            'end' => $end,
                            'class' => 'event event-blue',
                            'background' => true,
                        ];
                    }
                }
            }
        }
        $works = InternalWork::where([['data_ora_inizio', '<=', $date . ' 24:00:00'], ['data_ora_fine', '>=', $date . ' 00:00:00'],
            ['id_tecnico', $user->id_user]])->with(['operation' => function($query) {
            return $query->with('client');
        }])->get();
        $response2 = [];
        if($works){
            foreach($works as $work) {
                $start = date('Y-m-d', strtotime($work->data_ora_inizio)) == $date ? date('Y-m-d H:i', strtotime($work->data_ora_inizio)) : $date . ' 08:00:00';
                $end = date('Y-m-d', strtotime($work->data_ora_fine)) == $date ? date('Y-m-d H:i', strtotime($work->data_ora_fine)) : $date . ' 17:00:00';
                $response2[] = [
                    'id' => $work->id_lavori_interni,
                    'title' => $work->operation && $work->tipo_lavoro == 2 ? "<h3>Lavoro in officina (" . $work->operation->client->client_name . ")</h3>" :
                        '<h3>Lavoro interno' . ' ' . $work->note . '</h3>',
                    'content' => $work->note,
                    'type' => 2,
                    'tipeOfWork' => $work->tipo_lavoro,
                    'start' => $start,
                    'end' => $end,
                    'class' => $work->tipo_lavoro == 1 ? 'event event-grey' : 'event event-orange',
                    'background' => true,
                ];
            }
        }
        return response()->json([
            'status' => 'ok',
            'items' => array_merge($response, $response2)
        ], 200);
    }

    public function sendNotFinishedOperations(Request $request) {
        try {
            $user = Auth::user();

            $operations = Operation::where('tecnico', 'like', $user->id_user . ';%')->where('stato', 3)->orDoesntHave('report')->with('client')->get();
            $response = [];
            foreach($operations as $operation) {
                $response[] = [
                    'id' => $operation->id_intervento,
                    'description' => $operation->descrizione_intervento,
                    'data' => $operation->data,
                    'clientName' => $operation->client->client_name,
                ];
            }
            return response()->json([
                'status' => 'ok',
                'items' => $response
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

    public function setInternalWork(Request $request) {
        try {
            $request = (object)$request;
            $work = (object)$request->work;
            $typeOfWork = $work->selectedWork ? 2 : 1;
            $dateFrom = date('Y-m-d H:i:s', strtotime($work->date_from . ' ' . $work->hour_from));
            $dateTo = date('Y-m-d H:i:s', strtotime($work->date_to . ' ' . $work->hour_to));
            if(isset($work->id)) {
                if(in_array(Auth::user()->id_group, [1, 8])) {
                    $tehnician = CoreUsers::where('id_user', $work->id)->first();
                } else {
                    return response()->json([
                        'status' => 'error',
                        'data' => [
                            'error_code' => 400,
                            'message' => 'Permission Denied'
                        ]
                    ], 400);
                }
            } elseif(in_array(Auth::user()->id_group, [9, 10])) {
                $tehnician = Auth::user();
            } else {
                return response()->json([
                    'status' => 'error',
                    'data' => [
                        'error_code' => 400,
                        'message' => 'Permission Denied'
                    ]
                ], 401);
            }
            
            $data = [
                'id_tecnico' => $tehnician->id_user,
                'tipo' => $tehnician->user_type,
                'tipo_lavoro' => $typeOfWork,
                'data_ora_inizio' => $dateFrom,
                'data_ora_fine' => $dateTo,
                'note' => $work->note,
            ];
            if($typeOfWork == 2) {
                $data['id_intervento'] = $work->selectedWork;
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

    public function deleteInternalWork(Request $request) {
        try {
            $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
            if(in_array('D', $chars)) {
                InternalWork::where('id_lavori_interni', $request->idInternalWork)->delete();
                return response()->json([
                    'success' => 1
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'data' => [
                        'error_code' => 400,
                        'message' => 'Permission Denied'
                    ]
                ], 500);
            }
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
