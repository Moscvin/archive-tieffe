<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
/*use App\Staff;
use App\Company;*/
use App\Headquarter;
/*use App\Department;
use App\Stamping;*/
use App\CoreUsers;
use App\Models\Clienti;
use App\CoreAdminOptions;
use App\Models\Operation\Operation;
use Illuminate\Support\Facades\DB;

class InterventiMapController extends MainController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        $this->data = [];

        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $key = CoreAdminOptions::where('description', 'GOOGLE_MAPS_API_KEY')->first()->value;

        $pins = [];
        $pins = [
            'pin-1'=>asset('img/pins/pin-1.png'),
            'pin-2'=>asset('img/pins/pin-2.png'),
            'pin-3'=>asset('img/pins/pin-3.png'),
            'pin-4'=>asset('img/pins/pin-4.png')
        ];


        $legend_pins = [];
        $legend_pins = [
            'pin-1'=>asset('img/pins/legend/pin-01.png'),
            'pin-2'=>asset('img/pins/legend/pin-02.png')
        ];

        $technician_pins = [
            'pin-0'=>asset('img/pins/tech0.png'),
            'pin-1'=>asset('img/pins/tech1.png'),
            'pin-2'=>asset('img/pins/tech2.png'),
            'pin-3'=>asset('img/pins/tech3.png'),
            'pin-4'=>asset('img/pins/tech4.png'),
            'pin-5'=>asset('img/pins/tech5.png'),
        ];

        $this->data['chars'] = $chars;
        $this->data['pins'] = $pins;
        $this->data['legend_pins'] = $legend_pins;
        $this->data['technician_pins'] = $technician_pins;
        $this->data['key'] = $key;
        $this->data['today'] = date('d/m/Y', strtotime('today'));
        $this->data['month_ago'] = date('d/m/Y', strtotime('-1 month'));
        $invoicesTo = Clienti::where('committente', 1)->orderBy('ragione_sociale', 'asc')->get();
        $this->data['invoicesTo'] = $invoicesTo;

        return view('interventi.map', $this->data);
    }

    public function getMapInterventions(Request $request){

        $r = $request['params'];
        $date_start = $r['date_start'];
        $date_end = $r['date_end'];
        $curent_date = $r['curent_date'];
        $urgent = $r['urgent'];
        $tipologia = $r['tipologia'];
        $interventi = new Operation();
        $interventi = $interventi->with([
          'machineries'=>function($query){
            $query->with('machinery');
          },
          'location' => function($query){
            $query->WhereNotNull('indirizzo_via')->where('indirizzo_via','!=','');
            $query->WhereNotNull('indirizzo_cap')->where('indirizzo_cap','!=','');
            $query->WhereNotNull('indirizzo_comune')->where('indirizzo_comune','!=','');
            $query->WhereNotNull('indirizzo_provincia')->where('indirizzo_provincia','!=','');
            $query->WhereNotNull('indirizzo_civico')->where('indirizzo_civico','!=','');
            $query->with('client');
          }
        ])
        ->with('lastReportCanceled')->when($urgent !== null, function($query) use ($urgent) {
            $query->where('urgente', $urgent);
        })->where(function($q) use($r) {
            if($r['tipologia']) $q->where('tipologia', $r['tipologia']);
            $q->where(function($q) use($r) {
                $q->when($r['date_start'], function($query) use ($r){
                  return  $query->whereDate('data','>=', $r ['date_start']);
                })->when($r['date_end'], function($query) use ($r) {
                  return $query->whereDate('data','<=', $r['date_end']);
                });
            });
        })->where('stato', '!=', 0);

        $interventi = $interventi->where(function($query){
            $query->WhereNotNull('long')->WhereNotNull('lat');
        })->get();
        $response = [];
        if(count($interventi)) {
            foreach ($interventi as $key => $operation) {
                $response[$key]['statusText'] = $operation->tipologia;
                $response[$key]['className'] = $this->getClassName($operation);
                $response[$key]['address'] = $operation->headquarter->address ?? '';

                if($operation->tipologia) {
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
                $response[$key]['incasso'] = $operation->incasso;
                $response[$key]['cestello'] = $operation->cestello;
                $response[$key]['technician_1'] = $operation->techniciansArray[0] ?? '';
                $response[$key]['technician_2'] = $operation->techniciansArray[1] ?? '';
                $response[$key]['technician_3'] = $operation->techniciansArray[2] ?? '';
                $response[$key]['bodyStatus'] = $operation->a_corpo;
                $response[$key]['invoiceTo'] = $operation->fatturare_a;
                $response[$key]['note'] = $operation->note;
                $response[$key]['tipologia'] = $operation->tipologia;
                $response[$key]['file'] = $operation->file;
                $response[$key]['fileName'] = $operation->fileName;
                $response[$key]['path'] = $operation->file;
                $response[$key]['start'] = $operation->data;
                $response[$key]['urgente'] = $operation->urgente;
                $response[$key]['lat'] = $operation->lat;
                $response[$key]['long'] = $operation->long;

                $response[$key]['machineries'] = $operation->machineries->map(function($item) {
                    return [
                        'id' => $item->id_macchinario,
                        'description' => $item->fullDescription
                    ];
                });
            }
        }

        return response()->json([
            'interventi'=>$response
        ],200);
    }

    private function getClassName($operation)
    {
        switch($operation->stato) {
            case 0: {
                $className = 'light-red';
                break;
            }
            case 1: {
                if($operation->urgente == 1) {
                    $className = 'urgent';
                } elseif ($operation->data >= date('Y-m-d')) {
                    $className = "white";
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

    public function getCoordinates(Request $request) {
        $user = Auth::user();
        $user_group = $user->id_group;
        $person = Staff::where('id_user', $user->id_user)->first();
        $staff = Staff::when(isset($request->full_name), function ($q) use($request) {
            return $q->where(DB::raw('concat(cognome," ",nome)') , 'LIKE' , '%'.$request->full_name.'%');
        })->when(isset($request->id_azienda), function ($q) use($request) {
            return $q->where('personale.id_azienda', $request->id_azienda);
        })->when(isset($request->id_sede), function ($q) use($request) {
            return $q->where('personale.id_sede', $request->id_sede);
        })->when(isset($request->id_reparto), function ($q) use($request) {
            return $q->where('personale.id_reparto', $request->id_reparto);
        });

        if($user_group == 1 || $user_group == 8 || $user->id_group == 10) {
            $staffQuery = $staff;
        } elseif($user_group == 9 || $user_group == 12) {
            if(Company::where('id_responsabile', $person->id_personale)->orWhere('id_responsabile_amministrativi', $person->id_personale)->first()) {
                $staffQuery = $staff->join('aziende', 'personale.id_azienda', '=', 'aziende.id_azienda')->where('id_responsabile', $person->id_personale)
                    ->orWhere('id_responsabile_amministrativi', $person->id_personale);
            } elseif(Headquarter::where('id_responsabile_sede', $person->id_personale)->first()) {
                $staffQuery = $staff->join('sedi', 'personale.id_sede', '=', 'sedi.id_sede')->where('id_responsabile_sede', $person->id_personale);
            }
            elseif(Department::where('id_responsabile_reparto', $person->id_personale)->first()) {
                $staffQuery = $staff->join('reparti', 'personale.id_reparto', '=', 'reparti.id_reparto')->where('id_responsabile_reparto', $person->id_personale);
            }
            else {
                $staff->where('id_personale', $person->id_personale);
            }
        } else {
            $staff->where('id_personale', $person->id_personale);
        }

        $staff = $staff->get();
        $i = 0;
        $idEmployees = [];
        foreach($staff as $employee) {
            $idEmployees[$i++] = $employee->id_personale;
        }

        $from = $request->from ? \DateTime::createFromFormat('d/m/Y H:i:s', $request->from.' 00:00:00')->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
        $to = $request->to ? \DateTime::createFromFormat('d/m/Y H:i:s', $request->to.' 23:59:59')->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
        $stamps = Stamping::join('personale', 'timbrature.id_personale', '=', 'personale.id_personale')->select('entrata', 'lat', 'long', 'cognome', 'nome', 'orario')
            ->whereIn('timbrature.id_personale', $idEmployees)->whereBetween('orario', array($from, $to))->orderBy('orario', 'desc')->take(500)->get();
        if(count($staff) == 1) {
            $count = 1;
            $personAddress = [
                'lat' => +$staff[0]->lat_personale,
                'lng' => +$staff[0]->long_personale
            ];
            $personFullName = $staff[0]->cognome.' '.$staff[0]->nome;
            $company = Company::select('ragione_sociale')->where('id_azienda', $staff[0]->id_azienda)->first();
            $headquarter = Headquarter::select('denominazione_sede', 'lat_sede', 'long_sede')->where('id_sede', $staff[0]->id_sede)->first();
            $headquarterName = $company->ragione_sociale.', '.$headquarter->denominazione_sede;
            $headquarterAddress = [
                'lat' => +$headquarter->lat_sede,
                'lng' => +$headquarter->long_sede
            ];
        }
        else {
            $personAddress = null;
            $personFullName = null;
            $headquarterAddress = null;
            $headquarterName = null;
            $count = null;
        }
        $response = [
            'stamps' => $stamps,
            'count' => $count,
            'employee' => [
                'coors' => $personAddress,
                'fullName' => $personFullName
            ],
            'headquarter' => [
                'coors' => $headquarterAddress,
                'name' => $headquarterName
            ]
        ];
        return response()->json($response, 200);
    }
}
