<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Excel;

use File;
use Storage;
use App\Models\Clienti;
use App\Models\Operation;
use App\Models\Mean;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\ReportEquipment;
use App\CoreUsers;
use Illuminate\Support\Facades\Response;
use PDF;
use App\Models\InternalWork;
use App\Export\Pdf\ReportPdf;

class ReportController extends MainController {
    public function __construct() {
        parent::__construct();
    }

    public function worksInProgress() {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('reports.wokrs_in_progress', $this->data);
    }

    public function worksInProgressAjax(Request $request) {
        try {
            Carbon::setLocale('it');
            $request->date_start = $request->date_start ? $request->date_start : '1970-01-01 00:00:01'; 
            if($request->date_end == null) {
                $interventi = Operation::doesntHave('report')->where([
                    ['data','>=', $request->date_start . ' 00:00:00'],
                    ['data', '<=', date('Y-m-d 24:00:00', time())],
                    ['stato', 1]
                ])->orderBy('data', 'asc')->get();
            } else {
                $interventi = Operation::doesntHave('report')->where([
                    ['data', '<=', date('Y-m-d 24:00:00', time())],
                    ['data', '>=', $request->date_start . ' 00:00:00'],
                    ['data', '<=', $request->date_end . ' 24:00:00'],
                    ['stato', 1]
                ])->orderBy('data', 'asc')->get();
                
            }
            if(count($interventi)) {
                foreach($interventi as $intervento) {
                    if($intervento->id_clienti) {
                        $client = Clienti::where('id', $intervento->id_clienti)->first();
                        $intervento->client_name = $client->azienda == 1 ? $client->ragione_sociale : $client->cognome . ' ' . $client->nome;
                        $intervento->localita = strtoupper($client->nazione_sl) != 'ITALIA' ? 
                            $client->comune_sl ? $client->comune_sl .' - ' : '' .$client->nazione_sl 
                            : $client->comune_sl.' - '.$client->provincia_sl;
                        $intervento->title = $intervento->localita;
                    }
                    //Descrizione
                    $intervento->descrizione=$intervento->descrizione_intervento;

                    $intervento->tipologia = $intervento->tipo == 1 ? 'Meccanica' : 'Verde';
                    $intervento->esito = $intervento->stato == 0 ? 'Da pianificare' : 'Pianificato';
                    $intervento->ora = $intervento->data ? date('H:i', strtotime($intervento->data)) : '';
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
                        $intervento->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                    }
    
                    if($intervento->id_clienti) {
                        $intervento->title = $intervento->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $intervento->localita : $intervento->localita;
                        
                    } elseif($intervento->tecnico) {
                        $intervento->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                    } else {
                        $intervento->title = 'Nessun dato';
                    }

                    $intervento->className = "";
                    
                    switch($intervento->stato) {
                        case 0: {
                            $intervento->className .= 'light-red';
                            break;
                        }
                        case 1: {
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
                        default: {
                            $intervento->className .= 'light-red';
                            break;
                        }
                    }
                }
                return response($interventi);
            }

            $interventi = [];
            return response($interventi);
        } catch(\Exception $e) {
            return response($e->getMessage());
        }
    }

    public function worksToInvoice() {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('reports.works_to_invoice', $this->data);
    }

