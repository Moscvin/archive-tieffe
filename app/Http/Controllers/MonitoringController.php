<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clienti;
use App\Models\Location;
use App\Models\Operation\Operation;
use App\CoreUsers;

use App\Models\Addresses\Comuni;
use App\Models\Addresses\Nazioni;
use App\Models\Addresses\Province;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

use App\Models\Report\Report;

use DB;
use PDF;

class MonitoringController extends Controller
{
    public function getIntervenitByDateStartToEnd(Request $request){
        try {

            $r = $request['params'];
            $date_start = $r['date_start'];
            $date_end = $r['date_end'];
            $curent_date = $r['curent_date'];
            $status = !isset($r['type']) ? ( isset($r['status']) ? $r['status'] : 9 ) : 9;

            Carbon::setLocale('it');
            if($date_end == null) {
                $operations = Operation::where('data','>=',$date_start)->with(['machineries'=>function($query){
                        $query->with('machinery');
                    }, 'location' => function($query){
                        $query->with('client');
                    }])->with('lastReportCanceled')->when($status != 9, function($query) use($status) {
                    if($status == 1) {
                        return $query->where([['stato', $status], ['data', '>=', date('Y-m-d H:i:s', time())]]);
                    } elseif($status == 3) {
                        return $query->whereIn('stato', [1, 3])->where('data', '<=', date('Y-m-d H:i:s', time()));
                    } else {
                        return $query->where('stato', $status);
                    }
                })->orderBy('data')->get();
            } else if ($date_end != null){
                $operations = Operation::where([['data','>=',$date_start], ['data','<=',$date_end]])->with(['machineries'=>function($query){
                        $query->with('machinery');
                    }])->with('lastReportCanceled')->when($status != 9, function($query) use($status) {
                    if($status == 1) {
                        return $query->where([['stato', $status], ['data', '>=', date('Y-m-d H:i:s', time())]]);
                    } elseif($status == 3) {
                        return $query->whereIn('stato', [1, 3])->where('data', '<=', date('Y-m-d H:i:s', time()));
                    } else {
                        return $query->where('stato', $status);
                    }
                })->orderBy('data')->get();
            }

            $response = [];
            foreach ($operations as $key => $operation) {
                $response[$key]['statusText'] = $operation->tipologia ? $operation->tipologia : ($operation->stato == 0 ? 'Da pianificare' : 'Scaduto');
                $response[$key]['className'] = $this->getClassName($operation);
                $response[$key]['address'] = $operation->headquarter->address ?? '';

                if($operation->stato == 0) {
                    $response[$key]['className'] .= ' bold';
                }

                $response[$key]['title'] = $operation->headquarter->client->ragione_sociale ?? 'Nessun dato';

                $response[$key]['machineriesDescription'] = $operation->machineries->map(function($item, $key) {
                    return [
                        'description' => $item->machinery->descrizione ?? '',
                        'model' => $item->machinery->modello ?? '',
                    ];
                });

                $response[$key]['technicians'] = $operation->technicians();

                $response[$key]['id'] = $operation->id_intervento;
                $response[$key]['tipologia'] = $operation->tipologia;
                $response[$key]['client'] = (object)[
                    'id' => $operation->headquarter->client->id ?? '',
                    'name' => $operation->headquarter->client->ragione_sociale ?? ''
                ];
                $response[$key]['headquarter'] = $operation->id_sede;
                $response[$key]['urgent'] = $operation->urgente;
                $response[$key]['status'] = $operation->stato;
                $response[$key]['date'] = $operation->data;
                $response[$key]['ora_dalle'] = $operation->ora_dalle;
                $response[$key]['ora_alle'] = $operation->ora_alle;
                $response[$key]['technician_1'] = $operation->techniciansArray[0] ?? '';
                $response[$key]['technician_2'] = $operation->techniciansArray[1] ?? '';
                $response[$key]['technician_3'] = $operation->techniciansArray[2] ?? '';
                $response[$key]['bodyStatus'] = $operation->a_corpo;
                $response[$key]['cestello'] = $operation->cestello;
                $response[$key]['incasso'] = $operation->incasso;
                $response[$key]['invoiceTo'] = $operation->fatturare_a;
                $response[$key]['note'] = $operation->note;

                $response[$key]['file'] = $operation->file;
                $response[$key]['fileName'] = $operation->fileName;
                $response[$key]['path'] = $operation->file;
                $response[$key]['start'] = $operation->data;

                $response[$key]['machineries'] = $operation->machineries->map(function($item) {
                    return [
                        'id' => $item->id_macchinario,
                        'description' => $item->fullDescription
                    ];
                });
            }
            $this->data['interventi'] = collect($response)->groupBy('date');
            return response($this->data);
        } catch(\Exception $e) {
            return response($e->getMessage());
        }

    }

