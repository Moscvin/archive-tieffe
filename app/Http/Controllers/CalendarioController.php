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

use App\Models\Report;

use DB;
use PDF;


class CalendarioController extends Controller {
    public function index() {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        $invoicesTo = Clienti::where('committente', 1)->orderBy('ragione_sociale', 'asc')->get();

        $this->data['invoicesTo'] = $invoicesTo;
        return view('interventi.calendario', $this->data);
    }

    public function daprogrammare(){
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('interventi.daprogrammare', $this->data);
    }

    public function getIntervenitByDateStartToEnd(Request $request){
        try {

            $r = $request['params'];
            $date_start = $r['date_start'];
            $date_end = $r['date_end'];
            $curent_date = $r['curent_date'];
            $status = !isset($r['type']) ? ( isset($r['status']) ? $r['status'] : 9 ) : 9;

            Carbon::setLocale('it');
            if($date_end == null) {
                $interventi = Operation::where('data','>=',$date_start)->with(['machineries'=>function($query){
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
                $interventi = Operation::where([['data','>=',$date_start], ['data','<=',$date_end]])->with(['machineries'=>function($query){
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

            if(count($interventi)) {
                foreach($interventi as $intervento) {
                    $intervento->start = $intervento->data;

                    if($intervento->id_clienti) {
                        $client = Clienti::where('id', $intervento->id_clienti)->first();
                        $intervento->client_name = $intervento->location->client->ragione_sociale or '';
                        $intervento->localita = strtoupper($client->nazione_sl) != 'ITALIA' ?
                            $client->comune_sl ? $client->comune_sl .' - ' : '' .$client->nazione_sl
                            : $client->comune_sl.' - '.$client->provincia_sl;
                        $intervento->title = $intervento->localita;
                    }

                    $intervento->esito = $intervento->stato == 0 ? 'Da pianificare' : 'Pianificato';
                    $intervento->color = 'light-red';

                    $descriptions = [];
                    if(isset($intervento->machineries)){
                        foreach($intervento->machineries as $description){
                            $descriptions[] = $description->desc_intervento;
                        }
                        $descriptions = implode('; ', $descriptions);
                    }

                    $intervento->descrizione_intervento = $descriptions;
                    $intervento->data = $intervento->data ? date('Y-m-d', strtotime($intervento->data)) : '';
                    $intervento->fatturazione_status = $intervento->fatturazione ? 1 : 0;
                    $intervento->fatturazione_mensil = $intervento->conto_di ? $intervento->conto_di : 0;
                    $intervento->last_report = $intervento->lastReportCanceled;
                    $intervento->invoiceTo = $intervento->fatturare_a;
                    $machineries = [];

                    if(isset($intervento->machineries)){
                        foreach($intervento->machineries as $macchinario){
                            $machineries[] = $macchinario->modello;
                        }
                    }


                    $intervento->macchinario = implode('; ', $machineries);

                    $intervento->sede = $intervento->location->tipologia ?? '';
                    $intervento->client_name = $intervento->location->client->ragione_sociale ?? '';

                    if($intervento->tecnico) {
                        $tecnici_selected = preg_split('/;/', $intervento->tecnico);
                        $tecnico_gestione = intval($tecnici_selected[0]);

                        if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                            array_pop($tecnici_selected);
                        }
                        $intervento->tecnico_gestione = intval($tecnici_selected[0]);
                        $idsImploded = implode(',', $tecnici_selected);
                        $intervento->tecnici_selected = CoreUsers::select('id_user', 'name', 'family_name')->whereIn('id_user', $tecnici_selected)
                        ->orderByRaw(\DB::raw("FIELD(id_user, $idsImploded) asc"))->get();

                        $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
                        $intervento->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                    }

                    if($intervento->id_clienti) {
                        $intervento->title = $intervento->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $intervento->localita : $intervento->localita;

                    } elseif($intervento->tecnico) {
                        $intervento->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                    } else {
                        $intervento->title = 'Nessun dato';
                    }

                    if(isset($r['type'])){
                        $intervento->className = "full-color ";
                    } else {
                        $intervento->className = "";
                    }

                    switch($intervento->stato) {
                        case 0: {
                            $intervento->className .= 'light-red';
                            break;
                        }
                        case 1: {
                            if(strtotime($intervento->data) > time()) {
                                $intervento->className .= "white";
                                break;
                            }
                            $intervento->className .= 'light-orange';
                            break;
                        }
                        case 2: {
                            $report = Report::where('id_intervento', $intervento->id_intervento)->first();
                            if($report) {
                                $intervento->reportLink = '/downloadReport/' . $report->id_rapporto;
                            }
                            $intervento->className .= 'light-green';
                            break;
                        }
                        case 3: {
                            $report = Report::where('id_intervento', $intervento->id_intervento)->first();
                            if($report) {
                                $intervento->reportLink = '/downloadReport/' . $report->id_rapporto;
                            }
                            $intervento->className .= 'light-blue';
                            break;
                        }
                        case 5: {
                            $report = Report::where('id_intervento', $intervento->id_intervento)->first();
                            if($report) {
                                $intervento->reportLink = '/downloadReport/' . $report->id_rapporto;
                            }
                            $intervento->className .= 'light-violet';
                            break;
                        }
                        default: {
                            $intervento->className .= 'light-gray';
                            break;
                        }
                    }

                    if($intervento->note == 'Creato da promemoria'){
                        $intervento->className = 'light-pink';
                    }
                    if($intervento->urgente == 1){
                        $intervento->className = 'red';
                    }


                }

                if(!isset($r['type'])){
                    $forExcel = $interventi;
                    $interventi = $interventi->groupBy(function($date) {
                        return Carbon::parse($date->data)->format('Y-m-d');
                    });
                    $interventi['excel'] = $forExcel;
                }

                $this->data['interventi'] = $interventi;
                return response($this->data);
            }
            $this->data['interventi'] = [];
            return response($this->data);
        } catch(\Exception $e) {
            return response($e->getMessage());
        }

    }

    public function calendarioUpdadeIntervent(Request $request){

        $r = $request['params'];
        $id_intervento = $r['id_intervento'];
        $intervent = (object)$r['intervent'];
        $stato = Operation::where('id_intervento', $intervent->id_intervento)->first();
        if($stato->stato == 3){
            if($intervent->data && $intervent->ora_dalle && $intervent->ora_alle) {
                $date = date('Y-m-d H:i:s', strtotime($intervent->data . ' ' . $intervent->ora_dalle));
                $dateinnote = date('d/m/Y H:i:s', strtotime($intervent->data . ' ' . $intervent->ora_dalle));
            } else {
                $date = null;
                $dateinnote = null;
            }
            $tecnici = '';
            if(isset($intervent->tecnici_selected) && count($intervent->tecnici_selected)) {
                foreach($intervent->tecnici_selected as $tecnico) {
                    $tecnici .= $tecnico['id_user'] . ';';
                }
            }

            $dateinupdate = date('d/m/Y H:i:s', strtotime($stato->data));
            $noteinupdate = 'Intervento ripianificato da ' . $dateinupdate . ' ' . $stato->note;
            $data = [
                'id_old_operation' => $intervent->id_intervento,
                'id_clienti' => $intervent->id_clienti,
                'tipologia' => $intervent->tipologia,
                'ora_dalle' => $intervent->ora_dalle,
                'ora_alle' => $intervent->ora_alle,
                'incasso' => $intervent->incasso,
                'descrizione_intervento' => $intervent->descrizione_intervento,
                'data' => $date,
                'stato' => '1',
                'tecnico' => $tecnici,
                'note' => $noteinupdate ,
                'tipo' => $intervent->tipo,
                'fatturazione' => $intervent->fatturazione_status ? $intervent->fatturazione_status : 0,
                'conto_di' => $intervent->fatturazione_mensil ? $intervent->fatturazione_mensil : 0,
                'fatturare_a' => $intervent->invoiceTo,
            ];
            $check = Operation::create($data);
            $newnote = 'Intervento ripianificato per ' . $dateinnote . ' ' . $stato->note;

            $datatoupdate = [
                'stato' => '5',
                'note' => $newnote,
                'data_ripianificato' => $date
            ];
            Operation::where('id_intervento',$intervent->id_intervento)->update($datatoupdate);
        } else {
        if($intervent->data && ($intervent->ora_dalle && $intervent->ora_alle) && $intervent->stato != 0) {
            $date = date('Y-m-d H:i:s', strtotime($intervent->data . ' ' . $intervent->ora_dalle));
        } else {
            $date = null;
        }
        $tecnici = '';
        if(isset($intervent->tecnici_selected) && count($intervent->tecnici_selected)) {
            foreach($intervent->tecnici_selected as $tecnico) {
                $tecnici .= $tecnico['id_user'] . ';';
            }
        }

        $data = [
            'descrizione_intervento' => $intervent->descrizione_intervento,
            'data' => $date,
            'tipologia' => $intervent->tipologia,
            'ora_dalle' => $intervent->ora_dalle,
            'ora_alle' => $intervent->ora_alle,
            'stato' => $intervent->stato,
            'tecnico' => $tecnici,
            'note' => $intervent->note,
            'tipo' => $intervent->tipo,
            'fatturazione' => $intervent->fatturazione_status ? $intervent->fatturazione_status : 0,
            'conto_di' => $intervent->fatturazione_mensil ? $intervent->fatturazione_mensil : 0,
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
        $interventi = Operation::where([['data','>=',$date_start], ['data','<=',$date_end]])->orderBy('data', 'asc')->with('lastReportCanceled')->get();

        if(count($interventi)) {
            foreach($interventi as $intervento) {
                $intervento->start = $intervento->data;

                if($intervento->id_clienti) {
                    $client = Clienti::where('id', $intervento->id_clienti)->first();
                    $intervento->client_name = $client->azienda == 1 ? $client->ragione_sociale : $client->cognome . ' ' . $client->nome;
                    $intervento->localita = strtoupper($client->nazione_sl) != 'ITALIA' ?
                        $client->comune_sl ? $client->comune_sl .' - ' : '' .$client->nazione_sl
                        : $client->comune_sl.' - '.$client->provincia_sl;
                    $intervento->title = $intervento->localita;
                }
                $intervento->esito = $intervento->stato == 0 ? 'Da pianificare' : 'Pianificato';
                $intervento->data = $intervento->data ? date('Y-m-d', strtotime($intervento->data)) : '';
                $intervento->fatturazione_status = $intervento->fatturazione ? 1 : 0;
                $intervento->fatturazione_mensil = $intervento->conto_di ? $intervento->conto_di : 0;

                if($intervento->tecnico) {
                    $tecnici_selected = preg_split('/;/', $intervento->tecnico);
                    $tecnico_gestione = intval($tecnici_selected[0]);

                    if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                        array_pop($tecnici_selected);
                    }
                    $intervento->tecnico_gestione = intval($tecnici_selected[0]);
                    $idsImploded = implode(',', $tecnici_selected);
                    $intervento->tecnici_selected = CoreUsers::select('id_user', 'name', 'family_name')->whereIn('id_user', $tecnici_selected)
                    ->orderByRaw(\DB::raw("FIELD(id_user, $idsImploded) asc"))->get();

                    $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
                }

                if($intervento->id_clienti) {
                    $intervento->title = $intervento->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $intervento->localita : $intervento->localita;

                } elseif($intervento->tecnico) {
                    $intervento->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                } else {
                    $intervento->title = 'Nessun dato';
                }

                $intervento->className = "full-color ";
                switch($intervento->stato) {
                    case 0: {
                        $intervento->className .= 'light-red';
                        break;
                    }
                    case 1: {
                        if(strtotime($intervento->data) > time()) {
                            $intervento->className .= "white";
                            break;
                        }
                        $intervento->className .= 'light-orange';
                        break;
                    }
                    case 2: {
                        $intervento->className .= 'light-green';
                        break;
                    }
                    case 3: {
                        $intervento->className .= 'light-blue';
                        break;
                    }
                    case 4: {
                        $className = 'light-gray';
                        break;
                    }
                    case 5: {
                        $intervento->className .= 'light-violet';
                        break;
                    }
                    default: {
                        $intervento->className .= 'light-red';
                        break;
                    }
                }
            }
        }

        $this->data['events'] = $interventi;
        return response($this->data);
    }
}