    public function worksToInvoiceAjax(Request $request) {
        $operations = Operation::with(['client', 'report' => function($query) {
            return $query->with('mean');
        }])->whereHas('report', function($query) {
            $query->where('fatturato', 0);
        })->where(function($query) {
            return $query->where([['stato', 2],['fatturazione', 0]])->orWhere([['stato',3],['fatturazione', 0]])->orWhere([['stato',5],['fatturazione', 0]]);
        })->orderBy('data', 'desc')->get();
        $data = [];
        if(count($operations)) {
            foreach($operations as $operation) {
                if($operation->id_clienti) {
                    $operation->client_name = $operation->client->azienda == 1 ? $operation->client->ragione_sociale : 
                        $operation->client->cognome . ' ' . $operation->client->nome;
                    $operation->localita = strtoupper($operation->client->nazione_sl) != 'ITALIA' ? 
                        $operation->client->comune_sl ? $operation->client->comune_sl .' - ' : '' .$operation->client->nazione_sl 
                        : $operation->client->comune_sl.' - '.$operation->client->provincia_sl;
                }
                
                if($operation->tecnico) {
                    $tecnici_selected = preg_split('/;/', $operation->tecnico);
                    $tecnico_gestione = intval($tecnici_selected[0]);

                    if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                        array_pop($tecnici_selected);
                    }
                    $operation->tecnico_gestione = intval($tecnici_selected[0]);
                    $idsImploded = implode(',', $tecnici_selected);
                    $operation->tecnici_selected = CoreUsers::select('id_user', 'name', 'family_name')->whereIn('id_user', $tecnici_selected)
                    ->orderByRaw(\DB::raw("FIELD(id_user, $idsImploded) asc"))->get();

                    $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
                    $operation->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                }

                if($operation->id_clienti) {
                    $operation->title = $operation->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $operation->localita : 
                        $operation->localita;
                    
                } elseif($operation->tecnico) {
                    $operation->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                } else {
                    $operation->title = 'Nessun dato';
                }
                $data[] = [
                    'data' => $operation->data,
                    'client_name' => $operation->client_name,
                    'stato' => $operation->stato,
                    'descrizione_intervento' => $operation->descrizione_intervento,
                    'tecnico_name' => $operation->tecnico_name,
                    'marca' => $operation->report->mean ? $operation->report->mean->marca : '',
                    'reportNumber' => $operation->report->tipo_rapporto == 1 ? $operation->report->nr_rapporto . ' / C' : (
                        $operation->report->tipo_rapporto == 3 ? $operation->report->nr_rapporto . ' / V' : $operation->report->nr_rapporto),
                    'conto_di' => $operation->conto_di == 1 ? 'Cliente' : 
                        ($operation->conto_di == 2 ? 'Interporto' : (
                            $operation->conto_di == 3 ? 'Consorzio' : '')),
                    'id_intervento' => $operation->id_intervento,
                    'id_rapporto' => $operation->report->id_rapporto
                ];
            }
            return response($data);
        }

