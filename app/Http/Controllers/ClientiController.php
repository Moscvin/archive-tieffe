<?php

namespace App\Http\Controllers;

use App\Models\Addresses\Nazioni;
use App\Models\Clienti;
use App\Models\Location;
use App\Models\Machinery;
use App\Models\Operation\OperationMachinery;
use App\Models\Operation\Operation;
use App\CoreUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Excel;
use File;
use URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Storage;
use App\Models\Addresses\Comuni;
use App\Models\Addresses\Province;
use App\Helpers\VatNumber;
use App\Repositories\ClientRepository;

class ClientiController extends MainController
{
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function index()
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('clienti.customers', $this->data);
    }

    public function ajax(Request $request)
    {

        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $data = $this->clientRepository->getFiltered($request->all(), $chars);
        $index = 0;
        $data_json = [];

        foreach ($data->items as $items) {
            $locationFinal = '';
            $locationVia = '';
            $locationComune = '';
            $i = 0;
            $len = count($items->location);
            $phoneArray = [];
            foreach($items->location as $location){

                if ($i == $len - 1) {
                    $locationFinal .= $location->tipologia;

                }
                else  {
                    $locationFinal .= $location->tipologia . ', ';
                }

                if(isset($location->telefono1) && $location->telefono1){

                    $phoneArray[] = $location->telefono1;

                }

                if(isset($location->telefono2) && $location->telefono2){

                    $phoneArray[] = $location->telefono2;

                }
                $i++;
            }
            $locationVia = $items->location->implode('indirizzo_via', '|');

            $locationFinal = trim($locationFinal, ',') . ' - ' . $locationVia;
            $locationComune = $items->location->implode('indirizzo_comune','|');
            $phoneArray = implode(', ', $phoneArray);

            $data_json[] = [
                "{$items->id}",
                "{$items->ragione_sociale}",
              //  "{$items->codice_gestionale}",
                "{$items->partita_iva}",
                "{$items->codice_fiscale}",
                "{$phoneArray}",
                "{$locationFinal}",
                "{$locationVia}",
                "{$locationComune}",


            ];
            if (in_array("V", $chars)) {
                array_push($data_json[$index], "<a href=\"/customer_add/$items->id/view\" class=\"btn btn-xs btn-primary\" title=\"Visualizza\"><i class=\"fa fa-eye\"></i></a>");
            }
            if (in_array("E", $chars)) {
                array_push($data_json[$index], "<a href=\"/customer_add/$items->id\" class=\"btn btn-xs btn-info\" title=\"Modifica\"><i class=\"fa fa-edit\"></i></a>");
            }
            if (in_array("L", $chars)) {
                $lock_btn = ($items->cliente_visibile == 1) ? 'warning' : 'primary';
                $lock_icon = ($items->cliente_visibile == 1) ? 'lock' : 'unlock';
                $title_attr = ($items->cliente_visibile == 1) ? 'Nascondi' : 'Mostra';
                array_push($data_json[$index], "<button onclick='lockBlock(this)' title=\"$title_attr\"  data-my-id=\"$items->id\" data-my-current=\"$items->cliente_visibile\"  type=\"button\" class=\"action_block btn btn-xs btn-$lock_btn\" ><i class=\"fa fa-$lock_icon\"></i></button>");
            }

            if (in_array("D", $chars)) {
                array_push($data_json[$index], "<button onclick='deleteBlock(this)' data-my-id=\"$items->id\" type=\"button\" class=\"action_del btn btn-xs btn-warning\" title=\"Elimina\"><i class=\"fa fa-trash\"></i></button>");
            }
            $index++;
        }

        return response()->json([
            'draw' => $request->draw ?? 1,
            'recordsTotal' => $data->recordsTotal,
            'recordsFiltered' => $data->recordsFiltered,
            "data" => $data_json,
        ]);

    }

    public function add($id = null, Request $request)
    {
        $this->data['page'] = [];
        $this->data['nazionie'] = Nazioni::all();
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);

        if (Input::get('save')) {
            return $this->save($id, $request);
        }

        $this->data['exists'] = false;

        $tableItems = collect();
        if (!empty($id)) {
            $this->data['page'] = Clienti::where('id', $id)->with('location.machineries.operationMachineries.operation')->first();
            foreach ($this->data['page']->location as $location) {
                foreach ($location->machineries as $machinery) {
                    foreach ($machinery->operationMachineries as $operation) {
                        if($operation->operation){
                            $this->data['exists'] = true;
                        }
                        if($operation->operation){
                          $tableItems[$operation->operation->id_intervento] = (object) [
                            'id_intervento' => $operation->operation->id_intervento,
                            'tipologia' => $operation->operation->tipologia,
                            'data' => $operation->operation->data,
                            'machinery' => (object) [
                              'descrizione' => $machinery->descrizione
                            ],
							'technicians' => count($operation->operation->technicians()) ? $operation->operation->technicians()->pluck('fullname')->implode(', ') : ''
							,
                            'location' => (object) [
                              'indirizzo_via' => $location->indirizzo_via
                            ],
                            'note' => $operation->operation->note,
                            'stato' => ($operation->operation->stato == 0 || $operation->operation->stato == 1)? 'Pianificato'
                            : (
                                ($operation->operation->stato == 2)? 'Completato'
                                : (
                                  ($operation->operation->stato == 3)? 'Non completato' : null
                                  )
                              )
                          ];

                      }

                    }
                }
            }
        }
        else
          $this->data['page'] = (object) [ 'id' => null, 'location' => null ];

        if($this->data['exists']) $tableItems = collect($tableItems)->sortByDesc('data');
        $this->data['tableItems'] = $tableItems;




        $this->data['backRoute'] = $request->backRoute ?? '/customers';
        $this->data['userToken'] = auth()->user()->app_token;
        return view('clienti.customer_add', $this->data);
    }

    public function save($id = null, $request)
    {
        $validator = \Validator::make($request->all(), [
            'ragione_sociale' => 'required|string',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }
        $data = [
            'ragione_sociale' => $request->ragione_sociale,
            'partita_iva' => $request->partita_iva,
            'codice_fiscale' => $request->codice_fiscale,
            'note' => $request->note,
            'partita_iva_errata' => VatNumber::isNotValid($request->partita_iva),
            'committente' => $request->committente,
            'alldata' => !(empty($request->partita_iva) && empty($request->codice_fiscale)),
        ];


        if (empty($id)) {
            $createdClient = Clienti::create($data);
            Session::flash('success', 'Il cliente è aggiunto!');
            $id = $createdClient->id;
        } else {
            Clienti::where('id', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state salvate correttamente!');
        }
        return redirect("/customer_add/$id?backRoute=" . ($request->backRoute ?? '/customers'));
    }


    public function block()
    {
        $id = Input::get('id');
        $block = (int)Input::get('block');
        if (!empty($id)) {
            $data = [
                'cliente_visibile' => ($block != 1) ? 1 : 0
            ];
            Clienti::where('id', $id)->update($data);
        }
        return response()->json(array('statut' => 'Success'), 200);
    }

    public function delete()
    {
        $id = Input::get('id');
        if (!empty($id)) {
            $getClient = Clienti::where('id', $id)->with('location')->first();
            if ($getClient) {
                foreach ($getClient->location as $location) {
                    if ($location) {
                        $operations = Operation::where('id_sede', $location->id_sedi)->get();
                        $operations->each(function ($item) {
                            $item->delete();
                        });

                        Machinery::where('id_sedi', $location->id_sedi)->delete();
                    }
                }
                Location::where('id_cliente', $id)->delete();
            }
            Clienti::where('id', $id)->delete();
        }
        return response()->json(array('statut' => 'Success'), 200);
    }

    public function search($id, Request $request)
    {
        $data = [];
        if ($id == 1) {
            $clienti = Clienti::where('ragione_sociale', 'like', '%' . $request->value . '%')->get();
            foreach ($clienti as $c) {
                $data[] = [
                    'data' => $c->ragione_sociale
                ];
            }
            return response()->json(['result' => $data]);

        } elseif ($id == 2) {
            $clienti = Clienti::where('partita_iva', 'like', $request->value . '%')->get();
            foreach ($clienti as $c) {
                $data[] = [
                    'data' => $c->partita_iva
                ];
            }
            return response()->json(['result' => $data]);
        } elseif ($id == 3) {
            $clienti = Clienti::where('codice_fiscale', 'like', $request->value . '%')->get();
            foreach ($clienti as $c) {
                $data[] = [
                    'data' => $c->codice_fiscale
                ];
            }
            $unique = collect($data)->unique('data');
            return response()->json(['result' => $unique->values()->all()]);
        } elseif ($id == 4) {
            $clienti = Clienti::whereHas('location', function ($query) use ($request) {
                $query->where('indirizzo_via', 'like', '%' . $request->value . '%');
            })->get();

            $indirizzoViaFinal = '';
            foreach ($clienti as $c) {
                $len = count($c->location);
                $i = 0;
                foreach ($c->location as $location) {
                    if ($i == $len - 1)
                        $indirizzoViaFinal = $location->indirizzo_via;
                    else
                        $indirizzoViaFinal = $c->location->where('indirizzo_via', 'like', '%' . $request->value . '%');

                }
            }

            $data[] = [
                'data' => $indirizzoViaFinal
            ];
            return response()->json(['result' => $data]);
        } elseif ($id == 5) {
            $clienti = Clienti::whereHas('location', function ($query) use ($request) {
                $query->where('indirizzo_comune', 'like', '%' . $request->value . '%');
            })->get();

            $indirizzoComuneFinal = '';
            foreach ($clienti as $c) {
                $len = count($c->location);
                $i = 0;
                foreach ($c->location as $location) {
                    if ($i == $len - 1)
                        $indirizzoComuneFinal = $location->indirizzo_comune;
                    else
                        $indirizzoComuneFinal = $c->location->where('indirizzo_comune', 'like', '%' . $request->value . '%');

                }
            }


            $data[] = [
                'data' => $indirizzoComuneFinal
            ];
            return response()->json(['result' => $data]);


        } elseif($id == 6 ){
          $clienti = Clienti::whereHas('location', function ($query) use ($request) {
              $query->where(DB::raw('CONCAT_WS(", ", telefono1, telefono2)'), 'like', '%' . $request->value . '%');
          })->get();


          $telefonoFinal = '';
          foreach ($clienti as $c) {
              $len = count($c->location);
              $i = 0;
              foreach ($c->location as $location) {
                  if ($i == $len - 1)
                      $telefonoFinal = (!empty($location->telefono1) && !empty($location->telefono2)? implode(', ',[$location->telefono1, $location->telefono2]) :
                  (!empty($location->telefono1)? $location->telefono1 : (!empty($location->telefono2)? $location->telefono2: "")));
                  else
                      $telefonoFinal = $c->location->where('telefono1', 'like', '%' . $request->value . '%')
                                                   ->where('telefono2', 'like', '%' . $request->value . '%');

              }
          }


          $data[] = [
              'data' => $telefonoFinal
          ];

          return response()->json(['result' => $data]);
        }
         else {
            return response()->json(['app' => 'error'], 400);
        }
        return $id;
    }

    protected function replaceDiacriticeChar($word)
    {
        if (strlen($word) === 0) {
            return $word;
        }
        $diacriticeChar = ['ă', 'â', 'î', 'ş', 'ţ'];
        $withoutDiacriticeChar = ['a', 'a', 'i', 's', 't'];
        return strtolower(str_replace($diacriticeChar, $withoutDiacriticeChar, $word));
    }

    public function download(Request $request)
    {
        $clienti = Clienti::whereIn('id', explode(",", $request->ids))->get();
        foreach ($clienti as $page) {
            $dataArray[] = [
                'Cod. cliente' => $page->id,
                'Denominazione' => $page->ragione_sociale,
                'Partita IVA' => $page->partita_iva,
                'Codice Fiscale' => $page->codice_fiscale,
                'Visibile' => ($page->cliente_visibile == 1) ? 'Si' : 'No'
            ];
        }
        $exp = Excel::import(function ($excel) use ($dataArray) {
            $excel->setTitle('Clienti table');
            $excel->sheet('clienti', function ($sheet) use ($dataArray) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Cod. cliente');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('Denominazione');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('Partita IVA');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('Codice Fiscale');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('Visibile');
                });
                $sheet->cells('A1:E1', function ($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });
                $sheet->fromArray($dataArray, null, 'A2', false, false);
            });
        });
        $file = storage_path('exports/' . $exp['file']);
        return response()->download($file)->deleteFileAfterSend(true);
    }

    public function saveClient(Request $request)
    {
        $r = $request['params'];
        $client = $r['client'];
        if (isset($client)) {
            $client = Clienti::create($client);
            $name = $client->azienda == 1 ? $client->ragione_sociale : $client->cognome . ' ' . $client->nome;

            $data[] = [
                'name' => str_limit($name, 35, '...'),
                'id' => $client->id
            ];

            $message = 'Il cliente è aggiunto!';
            Session::flash('success', $message);
            return response()->json([
                'success' => $message,
                'clienti' => $data]);
        }
    }

    public function getComuneNascita(Request $request)
    {
        $r = $request['params'];
        $value = strtoupper($r['value']);
        $comuni = Comuni::where('comune', 'like', '%' . $value . '%')->get();
        $comuni = $comuni->load('provice');

        return response()->json(['comuni' => $comuni]);
    }

    public function getComuneNascitaByCap(Request $request)
    {
        $r = $request['params'];
        $value = strtoupper($r['value']);
        if (isset($r['comune_sl'])) {
            $comune_sl = $r['comune_sl'];
            $comuni = Comuni::where('cap', 'like', '%' . $value . '%')->where('comune', 'like', '%' . $comune_sl . '%')->get();
        } else {
            $comuni = Comuni::where('cap', 'like', '%' . $value . '%')->get();
        }

        $comuni = $comuni->load('provice');

        return response()->json(['comuni' => $comuni]);
    }


    public function getIntervention(Request $request,$idUser, $id)
    {
        try {
            $user = CoreUsers::find($idUser);
            $operation = Operation::where('id_intervento', $id)->with(['invoice_client', 'headquarter.client', 'machineries.machinery'])->first();
            $response = [
                'id_intervento' => $operation->id_intervento,
                'data' => $operation->data,
                'tipologia' => $operation->tipologia,
                'ora_dalle' => $operation->ora_dalle,
                'ora_alle' => $operation->ora_alle,
                'file' => !empty($operation->file) ? URL::to('/file/' . $operation->file) : null,
                'cliente' => (object)[
                    'cliente_id' => $operation->headquarter->client->id ?? 0,
                    'cliente_denominazione' => $operation->headquarter->client->ragione_sociale ?? '',
                    'note' => $operation->headquarter->client->note ?? '',
                    'alldata_cliente' => !(empty($operation->headquarter->client->partita_iva) &&
                    empty($operation->headquarter->client->codice_fiscale)),
                    'cliente_codice_fiscale' => $operation->headquarter->client->codice_fiscale ?? '',
                    'cliente_partita_iva' => $operation->headquarter->client->partita_iva ?? '',
                ],
                'sede' => (object)[
                    'id_sede' => $operation->headquarter->id_sedi ?? 0,
                    'telefono1' => $operation->headquarter->telefono1 ?? '',
                    'telefono2' => $operation->headquarter->telefono2 ?? '',
                    'denominazione' => $operation->headquarter->address ?? '',
                    'email' => $operation->headquarter->email ?? '',
                    'lat' => $operation->lat,
                    'long' => $operation->long,
                    'indirizzo' => (object) [
                      'indirizzo_via' => $operation->headquarter->indirizzo_via,
                      'indirizzo_civico' => $operation->headquarter->indirizzo_civico,
                      'indirizzo_cap' => $operation->headquarter->indirizzo_cap,
                      'indirizzo_comune' => $operation->headquarter->indirizzo_comune,
                      'indirizzo_provincia' => $operation->headquarter->indirizzo_provincia,
                    ],
                    'tipologia' => $operation->headquarter->tipologia,
                    'alldata_sede' => (boolean) $operation->headquarter->alldata,
                ],
                'descrizione_intervento' => $operation->machineries->implode('desc_intervento', ', ') ?? '',
                //'star' => $this->isResponsible($user->id, $operation->techniciansArray) ? 1 : 0,
                'stato' => $operation->stato,
                'urgente' => $operation->urgente ?? 0,
                'updated_at' => (string)$operation->updated_at,
                'non_assegnati' => $operation->pronto_intervento,
                'fatturare_a' => $operation->fatturare_a > 0 ? $operation->invoice_client->ragione_sociale : 'Cliente',
                'note' => $operation->note,
                'rapporto' => $operation->report->id_rapporto ?? 0,
                'technicians' => $operation->technicians()->map(function($item) {
                    return (object)[
                        'id_user' => $item->id_user,
                        'name' => $item->fullName
                    ];
                }),
                'dafatturare' => $operation->dafatturare,
                'incasso' => $operation->incasso,
                'cestello' => $operation->cestello
            ];

            if($operation->machineries->count() > 0){
               $response['machineries'] = [
                   'id_macchinario' => $operation->machineries->first()->id_macchinario,
                   'descrizione' => $operation->machineries->first()->machinery->descrizione,
                   'modello' => $operation->machineries->first()->machinery->modello,
                   'matricola' => $operation->machineries->first()->machinery->matricola,
                   'anno' => $operation->machineries->first()->machinery->anno,
                   'note' => $operation->machineries->first()->machinery->note,
                   'attivo' => $operation->machineries->first()->machinery->attivo,
                   'tetto' => $operation->machineries->first()->machinery->tetto,
                   '2tecnici' => $operation->machineries->first()->machinery['2tecnici'],
                   'SF_split' => $operation->machineries->first()->machinery->SF_split,
                   'SF_canalizzato' => $operation->machineries->first()->machinery->SF_canalizzato,
                   'SF_predisp_presente' => $operation->machineries->first()->machinery->SF_predisp_presente,
                   'SF_imp_el_presente' => $operation->machineries->first()->machinery->SF_imp_el_presente,
                   'SF_mq_locali' => $operation->machineries->first()->machinery->SF_mq_locali,
                   'SF_altezza' => $operation->machineries->first()->machinery->SF_altezza,
                   'SF_civile' => $operation->machineries->first()->machinery->SF_civile,
                   'SF_indust_commer' => $operation->machineries->first()->machinery->SF_indust_commer,
                   'SC_posizione_cana' => $operation->machineries->first()->machinery->SC_posizione_cana,
                   'SC_certif_canna' => $operation->machineries->first()->machinery->SC_certif_canna,
                   'SC_cana_da_intubare' => $operation->machineries->first()->machinery->SC_cana_da_intubare,
                   'SC_metri' => $operation->machineries->first()->machinery->SC_metri,
                   'SC_materiale' => $operation->machineries->first()->machinery->SC_materiale,
                   'SC_ind_com' => $operation->machineries->first()->machinery->SC_ind_com,
                   'SC_tondo_oval' => $operation->machineries->first()->machinery->SC_tondo_oval,
                   'SC_sezione' => $operation->machineries->first()->machinery->SC_sezione,
                   'SC_tetto_legno' => $operation->machineries->first()->machinery->SC_tetto_legno,
                   'SC_distanze' => $operation->machineries->first()->machinery->SC_distanze,
                   'SC_curve' => $operation->machineries->first()->machinery->SC_curve,
                   'SC_passotetto' =>  $operation->machineries->first()->machinery->SC_passotetto,
                   'F_anno_aquisto' => $operation->machineries->first()->machinery->F_anno_aquisto,
                   'F_tipo_gas' => $operation->machineries->first()->machinery->F_tipo_gas,
                   'C_costruttore' => $operation->machineries->first()->machinery->C_costruttore,
                   'C_matr_anno' => $operation->machineries->first()->machinery->C_matr_anno,
                   'C_nominale' => $operation->machineries->first()->machinery->C_nominale,
                   'C_combustibile' => $operation->machineries->first()->machinery->C_combustibile,
                   'C_tiraggio' => $operation->machineries->first()->machinery->C_tiraggio,
                   'C_uscitafumi' => $operation->machineries->first()->machinery->C_uscitafumi,
                   'C_libretto' => $operation->machineries->first()->machinery->C_libretto,
                   'C_LA_locale' => $operation->machineries->first()->machinery->C_LA_locale,
                   'C_LA_idoneo' => $operation->machineries->first()->machinery->C_LA_idoneo,
                   'C_LA_presa_aria' => $operation->machineries->first()->machinery->C_LA_presa_aria,
                   'C_LA_presa_aria_idonea' => $operation->machineries->first()->machinery->C_LA_presa_aria_idonea,
                   'C_KRA_dimensioni' => $operation->machineries->first()->machinery->C_KRA_dimensioni,
                   'C_KRA_materiale' => $operation->machineries->first()->machinery->C_KRA_materiale,
                   'C_KRA_coibenza' => $operation->machineries->first()->machinery->C_KRA_coibenza,
                   'C_KRA_curve90' => $operation->machineries->first()->machinery->C_KRA_curve90,
                   'C_KRA_lunghezza' => $operation->machineries->first()->machinery->C_KRA_lunghezza,
                   'C_KRA_idoneo' => $operation->machineries->first()->machinery->C_KRA_idoneo,
                   'C_CA_tipo' => $operation->machineries->first()->machinery->C_CA_tipo,
                   'C_CA_materiale' => $operation->machineries->first()->machinery->C_CA_materiale,
                   'C_CA_sezione' => $operation->machineries->first()->machinery->C_CA_sezione,
                   'C_CA_dimensioni' => $operation->machineries->first()->machinery->C_CA_dimensioni,
                   'C_CA_lunghezza' => $operation->machineries->first()->machinery->C_CA_lunghezza,
                   'C_CA_cam_raccolta' => $operation->machineries->first()->machinery->C_CA_cam_raccolta,
                   'C_CA_cam_raccolta_ispez' => $operation->machineries->first()->machinery->C_CA_cam_raccolta_ispez,
                   'C_CA_curve90' => $operation->machineries->first()->machinery->C_CA_curve90,
                   'C_CA_curve45' => $operation->machineries->first()->machinery->C_CA_curve45,
                   'C_CA_curve15' => $operation->machineries->first()->machinery->C_CA_curve15,
                   'C_CA_piombo' => $operation->machineries->first()->machinery->C_CA_piombo,
                   'C_CA_liberaindipendente' => $operation->machineries->first()->machinery->C_CA_liberaindipendente,
                   'C_CA_innesti' => $operation->machineries->first()->machinery->C_CA_innesti,
                   'C_CA_rotture' => $operation->machineries->first()->machinery->C_CA_rotture,
                   'C_CA_occlusioni' => $operation->machineries->first()->machinery->C_CA_occlusioni,
                   'C_CA_corpi_estranei' => $operation->machineries->first()->machinery->C_CA_corpi_estranei,
                   'C_CA_cambiosezione' => $operation->machineries->first()->machinery->C_CA_cambiosezione,
                   'C_CA_restringe' => $operation->machineries->first()->machinery->C_CA_restringe,
                   'C_CA_diventa' => $operation->machineries->first()->machinery->C_CA_diventa,
                   'C_CA_provatiraggio' => $operation->machineries->first()->machinery->C_CA_provatiraggio,
                   'C_CA_tiraggio' => $operation->machineries->first()->machinery->C_CA_tiraggio,
                   'C_CA_tettolegno' => $operation->machineries->first()->machinery->C_CA_tettolegno,
                   'C_CA_distanze_sicurezza' => $operation->machineries->first()->machinery->C_CA_distanze_sicurezza,
                   'C_CA_certificazione' => $operation->machineries->first()->machinery->C_CA_certificazione,
                   'C_KCO_dimensioni' => $operation->machineries->first()->machinery->C_KCO_dimensioni,
                   'C_KCO_forma' => $operation->machineries->first()->machinery->C_KCO_forma,
                   'C_KCO_cappelloterminale' => $operation->machineries->first()->machinery->C_KCO_cappelloterminale,
                   'C_KCO_zonareflusso' => $operation->machineries->first()->machinery->C_KCO_zonareflusso,
                   'C_KCO_graditetto' => $operation->machineries->first()->machinery->C_KCO_graditetto,
                   'C_KCO_accessotetto' => $operation->machineries->first()->machinery->C_KCO_accessotetto,
                   'C_KCO_comignolo' => $operation->machineries->first()->machinery->C_KCO_comignolo,
                   'C_KCO_tipocomignolo' => $operation->machineries->first()->machinery->C_KCO_tipocomignolo,
                   'C_KCO_idoncomignolo' => $operation->machineries->first()->machinery->C_KCO_idoncomignolo,
                   'C_KCO_cestello' => $operation->machineries->first()->machinery->C_KCO_cestello,
                   'C_KCO_ponteggio' => $operation->machineries->first()->machinery->C_KCO_ponteggio,
                   'tipo_impianto' => $operation->machineries->first()->machinery->tipo_impianto,
               ];
               $response['machineries']['alldata_macchinario'] = $operation->machineries->first()->machinery->alldata;
            }

            if(
              empty($response['sede']->telefono1) &&
              empty($response['sede']->indirizzo->indirizzo_via) &&
              empty($response['sede']->indirizzo->indirizzo_civico) &&
              empty($response['sede']->indirizzo->indirizzo_cap) &&
              empty($response['sede']->indirizzo->indirizzo_comune) &&
              empty($response['sede']->indirizzo->indirizzo_provincia)
            )
              $response['sede']->alldata_sede = false;




            return response()->json([
                'status' => 'ok',
                'data' => $response,
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }


}
