<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Clienti;
use App\Models\Operation\Operation;
use App\CoreUsers;
use App\Models\Addresses\Nazioni;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Excel;
use File;
use Storage;
use PDF;
use App\CoreAdminOptions;
use App\Models\Operation\OperationMachinery;
use App\Helpers\GeoDecoder;
use App\Models\Location;
use App\Helpers\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cookie;

class InterventiController extends Controller
{
    public function show_pdf($filename = null)
    {
      if(!$filename)header('Location: /index.php');

        $pdf = \PDF::setOptions([
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ]);

       $path = storage_path("app/public/rapporti_pdf/$filename");
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    public function index(Request $request)
    {
        // if($request->headers->get('referer') == URL::to('/').'/monitoring')
        //   dd(Cookie::get('monitoringDate'));
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        $this->data['nazionie'] = Nazioni::all();

        $invoicesTo = Clienti::where('committente', 1)->orderBy('ragione_sociale', 'asc')->get();

        $this->data['invoicesTo'] = $invoicesTo;
        $this->data['client_id'] = Input::get('client_id');
        return view('interventi.nuovo_intervento', $this->data);
    }

    public function clientSearch(Request $request)
    {
        $clients = Clienti::where([['ragione_sociale', 'like', '%' . $request->value . '%'], ['cliente_visibile', 1]])->select([
            'id', 'ragione_sociale as name'
        ])->orderBy('ragione_sociale', 'asc')->get();
        return response()->json($clients, 200);
    }
    public function clientSearchId(Request $request)
    {
        $client = Clienti::where([['id', '=', $request->value ], ['cliente_visibile', 1]])->select([
            'id', 'ragione_sociale as name'
        ])->first();
        return response()->json($client, 200);
    }

    public function getOperationsByDate(Request $request)
    {
        $date = $request->date;
        $response = [];

        if($date) {
            $operations = Operation::where('data', $date)->orderBy('ora_dalle', 'asc')->get();

            $response = $operations->map(function($operation) {
                return [
                    'clientName' => $operation->headquarter->client->ragione_sociale ?? '',
                    'tipologia' => $operation->tipologia,
                    'ora_dalle' => date('H:i', strtotime($operation->ora_dalle)),
                    'ora_alle' => date('H:i', strtotime($operation->ora_alle)),
                    'cestello' => $operation->cestello,
                    'incasso' => $operation->incasso,
                    'technicians' => $operation->technicians()->map(function($item) {
                        return [
                            'id' => $item->id_user,
                            'fullName' => $item->fullName
                        ];
                    }),
                    'location' => $operation->headquarter->address ?? '',
                ];
            });
        }
        return response()->json($response, 200);
    }

    public function getTechnicians(Request $request)
    {
        $technicians = CoreUsers::select('id_user', 'name', 'family_name', 'lat', 'lng', 'coord_updated_at')->where([['isactive', 1], ['id_group', 9]])->get();
        return response()->json($technicians, 200);
    }

    public function saveInterventi(Request $request)
    {
        try {
            $requestOperation = json_decode($request->operation);

            $technicians = '';
            for($i = 1; $i < 4; $i++) {
                if($requestOperation->{"technician_$i"} ?? false) {
                    $technicians .= ($requestOperation->{"technician_$i"} . ';');
                }
            }
            $headquarter = Location::where('id_sedi', $requestOperation->headquarter)->first();
            $coors = (new GeoDecoder('Italy '.  $headquarter->address))->getCoors();
            $operation = Operation::create([
                'tipologia' => (string) $requestOperation->tipologia !== ""?
                               (string) $requestOperation->tipologia : null,
                'ora_dalle' => (string) $requestOperation->ora_dalle !== ""?
                               (string) $requestOperation->ora_dalle : null,
                'ora_alle' => (string) $requestOperation->ora_alle !== ""?
                          (string) $requestOperation->ora_alle : null,
                'cestello' => $requestOperation->cestello,
                'incasso' => $requestOperation->incasso,
                'data' => $requestOperation->status ? $requestOperation->date : null,
                'urgente' => $requestOperation->urgent ?? 0,
                'stato' => $requestOperation->status,
                'tecnico' => $technicians,
                'note' => $requestOperation->note,
                'id_sede' => $requestOperation->headquarter,
                'file' => $request->file('file') ?
                    $request->file('file')->storeAs('/operation', $request->file('file')->getClientOriginalName())
                    : null,
                'long' => $coors->lng,
                'lat' => $coors->lat,
                'fatturare_a' => $requestOperation->invoiceTo,
                'pronto_intervento' => 0
            ]);

            foreach($requestOperation->machineries as $machinery) {
                if($machinery->id) {
                    OperationMachinery::create([
                        'id_intervento' => $operation->id_intervento,
                        'id_macchinario' => $machinery->id,
                        'desc_intervento' => $machinery->description
                    ]);
                }
            }


            $this->sendNotificationToAllTechnicians();


            return response()->json([
                'operation' => $operation
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function sendNotificationToAllTechnicians()
    {
        $targets = CoreUsers::where([['id_group', 9], ['isactive', 1], ['notification_token', '<>', '']])->whereNotNull('notification_token')->pluck('notification_token');
        $notification = new Notification($targets, 'Nuovo intervento');
        $notification->send();
    }
}
