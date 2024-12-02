<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;
use App\Models\Report\Report;
use App\Models\Location;
use App\Models\Clienti;
use App\Helpers\GeoDecoder;
use App\CoreUsers;
use Illuminate\Support\Facades\Storage;

class PlaningController extends Controller
{
    public function index(){
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        $invoicesTo = Clienti::where('committente', 1)->orderBy('ragione_sociale', 'asc')->get();
        $this->data['invoicesTo'] = $invoicesTo;
        return view('interventi.daprogrammare', $this->data);
    }

    public function getOperations(Request $request)
    {
        $operations = Operation::when($request->date, function($q) use($request) {
            $date = date("Y-m", strtotime($request->date));
            $date = $date."-31";
            $q->where('data','<=',$date)->orWhere('data', NULL);
        })->with(['lastReportCanceled', 'location', 'machineries' => function($query){
            $query->with('machinery');
        }])->get();
        $response = [];
        foreach ($operations as $key => $operation) {
            if($operation->stato == 0){
                $response[$key]['statusText'] = $operation->data >= date('Y-m-d') ? 'Da pianificare':'Scaduto - Da pianificare';

                $response[$key]['className'] = $this->getClassName($operation);
                $response[$key]['machineriesDescription'] = $operation->machineries->map(function($item, $key) {
                    return [
                        'description' => $item->machinery->descrizione ?? '',
                        'intervent_description' => $item->machinery->desc_intervento,
                        'model' => $item->machinery->modello ?? '',
                    ];
                });

                $response[$key]['id'] = $operation->id_intervento;
                $response[$key]['tipologia'] = $operation->tipologia;
                $response[$key]['ora_dalle'] = $operation->ora_dalle;
                $response[$key]['ora_alle'] = $operation->ora_alle;

                $response[$key]['cestello'] = $operation->cestello;
                $response[$key]['incasso'] = $operation->incasso;

                $response[$key]['client'] = (object)[
                    'id' => $operation->headquarter->client->id ?? '',
                    'name' => $operation->headquarter->client->ragione_sociale ?? ''
                ];
                $response[$key]['headquarter'] = $operation->id_sede;
                $response[$key]['address'] = $operation->headquarter->address ?? '';
                $response[$key]['urgent'] = $operation->urgente;
                $response[$key]['status'] = $operation->stato;
                $response[$key]['date'] = $operation->data;

                $response[$key]['technician_1'] = $operation->techniciansArray[0] ?? '';
                $response[$key]['technician_2'] = $operation->techniciansArray[1] ?? '';
                $response[$key]['technician_3'] = $operation->techniciansArray[2] ?? '';
                $response[$key]['invoiceTo'] = $operation->fatturare_a;
                $response[$key]['note'] = $operation->note;
                $response[$key]['file'] = $operation->file;
                $response[$key]['fileName'] = $operation->fileName;
                $response[$key]['path'] = $operation->file;

                $response[$key]['machineries'] = $operation->machineries->map(function($item) {
                    return [
                        'id' => $item->id_macchinario,
                        'description' => $item->desc_intervento
                    ];
                });
            }
        }

        return response()->json($response, 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $requestOperation = json_decode($request->operation);
            $technicians = '';
            for($i = 1; $i < 4; $i++) {
                if($requestOperation->{"technician_$i"}) {
                    $technicians .= ($requestOperation->{"technician_$i"} . ';');
                }
            }
            $oldOperation = Operation::where('id_intervento', $id)->first();
            if($oldOperation->id_sede != $requestOperation->headquarter) {
                $headquarter = Location::where('id_sedi', $requestOperation->headquarter)->first();
                $coors = (new GeoDecoder('Italy '.  $headquarter->address))->getCoors();
            }
            $data = [
                'tipologia' => $requestOperation->tipologia,
                'data' => $requestOperation->date,
                'ora_dalle' => $requestOperation->ora_dalle,
                'ora_alle' => $requestOperation->ora_alle,
                'cestello' => $requestOperation->cestello,
                'incasso' => $requestOperation->incasso,
                'urgente' => $requestOperation->urgent ?? 0,
                'stato' => $requestOperation->status,
                'tecnico' => $technicians,
                'fatturare_a' => $requestOperation->invoiceTo,
                'note' => $requestOperation->note,
                'id_sede' => $requestOperation->headquarter,
            ];
            if(isset($coors)) {
                $data['long'] = $coors->lng;
                $data['lat'] = $coors->lat;
            }
            if($request->file('file')) {
                $data['file'] = $request->file('file')
                        ->storeAs('/operation', $request->file('file')->getClientOriginalName());
                if ($oldOperation->file) {
                    Storage::delete($oldOperation->file);
                }
            } elseif (!($request->fileName && $request->path)) {
                $data['file'] = null;
                Storage::delete($oldOperation->file);
            }
            $operation = Operation::where('id_intervento', $id)->update($data);

            $this->updateMachineries($requestOperation->machineries, $id);
            return response()->json([
                'operation' => $operation
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }


    public function create(Request $request)
    {

        //dd($request->all());
        try {
            $requestOperation = json_decode($request->operation);
            $technicians = '';
            for($i = 1; $i < 4; $i++) {
                if($requestOperation->{"technician_$i"}) {
                    $technicians .= ($requestOperation->{"technician_$i"} . ';');
                }
            }
            $headquarter = Location::where('id_sedi', $requestOperation->headquarter)->first();
            $coors = (new GeoDecoder('Italy '.  $headquarter->address))->getCoors();

            $data = [
                'tipologia' => $requestOperation->tipologia,
                'data' => $requestOperation->date,
                'ora_dalle' => $requestOperation->ora_dalle,
                'ora_alle' => $requestOperation->ora_alle,
                'cestello' => $requestOperation->cestello,
                'incasso' => $requestOperation->incasso,
                'urgente' => $requestOperation->urgent ?? 0,
                'stato' => $requestOperation->status,
                'tecnico' => $technicians,
                'fatturare_a' => $requestOperation->invoiceTo,
                'note' => $requestOperation->note,
                'id_sede' => $requestOperation->headquarter,
            ];
            if(isset($coors)) {
                $data['long'] = $coors->lng;
                $data['lat'] = $coors->lat;
            }
            if($request->file('file')) {
                $data['file'] = $request->file('file')
                        ->storeAs('/operation', $request->file('file')->getClientOriginalName());
            }
            $operation = Operation::create($data);

            $this->updateMachineries($requestOperation->machineries, $operation->id_intervento);

            return response()->json([
                'operation' => $operation
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function replan(Request $request, $id)
    {
        try {
            $oldOperation = Operation::where('id_intervento', $id)->with('machineries')->first();

            $formattedDate = date('d/m/Y', strtotime($request->date));
            $data = [
                'tipologia' => $oldOperation->tipologia ?? null,
                'data' => $request->date,
                'ora_dalle' => $request->ora_dalle,
                'ora_alle' => $request->ora_alle,
                'cestello' => $oldOperation->cestello,
                'incasso' => $oldOperation->incasso ?? 0,
                'urgente' => $oldOperation->urgente,
                'stato' => $request->status,
                'tecnico' => $oldOperation->tecnico,
                'note' => $oldOperation->note . " \r\n \r\n ripianificato da {$formattedDate}",
                'id_sede' => $oldOperation->id_sede,
                'long' => $oldOperation->long,
                'lat' => $oldOperation->lat,
                'fatturare_a' => $oldOperation->fatturare_a,
                'old_id_intervento' => $id
            ];
            $operation = Operation::create($data);
            $oldOperation->machineries->each(function($item) use ($operation) {
                OperationMachinery::create([
                    'id_intervento' => $operation->id_intervento,
                    'id_macchinario' => $item->id_macchinario,
                    'desc_intervento' => $item->desc_intervento,
                ]);
            });
            $oldOperation->update([
                'stato' => 5
            ]);
            return response()->json([
                'operation' => $operation
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function getCalendarData(Request $request)
    {
        try {
            $status = $request->status ?? 9;

            $operations = Operation::when($request->date_start, function($q) use($request) {
                $q->where('data','>=',$request->date_start);
            })->when($request->date_end, function($q) use($request) {
                $q->where('data','<=',$request->date_end);
            })->with(['machineries.machinery', 'headquarter.client','lastReportCanceled'])
            ->when($status != 9, function($query) use($status) {
                if($status == 1) {
                    return $query->where([['stato', $status], ['data', '>=', date('Y-m-d H:i:s', time())]]);
                } elseif($status == 3) {
                    return $query->whereIn('stato', [1, 3])->where('data', '<=', date('Y-m-d H:i:s', time()));
                } else {
                    return $query->where('stato', $status);
                }
            })->orderBy('data')->get();

            $response = [];

            foreach($operations as $key => $operation) {
                $response[$key] = [
                    'id' => $operation->id_intervento,
                    'tipologia' => $operation->tipologia,
                    'client' => (object)[
                        'id' => $operation->headquarter->client->id ?? '',
                        'name' => $operation->headquarter->client->ragione_sociale ?? ''
                    ],
                    'headquarter' => $operation->id_sede,
                    'machineries' => $operation->machineries->map(function($item) {
                        return [
                            'id' => $item->id_macchinario,
                            'description' => $item->desc_intervento
                        ];
                    }),
                    'urgent' => $operation->urgente,
                    'status' => $operation->stato,
                    'date' => $operation->data,
                    'ora_dalle' => $operation->ora_dalle,
                    'ora_alle' => $operation->ora_alle,
                    'cestello' => $operation->cestello,
                    'incasso' => $operation->incasso,
                    'technician_1' => $operation->techniciansArray[0] ?? '',
                    'technician_2' => $operation->techniciansArray[1] ?? '',
                    'technician_3' => $operation->techniciansArray[2] ?? '',
                    'bodyStatus' => $operation->a_corpo,
                    'invoiceTo' => $operation->fatturare_a,
                    'note' => $operation->note,
                    'file' => $operation->file,
                    'fileName' => $operation->fileName,
                    'path' => $operation->file,
                    'start' => $operation->data,
                    'title' => $operation->headquarter->client->ragione_sociale ?? ''
                ];

                $report = Report::where('id_intervento', $operation->id_intervento)->first();
                if($report)
                  switch($operation->stato) {
                      case 2: {
                          $response[$key]['reportLink'] = '/downloadReport/' . $report->id_rapporto;
                          break;
                      }
                      case 3: {
                          $response[$key]['reportLink'] = '/downloadReport/' . $report->id_rapporto;
                          break;
                      }
                      case 5: {
                          $response[$key]['reportLink'] = '/downloadReport/' . $report->id_rapporto;
                          break;
                      }
                  }

                $response[$key]['class'] = "full-color-2 " . $this->getClassName($operation);
                $response[$key]['className'] = "full-color " . $this->getClassName($operation);
            }

            return response()->json($response, 200);
        } catch(\Exception $e) {
            return response($e->getMessage());
        }
    }

    public function getHoursCalendarData(Request $request)
    {
        try {
            $status = $request->status ?? 9;
            $operations = Operation::when($request->date_start, function($q) use($request) {
                $q->where('data','>=',$request->date_start);
            })->when($request->date_end, function($q) use($request) {
                $q->where('data','<=',$request->date_end);
            })->with(['machineries.machinery', 'headquarter.client','lastReportCanceled'])
            ->when($status != 9, function($query) use($status) {
                if($status == 1) {
                    return $query->where([['stato', $status], ['data', '>=', date('Y-m-d H:i:s', time())]]);
                } elseif($status == 3) {
                    return $query->whereIn('stato', [1, 3])->where('data', '<=', date('Y-m-d H:i:s', time()));
                } else {
                    return $query->where('stato', $status);
                }
            })->orderBy('data')->get();

            $technicians = CoreUsers::where([['isactive', 1], ['id_group', 9]])->pluck('id_user');
            $response = [];
            $index = 0;
            foreach($operations as $operation) {
                $event = [
                    'id' => $operation->id_intervento,
                    'tipologia' => $operation->tipologia,
                    'client' => (object)[
                        'id' => $operation->headquarter->client->id ?? '',
                        'name' => $operation->headquarter->client->ragione_sociale ?? '',
                        'address' => $operation->headquarter->address ?? '',
                        'phone' => $operation->headquarter->phones
                    ],
                    'headquarter' => $operation->id_sede,
                    'machineries' => $operation->machineries->map(function($item) {
                        return [
                            'id' => $item->id_macchinario,
                            'description' => $item->desc_intervento
                        ];
                    }),
                    'urgent' => $operation->urgente,
                    'status' => $operation->stato,
                    'date' => $operation->data,
                    'ora_dalle' => $operation->ora_dalle,
                    'ora_alle' => $operation->ora_alle,
                    'cestello' => $operation->cestello,
                    'incasso' => $operation->incasso,
                    'technician_1' => $operation->techniciansArray[0] ?? '',
                    'technician_2' => $operation->techniciansArray[1] ?? '',
                    'technician_3' => $operation->techniciansArray[2] ?? '',
                    'bodyStatus' => $operation->a_corpo,
                    'invoiceTo' => $operation->fatturare_a,
                    'note' => $operation->note,
                    'file' => $operation->file,
                    'fileName' => $operation->fileName,
                    'path' => $operation->file,
                    'start' => isset($operation->ora_dalle) && isset($operation->data) ? date('Y-m-d H:i', strtotime($operation->data . ' ' . $operation->ora_dalle ) ) : '',
                    'end' => isset($operation->ora_alle) && isset($operation->data) ? date('Y-m-d H:i', strtotime($operation->data . ' ' . $operation->ora_alle) ) : '',
                    'title' => $operation->headquarter->client->ragione_sociale ?? '',
                ];
                switch($operation->stato) {
                    case 2: {
                        $report = Report::where('id_intervento', $operation->id_intervento)->first();
                        if($report) {
                            $event['reportLink'] = '/downloadReport/' . $report->id_rapporto;
                        }
                        break;
                    }
                    case 3: {
                        $report = Report::where('id_intervento', $operation->id_intervento)->first();
                        if($report) {
                            $event['reportLink'] = '/downloadReport/' . $report->id_rapporto;
                        }
                        break;
                    }
                    case 5: {
                        $report = Report::where('id_intervento', $operation->id_intervento)->first();
                        if($report) {
                            $event['reportLink'] = '/downloadReport/' . $report->id_rapporto;
                        }
                        break;
                    }
                }
                $event['class'] = "full-color-2 " . $this->getClassName($operation);
                $event['className'] = "full-color " . $this->getClassName($operation);

                foreach ($operation->techniciansArray as $technician) {
                    if ($technician) {
                        $event['split'] = $this->getColumnNumber($technicians, (int)$technician);
                        $response[$index++] = $event;
                    }
                }
            }

            return response()->json((array)$response, 200);
        } catch(\Exception $e) {
            return response($e->getMessage());
        }
    }


    private function updateMachineries($machineries, $id)
    {
        foreach($machineries as $machinery) {
            if($machinery->id) {
                if(OperationMachinery::where([['id_intervento', $id], ['id_macchinario', $machinery->id]])->first()) {
                    OperationMachinery::where([['id_intervento', $id], ['id_macchinario', $machinery->id]])->update([
                        'desc_intervento' => $machinery->description
                    ]);
                } else {
                    OperationMachinery::create([
                        'id_intervento' => $id,
                        'id_macchinario' => $machinery->id,
                        'desc_intervento' => $machinery->description
                    ]);
                }
            }
        }
        $ids = collect($machineries)->pluck('id');
        OperationMachinery::where('id_intervento', $id)->whereNotIn('id_macchinario', $ids)->delete();
        return true;
    }

    private function getClassName($operation)
    {
        switch($operation->stato) {
            case 0: {
                $className = 'light-gray';
                if ($operation->data >= date('Y-m-d')) {
                    $className = "light-gray";
                } else {
                    $className = 'light-orange';
                }
                break;
            }
            case 1: {
                if($operation->urgente == 1) {
                    $className = 'urgent';
                } elseif ($operation->data >= date('Y-m-d')) {
                    $className = "light-gray"; //white
                } else {
                    $className = 'light-orange';
                }
                break;
            }
            case 2: {
                $className = 'light-green';
                break;
            }
            case 3: {
                $className = 'light-blue';
                break;
            }
            case 4: {
                $className = 'light-gray';
                break;
            }
            case 5: {
                $className = 'light-violet';
                break;
            }
            default: {
                $className = '';
                break;
            }
        }

        if(str_contains($operation->note, 'Creato da promemoria')) {
            $className = 'light-pink';
        }
        return $className;
    }

    private function getColumnNumber($technicians, $id)
    {
        $position = $technicians->search($id);
        if ($position !== false) {
            $position++;
        }
        return $position;
    }
}
