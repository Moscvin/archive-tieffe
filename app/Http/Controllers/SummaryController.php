<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\InternalWork;
use App\CoreUsers;

class SummaryController extends Controller {
    public function dailySummary() {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        return view('summary.daily_summary', $this->data);
    }

    public function monthlySummary() {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        return view('summary.monthly_summary', $this->data); 
    }

    public function getSummaryByDate(Request $request) {
        $tecnicName = CoreUsers::where('id_user', $request->tehnician)->first();
        $operations = Operation::where('tecnico','LIKE',"%{$request->tehnician}%")->whereHas('report', function($query) use($request) {
            return $query->where([['data_inizio', '<=', $request->date . ' 24:00:00'], ['data_fine', '>=', $request->date . ' 00:00:00']])->orderBy('data_inizio', 'asc');
        })->with('client')->get();
        $works = InternalWork::where([['id_tecnico', $request->tehnician], ['data_ora_inizio', '<=', $request->date . ' 24:00:00'],
            ['data_ora_fine', '>=', $request->date . ' 00:00:00']])->with(['operation' => function($query) {
                return $query->with('client');
        }])->orderBy('data_ora_inizio', 'asc')->get();
        $response = [];
        $totalHours = count($operations) + count($works);
        foreach($operations as $operation) {
            if(date('Y-m-d', strtotime($operation->report->data_inizio)) == date('Y-m-d', strtotime($operation->report->data_fine))) {
                $totalHours += strtotime($operation->report->data_fine) - strtotime($operation->report->data_inizio);
                $response[] = [
                    'date_start' => $operation->report->data_inizio,
                    'date_end' => $operation->report->data_fine,
                    'description' => "Intervento (" . $operation->client->client_name . "-" .  $operation->descrizione_intervento . ")",
                    'id_intervento' => $operation->id_intervento,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '' 
                ];
            } elseif(date('Y-m-d', strtotime($operation->report->data_inizio)) == $request->date) {
                $totalHours += strtotime($request->date . ' 17:00:00') - strtotime($operation->report->data_inizio);
                $response[] = [
                    'date_start' => $operation->report->data_inizio,
                    'date_end' => $request->date . ' 17:00:00',
                    'description' => "Intervento (" . $operation->client->client_name . "-" .  $operation->descrizione_intervento .")",
                    'id_intervento' => $operation->id_intervento,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '' 
                ];
            } elseif(date('Y-m-d', strtotime($operation->report->data_fine)) == $request->date) {
                $totalHours += strtotime($operation->report->data_fine) - strtotime($request->date . ' 08:00:00');
                $response[] = [
                    'date_start' => $request->date . ' 08:00:00',
                    'date_end' => $operation->report->data_fine,
                    'description' => "Intervento (" . $operation->client->client_name . "-" .  $operation->descrizione_intervento .")",
                    'id_intervento' => $operation->id_intervento,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '' 
                ];
            } elseif(date('N', strtotime($request->date . '08:00:00')) < 6) {
                $totalHours += strtotime($request->date . ' 17:00:00') - strtotime($request->date . ' 08:00:00');
                $response[] = [
                    'date_start' => $request->date . ' 08:00:00',
                    'date_end' => $request->date . ' 17:00:00',
                    'description' => "Intervento (" . $operation->client->client_name . "-" .  $operation->descrizione_intervento .")",
                    'id_intervento' => $operation->id_intervento,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '' 
                ];
            }
        }
        foreach($works as $work) {
            if($work->tipo_lavoro == 1) {
                $description = 'Lavoro interno' . ' ' . $work->note;
            } elseif($work->tipo_lavoro == 2) {
                $description = "Lavoro in officina (" . $work->operation->client->client_name . ")";
            } else {
                $description = 'Lavoro nedeterminato';
            }
            if(date('Y-m-d', strtotime($work->data_ora_inizio)) == date('Y-m-d', strtotime($work->data_ora_fine))) {
                $totalHours += strtotime($work->data_ora_fine) - strtotime($work->data_ora_inizio);
                $response[] = [
                    'date_start' => $work->data_ora_inizio,
                    'date_end' => $work->data_ora_fine,
                    'description' => $description,
                    'id_lavori_interne' => $work->id_lavori_interni,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '',
                    'tipo_lavoro' => $work->tipo_lavoro
                ];
            } elseif(date('Y-m-d', strtotime($work->data_ora_inizio)) == $request->date) {
                $totalHours += strtotime($request->date . ' 17:00:00') - strtotime($work->data_ora_inizio);
                $response[] = [
                    'date_start' => $work->data_ora_inizio,
                    'date_end' => $request->date . ' 17:00:00',
                    'description' => $description,
                    'id_lavori_interne' => $work->id_lavori_interni,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '',
                    'tipo_lavoro' => $work->tipo_lavoro
                ];
            } elseif(date('Y-m-d', strtotime($work->data_ora_fine)) == $request->date) {
                $totalHours += strtotime($work->data_ora_fine) - strtotime($request->date . ' 08:00:00');
                $response[] = [
                    'date_start' => $request->date . ' 08:00:00',
                    'date_end' => $work->data_ora_fine,
                    'description' => $description,
                    'id_lavori_interne' => $work->id_lavori_interni,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '',
                    'tipo_lavoro' => $work->tipo_lavoro
                ];
            } elseif(date('N', strtotime($request->date . '08:00:00')) < 6) {
                $totalHours += strtotime($request->date . ' 17:00:00') - strtotime($request->date . ' 08:00:00');
                $response[] = [
                    'date_start' => $request->date . ' 08:00:00',
                    'date_end' => $request->date . ' 17:00:00',
                    'description' => $description,
                    'id_lavori_interne' => $work->id_lavori_interni,
                    'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : '',
                    'tipo_lavoro' => $work->tipo_lavoro
                ];
            }
        }
        
        usort($response, array($this,"sortFunction"));
        return response()->json([
            'operations' => $response,
            'totalHours' => $totalHours,
            'tech' => $request->tehnician,
            'date' => $request->date,
            'tecnicName' => $tecnicName ? $tecnicName->name . ' ' . $tecnicName->family_name : ''
        ], 200);
    }
    function sortFunction( $a, $b ) {
        return strtotime($a["date_start"]) - strtotime($b["date_start"]);
    }
    public function getSummaryByMonth(Request $request) {
        $operations = Operation::where('tipo', $request->type)->whereHas('report', function($query) use($request) {
            return $query->where('data_inizio', 'like', $request->date . '%')->orWhere('data_fine', 'like', $request->date . '%')->orderBy('data_inizio', 'asc');
        })->with('client')->get();
        $works = InternalWork::where([['tipo', $request->type], ['data_ora_inizio', 'like', $request->date . '%']])
            ->orWhere([['tipo', $request->type], ['data_ora_fine', 'like', $request->date . '%']])->with(['tehnician', 'operation' => function($query) {
                return $query->with('client');
        }])->orderBy('data_ora_inizio', 'asc')->get();
        $operationArray = $this->getArrayOfOperations($operations, $request->date);
        $internalArray = $this->getArrayOfInternalWorks($works, $request->date);
        $response = array_merge($operationArray['array'], $internalArray['array']);
        usort($response, array($this, "sortByDate"));
        $response = $this->groupRecordsByDate($response);
        
        return response()->json([
            'operations' => $response,
            'totalHours' => $operationArray['hours'] + $internalArray['hours']
        ], 200);
    }