        return response([]);
    }

    public function workToInvoiceEdit($id = null, $mode = 'view') {
        $this->data['mode'] = $mode;
        $operation = Operation::with(['client', 'report' => function($query) {
            return $query->with(['mean', 'equipment', 'photos']);
        }])->where('id_intervento', $id)->first();
        if($operation->id_clienti) {
            $operation->client_name = $operation->client->azienda == 1 ? $operation->client->ragione_sociale : 
                $operation->client->cognome . ' ' . $operation->client->nome;
            $operation->localita = strtoupper($operation->client->nazione_sl) != 'ITALIA' ? 
                $operation->client->comune_sl ? $operation->client->comune_sl .' - ' : '' .$operation->client->nazione_sl 
                : $operation->client->comune_sl.' - '.$operation->client->provincia_sl;
        }
        
        if($operation->tecnico) {
            $tecnici_selected = preg_split('/;/', $operation->tecnico);
            $tecnico_gestione = intval($tecnici_selected[0]);

            if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                array_pop($tecnici_selected);
            }
            $operation->tecnico_gestione = intval($tecnici_selected[0]);
            $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
            $operation->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
        }

        if($operation->id_clienti) {
            $operation->title = $operation->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $operation->localita : 
                $operation->localita;
            
        } elseif($operation->tecnico) {
            $operation->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
        } else {
            $operation->title = 'Nessun dato';
        }

        $operation->conto_di = $operation->conto_di == 1 ? 'Cliente' : ($operation->conto_di == 2 ? 'Interporto' : ($operation->conto_di == 3 ? 'Consorzio' : ''));
        $operation->status = $operation->stato == 1 ? 'Pianificato' : ($operation->stato == 2 ? 'Confermato' : ($operation->stato == 3 ? 'Non completato' : (
            $operation->stato == 0 ? 'Da pianificare' : 'KO'
        )));
        $operation->hoursByDates = $this->getArrayOfWorkedDays($operation->id_intervento);

        $this->data['operation'] = $operation;

        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        return view('reports.works_to_invoice_edit', $this->data);
    }

    public function worksPerMonth() {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('reports.works_per_month', $this->data);
    }

    public function worksPerMonthAjax(Request $request) {
        $operations = Operation::where([['fatturazione', 1], ['data', 'like', $request->date_start . '%']])->whereIn('stato', [2, 3, 5])
        ->with(['client', 'report' => function($query) {
            return $query->with('mean');
        }])->whereHas('report', function($query) {
            $query->where('fatturato', 0);
        })->when($request->client, function($query) use ($request) {
                return $query->where('id_clienti', $request->client);
            }
        )->orderBy('data', 'desc')->get();
        $data = [];
        if(count($operations)) {
            foreach($operations as $operation) {
                if($operation->id_clienti) {
                    $operation->client_name = $operation->client->azienda == 1 ? $operation->client->ragione_sociale : 
                        $operation->client->cognome . ' ' . $operation->client->nome;
                    $operation->localita = strtoupper($operation->client->nazione_sl) != 'ITALIA' ? 
                        $operation->client->comune_sl ? $operation->client->comune_sl .' - ' : '' .$operation->client->nazione_sl 
                        : $operation->client->comune_sl.' - '.$operation->client->provincia_sl;
                }
                
                if($operation->tecnico) {
                    $tecnici_selected = preg_split('/;/', $operation->tecnico);
                    $tecnico_gestione = intval($tecnici_selected[0]);

                    if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                        array_pop($tecnici_selected);
                    }
                    $operation->tecnico_gestione = intval($tecnici_selected[0]);
                    $idsImploded = implode(',', $tecnici_selected);
                    $operation->tecnici_selected = CoreUsers::select('id_user', 'name', 'family_name')->whereIn('id_user', $tecnici_selected)
                    ->orderByRaw(\DB::raw("FIELD(id_user, $idsImploded) asc"))->get();

                    $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
                    $operation->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                }

                if($operation->id_clienti) {
                    $operation->title = $operation->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $operation->localita : 
                        $operation->localita;
                    
                } elseif($operation->tecnico) {
                    $operation->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                } else {
                    $operation->title = 'Nessun dato';
                }
                $data[] = [
                    'data' => $operation->data,
                    'stato' => $operation->stato,
                    'client_name' => $operation->client_name,
                    'descrizione_intervento' => $operation->descrizione_intervento,
                    'tecnico_name' => $operation->tecnico_name,
                    'marca' => $operation->report->mean ? $operation->report->mean->marca : '',
                    'linkToPDF' => ' ',
                    'conto_di' => $operation->conto_di == 1 ? 'Cliente' : 
                        ($operation->conto_di == 2 ? 'Interporto' : (
                            $operation->conto_di == 3 ? 'Consorzio' : '')),
                    'id_intervento' => $operation->id_intervento,
                    'id_rapporto' => $operation->report->id_rapporto,
                    'tipo' => $operation->tipo,
                    'reportNumber' => $operation->report->tipo_rapporto == 1 ? $operation->report->nr_rapporto . ' / C' : (
                        $operation->report->tipo_rapporto == 3 ? $operation->report->nr_rapporto . ' / V' : $operation->report->nr_rapporto),
                ];
            }
            return response($data);
        }

        return response([]);
    }

    public function workPerMonthEdit($id = null, $mode = 'view') {
        $this->data['mode'] = $mode;
        $operation = Operation::with(['client', 'report' => function($query) {
            return $query->with(['mean', 'equipment', 'photos']);
        }])->where('id_intervento', $id)->first();
        if($operation->id_clienti) {
            $operation->client_name = $operation->client->azienda == 1 ? $operation->client->ragione_sociale : 
                $operation->client->cognome . ' ' . $operation->client->nome;
            $operation->localita = strtoupper($operation->client->nazione_sl) != 'ITALIA' ? 
                $operation->client->comune_sl ? $operation->client->comune_sl .' - ' : '' .$operation->client->nazione_sl 
                : $operation->client->comune_sl.' - '.$operation->client->provincia_sl;
        }
        
        if($operation->tecnico) {
            $tecnici_selected = preg_split('/;/', $operation->tecnico);
            $tecnico_gestione = intval($tecnici_selected[0]);

            if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                array_pop($tecnici_selected);
            }
            $operation->tecnico_gestione = intval($tecnici_selected[0]);
            $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
            $operation->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
        }

        if($operation->id_clienti) {
            $operation->title = $operation->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $operation->localita : 
                $operation->localita;
            
        } elseif($operation->tecnico) {
            $operation->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
        } else {
            $operation->title = 'Nessun dato';
        }

        $operation->conto_di = $operation->conto_di == 1 ? 'Cliente' : ($operation->conto_di == 2 ? 'Interporto' : ($operation->conto_di == 3 ? 'Consorzio' : ''));
        $operation->status = $operation->stato == 1 ? 'Pianificato' : ($operation->stato == 2 ? 'Confermato' : ($operation->stato == 3 ? 'In corso' : (
            $operation->stato == 0 ? 'Da pianificare' : 'KO'
        )));

        $this->data['operation'] = $operation;

        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        return view('reports.works_per_month_edit', $this->data);
    }

    public function worksInvoiced() {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('reports.works_invoiced', $this->data);
    }

    public function worksInvoicedAjax(Request $request) {
        $operations = Operation::with(['client', 'report' => function($query) {
            return $query->with('mean');
        }])->whereHas('report', function($query) {
            $query->where('fatturato', 1);
        })->whereHas('report', function($query) {
            $query->with('mean');
        })->where([['data', 'like', $request->date_start . '%']]
        )->whereIn('stato', [2, 3, 5])->when($request->client, function($query) use ($request) {
                return $query->where('id_clienti', $request->client);
            }
        )->orderBy('data', 'desc')->get();
        $data = [];
        if(count($operations)) {
            foreach($operations as $operation) {
                if($operation->id_clienti) {
                    $operation->client_name = $operation->client->azienda == 1 ? $operation->client->ragione_sociale : 
                        $operation->client->cognome . ' ' . $operation->client->nome;
                    $operation->localita = strtoupper($operation->client->nazione_sl) != 'ITALIA' ? 
                        $operation->client->comune_sl ? $operation->client->comune_sl .' - ' : '' .$operation->client->nazione_sl 
                        : $operation->client->comune_sl.' - '.$operation->client->provincia_sl;
                }
                
                if($operation->tecnico) {
                    $tecnici_selected = preg_split('/;/', $operation->tecnico);
                    $tecnico_gestione = intval($tecnici_selected[0]);

                    if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                        array_pop($tecnici_selected);
                    }
                    $operation->tecnico_gestione = intval($tecnici_selected[0]);
                    $idsImploded = implode(',', $tecnici_selected);
                    $operation->tecnici_selected = CoreUsers::select('id_user', 'name', 'family_name')->whereIn('id_user', $tecnici_selected)
                    ->orderByRaw(\DB::raw("FIELD(id_user, $idsImploded) asc"))->get();

                    $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
                    $operation->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                }

                if($operation->id_clienti) {
                    $operation->title = $operation->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $operation->localita : 
                        $operation->localita;
                    
                } elseif($operation->tecnico) {
                    $operation->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
                } else {
                    $operation->title = 'Nessun dato';
                }
                $data[] = [
                    'data' => $operation->data,
                    'client_name' => $operation->client_name,
                    'stato' => $operation->stato,
                    'descrizione_intervento' => $operation->descrizione_intervento,
                    'tecnico_name' => $operation->tecnico_name,
                    'marca' => $operation->report->mean ? $operation->report->mean->marca : '',
                    'linkToPDF' => ' ',
                    'conto_di' => $operation->conto_di == 1 ? 'Cliente' : 
                        ($operation->conto_di == 2 ? 'Interporto' : (
                            $operation->conto_di == 3 ? 'Consorzio' : '')),
                    'id_intervento' => $operation->id_intervento,
                    'id_rapporto' => $operation->report->id_rapporto,
                    'tipo' => $operation->tipo,
                    'mensile' => $operation->fatturazione ? 'Si' : 'No',
                    'reportNumber' => $operation->report->tipo_rapporto == 1 ? $operation->report->nr_rapporto . ' / C' : (
                        $operation->report->tipo_rapporto == 3 ? $operation->report->nr_rapporto . ' / V' : $operation->report->nr_rapporto),
                ];
            }
            return response($data);
        }

        return response([]);
    }

    public function workInvoicedEdit($id = null, $mode = 'view') {
        $this->data['mode'] = $mode;
        $operation = Operation::with(['client', 'report' => function($query) {
            return $query->with(['mean', 'equipment', 'photos']);
        }])->where('id_intervento', $id)->first();
        if($operation->id_clienti) {
            $operation->client_name = $operation->client->azienda == 1 ? $operation->client->ragione_sociale : 
                $operation->client->cognome . ' ' . $operation->client->nome;
            $operation->localita = strtoupper($operation->client->nazione_sl) != 'ITALIA' ? 
                $operation->client->comune_sl ? $operation->client->comune_sl .' - ' : '' .$operation->client->nazione_sl 
                : $operation->client->comune_sl.' - '.$operation->client->provincia_sl;
        }
        
        if($operation->tecnico) {
            $tecnici_selected = preg_split('/;/', $operation->tecnico);
            $tecnico_gestione = intval($tecnici_selected[0]);

            if(!$tecnici_selected[count($tecnici_selected) - 1]) {
                array_pop($tecnici_selected);
            }
            $operation->tecnico_gestione = intval($tecnici_selected[0]);
            $tecnico_gestione = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tecnico_gestione)->first();
            $operation->tecnico_name = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
        }

        if($operation->id_clienti) {
            $operation->title = $operation->tecnico ?  $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name . ' - ' .  $operation->localita : 
                $operation->localita;
            
        } elseif($operation->tecnico) {
            $operation->title = $tecnico_gestione->family_name . ' ' . $tecnico_gestione->name;
        } else {
            $operation->title = 'Nessun dato';
        }

        $operation->conto_di = $operation->conto_di == 1 ? 'Cliente' : ($operation->conto_di == 2 ? 'Interporto' : ($operation->conto_di == 3 ? 'Consorzio' : ''));
        $operation->status = $operation->stato == 1 ? 'Pianificato' : ($operation->stato == 2 ? 'Confermato' : ($operation->stato == 3 ? 'In corso' : (
            $operation->stato == 0 ? 'Da pianificare' : 'KO'
        )));
        $operation->hoursByDates = $this->getArrayOfWorkedDays($operation->id_intervento);
        $this->data['operation'] = $operation;

        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        return view('reports.works_invoiced_edit', $this->data);
    }

    public function saveReport(Request $request) {
        $data = [
            'fatturato' => $request->fatturato
        ];
        Report::where('id_rapporto', $request->id)->update($data);
        return response()->json('succes', 200);
    }

    public function downloadEXC($id) {
        $this->data=null;
        if(!$id==null) {
            $reports = Report::select('*')->where(['id_intervento'=>$id])->first();

            $nr_rapporto=$reports['nr_rapporto'];
            $id_rapporto=$reports['id_rapporto'];
            $tipo_rapporto=$reports['tipo_rapporto'];
            $desc_intervento=$reports['descrizione_intervento'];
            $nr_ore=$reports['altri_ore'];


            $check_nr = $tipo_rapporto == 1 ? $nr_rapporto . 'C' : (
            $tipo_rapporto == 3 ? $reports['nr_rapporto'] . 'V' : $reports['nr_rapporto']);

            $report_mat = ReportEquipment::select('*')->where(['id_rapporto'=>$id_rapporto])->first();
            $reports_mat = ReportEquipment::select('*')->where(['id_rapporto'=>$id_rapporto])->get();
            $quantita_=$report_mat['quantita'];

            $price=26.00*intval($quantita_);

            $this->data=[
                'report'=>array('nr_rap'=>$nr_rapporto,'id_interv'=>$id_rapporto,'desc_interv'=>$desc_intervento,'nr_ore'=>$nr_ore,'quantity'=>$quantita_),
                'reports_mat'=>$reports_mat,
                'first_row_price'=>$price
            ];
            return Excel::download(new ExportReport("xls_reports.righe_documento_sample", $this->data), 'Righe documento '.$check_nr.'.xls');
        } else {
            echo 'Id is missing';
        }
    }


    public function download_pdf($id){

        $reportPdf = new ReportPdf($id);

        return $reportPdf->stream();
    }
    public function show_pdf($id){

        $reportPdf = new ReportPdf($id);

        return $reportPdf->stream();
    }


    public function see_html_test($id){
        return view('report_pdf_template');
    }