    public function getOperationsByClient(Request $request){

        $r = $request['params'];

        $client = $r['client'];
        $clientId = $client['id'] ?? '';
        $clientName = $client['name'] ?? '';
        $searchValue = $r['value'];

        Carbon::setLocale('it');

        $operations = Operation::with(['machineries' => function($query){

            $query->with('machinery');

        }, 'headquarter' => function($query){

            $query->with('client');

        }])->with('lastReportCanceled');

        if($searchValue !== null){
            $operations = $operations->whereHas('headquarter', function($query) use ($searchValue) {
                $query->whereHas('client', function($query) use ($searchValue){
                    return $query->where('ragione_sociale', 'like', '%'.$searchValue.'%');
                });
            });
        }

        $operations = $operations->orderBy('data', 'desc')->get();
    

        $response = [];
        foreach ($operations as $key => $operation) {

            $response[$key]['statusText'] = $operation->tipologia ? $operation->tipologia : ($operation->stato == 0 ? 'Da pianificare' : 'Scaduto');
            $response[$key]['className'] = $this->getClassName($operation);
            $response[$key]['address'] = $operation->headquarter->address ?? '';

            if($operation->stato == 0) {
                $response[$key]['className'] .= ' bold';
            }

            $response[$key]['title'] = $operation->headquarter->client->ragione_sociale ?? 'Nessun dato';

            $response[$key]['machineriesDescription'] = $operation->machineries->map(function($item, $key) {
                return [
                    'description' => $item->machinery->descrizione ?? '',
                    'model' => $item->machinery->modello ?? '',
                ];
            });

            $response[$key]['technicians'] = $operation->technicians();

            $response[$key]['id'] = $operation->id_intervento;
            $response[$key]['tipologia'] = $operation->tipologia;
            $response[$key]['client'] = (object)[
                'id' => $operation->headquarter->client->id ?? '',
                'name' => $operation->headquarter->client->ragione_sociale ?? ''
            ];
            $response[$key]['headquarter'] = $operation->id_sede;
            $response[$key]['urgent'] = $operation->urgente;
            $response[$key]['status'] = $operation->stato;
            $response[$key]['date'] = $operation->data;
            $response[$key]['ora_dalle'] = $operation->ora_dalle;
            $response[$key]['ora_alle'] = $operation->ora_alle;
            $response[$key]['technician_1'] = $operation->techniciansArray[0] ?? '';
            $response[$key]['technician_2'] = $operation->techniciansArray[1] ?? '';
            $response[$key]['technician_3'] = $operation->techniciansArray[2] ?? '';
            $response[$key]['bodyStatus'] = $operation->a_corpo;
            $response[$key]['cestello'] = $operation->cestello;
            $response[$key]['incasso'] = $operation->incasso;
            $response[$key]['invoiceTo'] = $operation->fatturare_a;
            $response[$key]['note'] = $operation->note;

            $response[$key]['file'] = $operation->file;
            $response[$key]['fileName'] = $operation->fileName;
            $response[$key]['path'] = $operation->file;
            $response[$key]['start'] = $operation->data;

            $response[$key]['machineries'] = $operation->machineries->map(function($item) {
                return [
                    'id' => $item->id_macchinario,
                    'description' => $item->fullDescription
                ];
            });
        }
        $this->data['interventi'] = collect($response)->groupBy('date');
        return response($this->data);
    }