    public function searchTehnician(Request $request) {
        $users = CoreUsers::where('id_group', 9)->orWhere('id_group', 10)->get();
        $response = [];
        foreach($users as $user) {
            $response[] = [
                'id' => $user->id_user,
                'name' => $user->full_name
            ];
        }
        return response()->json($response, 200);
    }

    private function getTehnicianName($string) {
        $id = preg_split('/;/', $string);
        $id = $id[0];
        $user = CoreUsers::where('id_user', $id)->first();
        return $user->family_name . ' ' . $user->name;
    }

    private function getArrayOfOperations($operations, $requestMonth) {
        $response = [];
        $totalHours = 0;
        foreach($operations as $operation) {
            $date = $operation->report->data_inizio;
            $end = $operation->report->data_fine;
            $loop = true;
            while($loop) {
                if(date('Y-m', strtotime($date)) == $requestMonth) {
                    if(date('Y-m-d', strtotime($date)) <= date('Y-m-d', strtotime($end))) {
                        if(date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($end))) {
                            if(date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($operation->report->data_inizio))) {
                                $hours = strtotime($end) - strtotime($operation->report->data_inizio);
                            } else {
                                $hours = strtotime($end) - strtotime(date('Y-m-d', strtotime($date)) . ' 08:00:00');
                            }
                        } elseif(date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($operation->report->data_inizio))) {
                            $hours = strtotime(date('Y-m-d', strtotime($operation->report->data_inizio)) . ' 17:00:00') - strtotime($operation->report->data_inizio);
                        } else {
                            $hours = strtotime(date('Y-m-d', strtotime($date)) . ' 17:00:00') - strtotime(date('Y-m-d', strtotime($date)) . ' 08:00:00');
                        }
                        $date = new \DateTime($date);
                        $date->add(new \DateInterval('P1D'));
                        $date = $date->format('Y-m-d H:i:s');
                        $totalHours += $hours;
                        $response[] = [
                            'date' => $operation->report->data_inizio,
                            'tehnician' => $this->getTehnicianName($operation->tecnico),
                            'hours' => $hours,
                            'id_user' => preg_split('/;/', $operation->tecnico)[0]
                        ];
                    } else {
                        $loop = false;
                    }

                } elseif(date('Y-m', strtotime($date)) < $requestMonth) {
                    $date = new \DateTime($date);
                    $date->add(new \DateInterval('P1D'));
                    $date = $date->format('Y-m-d H:i:s');
                } else {
                    $loop = false;
                }
            }
        }
        return [
            'array' => $response,
            'hours' => $totalHours
        ];
    }

    private function getArrayOfInternalWorks($operations, $requestMonth) {
        $response = [];
        $totalHours = 0;
        foreach($operations as $operation) {
            $date = $operation->data_ora_inizio;
            $end = $operation->data_ora_fine;
            $loop = true;
            while($loop) {
                if(date('Y-m', strtotime($date)) == $requestMonth) {
                    if(date('Y-m-d', strtotime($date)) <= date('Y-m-d', strtotime($end))) {
                        if(date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($end))) {
                            if(date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($operation->data_ora_inizio))) {
                                $hours = strtotime($end) - strtotime($operation->data_ora_inizio);
                            } else {
                                $hours = strtotime($end) - strtotime(date('Y-m-d', strtotime($date)) . ' 08:00:00');
                            }
                        } elseif(date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($operation->data_ora_inizio))) {
                            $hours = strtotime(date('Y-m-d', strtotime($operation->data_ora_inizio)) . ' 17:00:00') - strtotime($operation->data_ora_inizio);
                        } else {
                            $hours = strtotime(date('Y-m-d', strtotime($date)) . ' 17:00:00') - strtotime(date('Y-m-d', strtotime($date)) . ' 08:00:00');
                        }
                        $totalHours += $hours;
                        $response[] = [
                            'date' => $date,
                            'tehnician' => $operation->tehnician->family_name . ' ' .$operation->tehnician->name,
                            'hours' => $hours,
                            'id_user' => $operation->tehnician->id_user
                        ];
                        $date = new \DateTime($date);
                        $date->add(new \DateInterval('P1D'));
                        $date = $date->format('Y-m-d H:i:s');
                    } else {
                        $loop = false;
                    }

                } elseif(date('Y-m', strtotime($date)) < $requestMonth) {
                    $date = new \DateTime($date);
                    $date->add(new \DateInterval('P1D'));
                    $date = $date->format('Y-m-d H:i:s');
                } else {
                    $loop = false;
                }
            }
        }
        return [
            'array' => $response,
            'hours' => $totalHours
        ];
    }

    private function sortByDate($a, $b) {
        return strcmp($a['date'], $b['date']);
    }

    private function groupRecordsByDate($array) {
        $newArray = [];
        foreach($array as $item) {
            $key = date('Y-m-d', strtotime($item['date'])) . '_' . $item['id_user'];
            if(array_key_exists($key, $newArray)) {
                $newArray[$key]['hours'] += $item['hours'];
            } else {
                $newArray[$key]['date'] = $item['date'];
                $newArray[$key]['tehnician'] = $item['tehnician'];
                $newArray[$key]['hours'] = $item['hours'];
            }
        }
        return $newArray;
    }

    public function delet5eWork(Request $request) {
        try {
            if($request->idInternalWork) {
                InternalWork::where('id_lavori_interni', $request->idInternalWork)->delete();
            }
            return response()->json([
                'success' => 1
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'error' => 1,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}