<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Report;
use App\Models\ReportEquipment;
use App\Models\ReportPhoto;
use App\Models\InternalWork;
use App\Models\Operation;
use App\CoreUsers;

use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function getReport(Request $request) {
        try {
            $dataOperation = [
                'stato' => isset($request->stato) && $request->stato ? $request->stato : 1,
            ];
            Operation::where('id_intervento', $request->id_intervento)->update($dataOperation);
            $numberOfReport = $this->getNumberOfReport($request);


            $dataReport = [
                'id_intervento' => isset($request->id_intervento) && $request->id_intervento ? $request->id_intervento : null,
                'id_mezzo' => isset($request->id_mezzo) && $request->id_mezzo ? $request->id_mezzo : null,
                'data_inizio' => isset($request->data_ora_inizio) && $request->data_ora_inizio ? $request->data_ora_inizio : null,
                'data_fine' => isset($request->data_ora_fine) && $request->data_ora_fine ? $request->data_ora_fine : null,
                'difetto' => isset($request->difetto) && $request->difetto ? $request->difetto : '',
                'descrizione_intervento' => isset($request->descrizione_intervento) && $request->descrizione_intervento ? $request->descrizione_intervento : '',
                'altri_note' => isset($request->altri_note) && $request->altri_note ? $request->altri_note : '',
                'altri_ore' => $this->getHours($request),
                'data_invio' => isset($request->data_ora_invio) && $request->data_ora_invio ? $request->data_ora_invio : null,
                'fatturato' => 0,
                'dafatturare' => $request->dafatturare,
                'nr_rapporto' => $numberOfReport->nr,
                'tipo_rapporto' => $numberOfReport->type,
                'firmatario' => $request->firmatario,
                'incasso_pos' => $request->incasso_pos,
                'incasso_in_contanti' => $request->incasso_in_contanti,
                'incasso_con_assegno' => $request->incasso_con_assegno,
                'carrello_cingolato' => $request->carrello_cingolato,
                'altra_norma_text' => $request->altra_norma_text,
                'raccomandazioni' => $request->raccomandazioni,
                'prescrizioni' => $request->prescrizioni,
                'UNI_7129' => $request->UNI_7129,
                'UNI_10683' => $request->UNI_10683,
                'altra_norma_value' => $request->altra_norma_value,
            ];

            $lastReport = Report::create($dataReport);
            $idReport = $lastReport->id_rapporto;

            if($request->rapporti_materiali) {
                foreach($request->rapporti_materiali as $equipment) {
                    $equipment = (object)$equipment;
                    $dataReportEquipment = [
                        'id_rapporto' => $idReport,
                        'id_materiali' => $equipment->id_materiali,
                        'quantita' => $equipment->quantita
                    ];
                    ReportEquipment::create($dataReportEquipment);
                }
            }

            return response()->json([
                'status' => 'ok',
                'data' => [
                    'id_rapporto' => $idReport
                ]
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => '500',
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function getReportsPhoto(Request $request) {
        try {
            $photoNew = [];
            $object = [];
            $operation = Operation::Select('*')->where(['id_intervento'=>$request->id_intervento])->first();
            if(isset($request->firma) && $request->firma) {
                Storage::delete(Storage::files('signatures/' . $request->id_intervento));
                $firma = Storage::putFile('signatures/' . $request->id_intervento, $request->firma);
                $dataReport = [
                    'firma' => $firma,
                ];
                Report::where('id_rapporto', $request->id_rapporto)->update($dataReport);
            }
            else{
                if(isset($operation->stato) && $operation->stato!='3'){
                        return response()->json([
                            'status' => 'Error',
                            'data' => [
                                'message' => 'Firma is required, with excepting stato "non completato" '
                            ]
                        ], 400);
                }
            }



            if(isset($request->rapporti_foto) && count($request->rapporti_foto)) {
                foreach($request->rapporti_foto as $photo) {
                    $object[] = $photo;
                    $reportPhoto = Storage::putFile('photos/' . $request->id_intervento, $photo);
                    if($reportPhoto) {
                        $dataReportPhoto = [
                            'id_rapporto' => $request->id_rapporto,
                            'filename' => $reportPhoto
                        ];
                        $photoNew[] = ReportPhoto::create($dataReportPhoto);
                    } else {
                        return response()->json([
                            'status' => 'ok',
                            'data' => [
                                'message' => 'One photo is not detected'
                            ]
                        ], 200);
                    }

                }
            } else {
                return response()->json([
                    'status' => 'ok',
                    'data' => [
                        'message' => 'Photos are not detected'
                    ]
                ], 200);
            }
            return response()->json([
                'status' => 'ok',
                'data' => [
                    'message' => 'Uploaded with success',
                    'photos' => count($request->rapporti_foto),
                    'models' => $request->rapporti_foto,
                ]
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => '500',
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function sendReportsByDate(Request $request) {
        try {
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();

            $date = isset($request->date) && $request->date ? $request->date : date('Y-m-d');
            $reports = Report::where('data_inizio', 'like', $date . '%')->whereHas('operation', function($query) use($user) {
                return $query->where('tecnico', 'like', '%' . $user->id_user . '%');
            })->with(['operation' => function($query) {
                return $query->with('client');
            }])->get();
            $response = [];
            if($reports){
            foreach($reports as $report) {
                if(in_array($user->id_user, preg_split('/;/', $report->operation->tecnico))) {
                    $clientName = '';
                    if($report->operation && $report->operation->client) {
                        $clientName = $report->operation->client->azienda ? $report->operation->client->ragione_sociale :
                            $report->operation->client->cognome . ' ' . $report->operation->client->nome;
                    }
                    if($report->data_inizio && $report->data_fine){
                    $response[] = [
                        'id_rapporto' => $report->id_rapporto,
                        'cliente' => $clientName,
                        'descrizione_intervento' => $report->descrizione_intervento,
                        'data_inizio' => $report->data_inizio,
                        'data_fine' => $report->data_fine,
                    ];
                }
                }
            }
        }
            $works = InternalWork::where([['data_ora_inizio', 'like', $request->date . '%'], ['id_tecnico', $user->id_user]])->with(['operation' => function($query) {
                return $query->with('client');
            }])->get();
            $response2 = [];
            if($works){
            foreach($works as $work) {
                $response2[] = [
                    'id_lavori_interni' => $work->id_lavori_interni,
                    'client_name' => $work->operation && $work->tipo_lavoro == 2 ? $work->operation->client->client_name : '',
                    'tipo_lavoro' => $work->tipo_lavoro,
                    'data_ora_inizio' => $work->data_ora_inizio,
                    'data_ora_fine' => $work->data_ora_fine,
                    'note' => $work->note,
                ];
            }
        }
            return response()->json([
                'status' => 'ok',
                'riepilogo' => $response,
                'lavori_interni' => $response2
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => '500',
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function getNumberOfReport($request) {
        $operation = Operation::where('id_intervento', $request->id_intervento)->first();
        $type = $operation->conto_di == 3 ? 1 : ($operation->tipo == 1 ? 2 : ($operation->tipo == 2 ? 3 : 4));
        if($request->id_rapporto) {
            $report = Report::where('id_rapporto', $request->id_rapporto)->first();
        } else {
            $report = Report::where('tipo_rapporto', $type)->orderBy('nr_rapporto', 'desc')->first();
        }

        return (object)[
            'nr' => $report ? ++$report->nr_rapporto : 1,
            'type' => $type
        ];
    }

    private function getHours($request) {
        $currentOperation = Operation::where('id_intervento', $request->id_intervento)->first();
        $oldOperation = Operation::where('id_intervento', $currentOperation->id_old_operation)->whereHas('report')->first();
        $hours = isset($request->altri_ore) && $request->altri_ore ? $request->altri_ore : 0;
        if($oldOperation) {
            $hours += ($oldOperation->report ? $oldOperation->report->altri_ore : 0);
        }
        return $hours;
    }
}