    public function calendarioUpdadeIntervent(Request $request){

        $r = $request['params'];
        $id_intervento = $r['id_intervento'];
        $intervent = (object)$r['intervent'];
        $stato = Operation::where('id_intervento', $intervent->id_intervento)->first();
        if($stato->stato == 3){
            if($intervent->data && $intervent->ora) {
                $date = date('Y-m-d H:i:s', strtotime($intervent->data . ' ' . $intervent->ora . ':00'));
                $dateinnote = date('d/m/Y H:i:s', strtotime($intervent->data . ' ' . $intervent->ora . ':00'));
            } else {
                $date = null;
                $dateinnote = null;
            }
            $tecnici = '';
            if(isset($intervent->tecnici_selected) && count($intervent->tecnici_selected) && $intervent->tipologia == "") {
                foreach($intervent->tecnici_selected as $tecnico) {
                    $tecnici .= $tecnico['id_user'] . ';';
                }
            }

            $dateinupdate = date('d/m/Y H:i:s', strtotime($stato->data));
            $noteinupdate = 'Intervento ripianificato da ' . $dateinupdate . ' ' . $stato->note;
            $data = [
                'id_old_operation' => $intervent->id_intervento,
                'id_clienti' => $intervent->id_clienti,
                'descrizione_intervento' => $intervent->descrizione_intervento,
                'data' => $date,
                'stato' => '1',
                'tecnico' => $tecnici,
                'note' => $noteinupdate ,
                'tipo' => $intervent->tipo,
                'fatturazione' => $intervent->fatturazione_status ? $intervent->fatturazione_status : 0,
                'conto_di' => $intervent->fatturazione_mensil ? $intervent->fatturazione_mensil : 0,
                'tipologia' => $intervent->tipologia,
            ];
            $check = Operation::create($data);
            if($intervent->tipologia == ""){
                $newnote = 'Intervento ripianificato per ' . $dateinnote . ' ' . $stato->note;
            } else {
                $newnote = 'Intervento ripianificato come Non assegnato';
            }
            $datatoupdate = [
                'stato' => '5',
                'note' => $newnote,
                'data_ripianificato' => $date
            ];
            Operation::where('id_intervento',$intervent->id_intervento)->update($datatoupdate);
        } else {
        if($intervent->data && $intervent->ora_dalle && $intervent->ora_alle && ($intervent->stato != 0 && $intervent->tipologia == "")) {
            $date = date('Y-m-d H:i:s', strtotime($intervent->data . ' ' . $intervent->ora_dalle));
        } else {
            $date = null;
        }
        $tecnici = '';
        if(isset($intervent->tecnici_selected) && count($intervent->tecnici_selected) && $intervent->tipologia == "") {
            foreach($intervent->tecnici_selected as $tecnico) {
                $tecnici .= $tecnico['id_user'] . ';';
            }
        }

        $data = [
            'descrizione_intervento' => $intervent->descrizione_intervento,
            'data' => $date,
            'stato' => $intervent->stato,
            'tecnico' => $tecnici,
            'note' => $intervent->note,
            'tipo' => $intervent->tipo,
            'fatturazione' => $intervent->fatturazione_status ? $intervent->fatturazione_status : 0,
            'conto_di' => $intervent->fatturazione_mensil ? $intervent->fatturazione_mensil : 0,
            'tipologia' => $intervent->tipologia,
        ];

        Operation::where([['id_intervento', $id_intervento], ['stato', '<>', 2]])->update($data);
        }
        return response()->json(['statut' => 'Success']);
    }

    public function getEventsByViewModeAndDate(Request $request){
         $r = $request['params'];

        $date_start = $r['date_start'];
        $date_end = $r['date_end'];
        $mode = $r['mode'];

        $curent_date = Carbon::now()->format('Y-m-d');
        $operations = Operation::where([['data','>=',$date_start], ['data','<=',$date_end]])->orderBy('data', 'asc')->with(['lastReportCanceled', 'location', 'machineries' => function($query){
            $query->with('machinery');
        }])->get();

        $response = [];
        foreach ($operations as $key => $operation) {
            $response[$key]['statusText'] = $operation->tipologia ? $operation->tipologia : ($operation->stato == 0 ? 'Da pianificare' : 'Scaduto');
            $response[$key]['className'] = $this->getClassName($operation);
            $response[$key]['address'] = $operation->headquarter->address ?? '';

            if($operation->stato == 0) {
                $response[$key]['className'] .= ' bold';
            }

            $response[$key]['title'] = $operation->headquarter->client->ragione_sociale ?? 'Nessun dato';

            $response[$key]['machineriesDescription'] = $operation->machineries->map(function($item, $key) {
                return [
                    'description' => $item->machinery->descrizione ?? '',
                    'model' => $item->machinery->modello ?? '',
                ];
            });

            $response[$key]['id'] = $operation->id_intervento;
            $response[$key]['tipologia'] = $operation->tipologia;
            $response[$key]['client'] = (object)[
                'id' => $operation->headquarter->client->id ?? '',
                'name' => $operation->headquarter->client->ragione_sociale ?? ''
            ];
            $response[$key]['headquarter'] = $operation->id_sede;
            $response[$key]['urgent'] = $operation->urgente;
            $response[$key]['status'] = $operation->stato;
            $response[$key]['date'] = $operation->data;
            $response[$key]['ora_dalle'] = $operation->ora_dalle;
            $response[$key]['ora_alle'] = $operation->ora_alle;
            $response[$key]['technician_1'] = $operation->techniciansArray[0] ?? '';
            $response[$key]['technician_2'] = $operation->techniciansArray[1] ?? '';
            $response[$key]['technician_3'] = $operation->techniciansArray[2] ?? '';
            $response[$key]['note'] = $operation->note;
            $response[$key]['file'] = $operation->file;
            $response[$key]['fileName'] = $operation->fileName;
            $response[$key]['path'] = $operation->file;
            $response[$key]['start'] = $operation->data;

            $response[$key]['machineries'] = $operation->machineries->map(function($item) {
                return [
                    'id' => $item->id_macchinario,
                    'description' => $item->fullDescription
                ];
            });
        }
        $this->data['events'] = $response;
        return response()->json($this->data, 200);
    }

    private function getClassName($operation)
    {
        switch($operation->stato) {
            case 0: {
                $className = "light-gray";
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



        if($operation->note == 'Creato da promemoria') {
            $className = 'light-pink';
        }
        return $className;
    }
}