/*    public function downloadPDF($id) {
        $report = Report::where('id_rapporto', $id)->with(['operation' => function($query) {
            return $query->with('client');
        }])->first();

        $operation = Operation::where('id_intervento', $report->id_intervento)->first();
        $mean = Mean::where('id_mezzo', $report->id_mezzo)->first();
        $equipment = ReportEquipment::where('id_rapporto', $report->id_rapporto)->with(['equipment'])->get();
        $client = Clienti::where('id', $operation->id_clienti)->first();
        $photos = ReportPhoto::where('id_rapporto', $report->id_rapporto)->get();

        $tehnicians = preg_split('/;/', $operation->tecnico);
        $tehnician = CoreUsers::select('id_user', 'name', 'family_name')->where('id_user', $tehnicians[0])->first();

        $clientName = $client->azienda == 1 ? $client->ragione_sociale : $client->cognome . ' ' . $client->nome;
        $clientAddress = strtoupper($client->nazione_sl) != 'ITALIA' ? ($client->comune_sl ? $client->comune_sl .' - ' : '' .$client->nazione_sl)
            : $client->comune_sl.' - '.$client->provincia_sl;
        $numberOfReport = $report->tipo_rapporto == 1 ? $report->nr_rapporto . ' / C' : (
            $report->tipo_rapporto == 3 ? $report->nr_rapporto . ' / V' : $report->nr_rapporto);
        $data = [
            'report' => $report,
            'operation' => $operation,
            'mean' => $mean,
            'equipment' => $equipment,
            'client' => $client,
            'photos' => $photos,
            'tehnician' => $tehnician,
            'clientName' => $clientName,
            'clientAddress' => $clientAddress,
            'numberOfReport' => $numberOfReport,
            'workedHours' => $this->getArrayOfWorkedDays($report->id_intervento)
        ];
        $filename = 'Rapporto ' . $numberOfReport . ' ' . $clientName . ' ' . date('d-m-Y', strtotime($report->data_inizio)) . '.pdf';
        $pdf = PDF::setOptions([
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('report_pdf_template', $data);
        return $pdf->download($filename);
    }*/

    private function getArrayOfWorkedDays($id) {
        $operation = Operation::where('id_intervento', $id)->with(['reports', 'internalWorks'])->first();
        $array = [];
        if(count($operation->internalWorks)) {
            foreach($operation->internalWorks as $internalWork) {
                $array[] = [
                    'start' => $internalWork->data_ora_inizio,
                    'end' => $internalWork->data_ora_fine,
                    'extra' => 0,
                    'key' => 1
                ];
            }
        }
        $hoursByDates = [];
        $totalMinutes = 0;
        foreach($array as $item) {
            $dateStart = date('Y-m-d', strtotime($item['start']));
            $dateEnd = date('Y-m-d', strtotime($item['end']));
            $oneDay = $dateStart == $dateEnd;
            $dateNow = $dateStart;
            $loop = true;
            if($dateStart <= $dateEnd) {
                while($loop) {
                    if($dateNow == $dateEnd) {
                        $minutes = $oneDay ? intval((strtotime($item['end']) - strtotime($item['start'])) / 60) : 
                            intval((strtotime($item['end']) - strtotime($dateNow . ' 08:00:00')) / 60);
                        $totalMinutes += $minutes;
                        $minToShow = $minutes % 60;
                        $hours = intval($minutes / 60);
                        if(isset($hoursByDates[$dateNow])) {
                            $hoursByDates[$dateNow] = [
                                'hours' => $hoursByDates[$dateNow]['hours'] + $hours,
                                'minutes' => $hoursByDates[$dateNow]['minutes'] + $minToShow,
                                'type' => 2
                            ];
                        } else {
                            $hoursByDates[$dateNow] = [
                                'hours' => $hours,
                                'minutes' => $minToShow,
                                'type' => 2
                            ];
                        }
                        $loop = false;
                    } elseif($dateStart == $dateNow) {
                        $minutes = $oneDay ? intval((strtotime($item['end']) - strtotime($item['start'])) / 60) : 
                            intval((strtotime($dateNow . ' 17:00:00') - strtotime($item['start'])) / 60);
                        $totalMinutes += $minutes;
                        $minToShow = $minutes % 60;
                        $hours = intval($minutes / 60);
                        if(isset($hoursByDates[$dateNow])) {
                            $hoursByDates[$dateNow] = [
                                'hours' => $hoursByDates[$dateNow]['hours'] + $hours,
                                'minutes' => $hoursByDates[$dateNow]['minutes'] + $minToShow,
                                'type' => 2
                            ];
                        } else {
                            $hoursByDates[$dateNow] = [
                                'hours' => $hours,
                                'minutes' => $minToShow,
                                'type' => 2
                            ];
                        }
                        $dateNow = date('Y-m-d', strtotime($dateNow) + 3600 * 24);
                    } else {
                        $minutes = $oneDay ? intval((strtotime($item['end']) - strtotime($item['start'])) / 60) : 
                            intval((strtotime($dateNow . ' 17:00:00') - strtotime($dateNow . ' 08:00:00')) / 60);
                        $totalMinutes += $minutes;
                        $minToShow = $minutes % 60;
                        $hours = intval($minutes / 60);
                        if(isset($hoursByDates[$dateNow])) {
                            $hoursByDates[$dateNow] = [
                                'hours' => $hoursByDates[$dateNow]['hours'] + $hours,
                                'minutes' => $hoursByDates[$dateNow]['minutes'] + $minToShow,
                                'type' => 2
                            ];
                        } else {
                            $hoursByDates[$dateNow] = [
                                'hours' => $hours,
                                'minutes' => $minToShow,
                                'type' => 2
                            ];
                        }
                        $dateNow = date('Y-m-d', strtotime($dateNow) + 3600 * 24);
                    }
                }
            }
        }

        if(count($operation->reports)) {
            foreach($operation->reports as $report) {
                $date = date('Y-m-d', strtotime($report->data_inizio));
                $totalMinutes += $report->altri_ore * 60;
                if(isset($hoursByDates[$date])) {
                    $hoursByDates[$date] = [
                        'hours' => $hoursByDates[$date]['hours'] + $report->altri_ore,
                        'minutes' => $hoursByDates[$date]['minutes'] + 0,
                        'type' => 1
                    ];
                } else {
                    $hoursByDates[$date] = [
                        'hours' => $report->altri_ore,
                        'minutes' => 0,
                        'type' => 1
                    ];
                }
            }
        }
        $minToShow = $totalMinutes % 60;
        $minToShow = $minToShow > 9 ? $minToShow : '0' . $minToShow;
        $hours = intval($totalMinutes / 60);
        $hours = $hours > 9 ? $hours : '0' . $hours;
        return (object)[
            'array' => $hoursByDates,
            'total' => $hours . ':' . $minToShow
        ];
    }

    public function newReport() {
        $this->data['operations'] = Operation::get();
        $this->data['means'] = Mean::get();
        return view('reports.new_report', $this->data);
    }

    public function saveReportManually(Request $request) {
        try {
            $dataOperation = [
                'stato' => isset($request->stato) && $request->stato ? $request->stato : 1,
            ];
            Operation::where('id_intervento', $request->id_intervento)->update($dataOperation);
    
            if(isset($request->firma) && $request->firma) {
                $firma = Storage::disk('local')->putFile('signatures/' . $request->id_intervento, $request->firma);
            }

            $numberOfReport = $this->getNumberOfReport($request);
    
            $dataReport = [
                'id_intervento' => isset($request->id_intervento) && $request->id_intervento ? $request->id_intervento : null,
                'id_mezzo' => isset($request->id_mezzo) && $request->id_mezzo ? $request->id_mezzo : null,
                'data_inizio' => isset($request->data_ora_inizio) && $request->data_ora_inizio ? $request->data_ora_inizio : null,
                'data_fine' => isset($request->data_ora_fine) && $request->data_ora_fine ? $request->data_ora_fine : null,
                'difetto' => isset($request->difetto) && $request->difetto ? $request->difetto : '',
                'descrizione_intervento' => isset($request->descrizione_intervento) && $request->descrizione_intervento ? $request->descrizione_intervento : '',
                'altri_note' => isset($request->altri_note) && $request->altri_note ? $request->altri_note : '',
                'altri_ore' => isset($request->altri_ore) && $request->altri_ore ? $request->altri_ore : 0,
                'firma' => isset($request->firma) && $firma ? $firma : null,
                'data_invio' => isset($request->data_ora_invio) && $request->data_ora_invio ? $request->data_ora_invio : null,
                'fatturato' => 0,
                'nr_rapporto' => $numberOfReport->nr,
                'tipo_rapporto' => $numberOfReport->type,
            ];

            if(isset($request->id_rapporto) && $request->id_rapporto) {
                //firma delete image
                Report::where('id_rapporto', $request->id_rapporto)->update($dataReport);
                $idReport = $request->id_rapporto;
            } else {
                Report::create($dataReport);
                $lastReport = Report::select('id_rapporto')->orderBy('id_rapporto', 'desc')->first();
                $idReport = $lastReport->id_rapporto;
            }

            if(isset($request->rapporti_foto) && count($request->rapporti_foto)) {
                foreach($request->rapporti_foto as $photo) {
                    
                    $reportPhoto = Storage::disk('local')->putFile('photos/' . $request->id_intervento, $photo);
    
                    $dataReportPhoto = [
                        'id_rapporto' => $idReport,
                        'filename' => $reportPhoto
                    ];
                    ReportPhoto::create($dataReportPhoto);
                }
            }
            
            if(isset($request->rapporti_materiali) && count($request->rapporti_materiali)) {
                foreach($request->rapporti_materiali as $equipment) {
                    $dataReportEquipment = [
                        'id_rapporto' => $idReport,
                        'id_materiali' => $equipment->id_materiali,
                        'quantita' => $equipment->quantita
                    ];
                    ReportPhoto::create($dataReportPhoto);
                }
            }
    
            return redirect('new_report');
        } catch(\Exception $e) {
            return dd($e);
        }
    }

    private function getNumberOfReport($request) {
        if($request->id_rapporto) {
            $report = Report::where('id_rapporto', $request->id_rapporto)->first();
            return $report->nr_rapporto;
        }

        $operation = Operation::where('id_intervento', $request->id_intervento)->first();

        $type = $operation->conto_di == 3 ? 1 : ($operation->tipo == 1 ? 2 : ($operation->tipo == 2 ? 3 : 4));

        $report = Report::where('tipo_rapporto', $type)->orderBy('nr_rapporto', 'desc')->first();
        return (object)[
            'nr' => $report ? ++$report->nr_rapporto : 1,
            'type' => $type
        ];
    }

    public function updateHours(Request $request) {
        $r = $request['params'];
        if($r['id_intervento'] && ($r['from'] < $r['to']) && $r['from'] != null && $r['to'] != null){
            $getintervento = Report::where('id_intervento',$r['id_intervento'])->first();
            $dateinizio =  date('Y-m-d', strtotime($getintervento->data_inizio));
            $datefine =  date('Y-m-d', strtotime($getintervento->data_fine));
            $datainizioupdate = $dateinizio . ' ' . $r['from'] .':00';
            $datafinepdate = $datefine . ' ' . $r['to'] .':00';
            $data = [
                'data_inizio' => $datainizioupdate,
                'data_fine' => $datafinepdate,
            ];
            $updateintervento = Report::where('id_intervento',$r['id_intervento'])->update($data);
        } elseif ($r['id_lavori_interne'] && ($r['from'] < $r['to']) && $r['from'] != null && $r['to'] != null) {
            $getintervento = InternalWork::where('id_lavori_interni', $r['id_lavori_interne'])->first();
            $dateinizio =  date('Y-m-d', strtotime($getintervento->data_ora_inizio));
            $datefine =  date('Y-m-d', strtotime($getintervento->data_ora_fine));
            $datainizioupdate = $dateinizio . ' ' . $r['from'] .':00';
            $datafinepdate = $datefine . ' ' . $r['to'] .':00';
            $data = [
                'data_ora_inizio' => $datainizioupdate,
                'data_ora_fine' => $datafinepdate,
            ];
            $updateintervento = InternalWork::where('id_lavori_interni',$r['id_lavori_interne'])->update($data);
        } else {
            return response()->json(['data' => 'Wrong date'], 100);
        }
        
            return response()->json(['data' => 'succes'], 200);
    }
}