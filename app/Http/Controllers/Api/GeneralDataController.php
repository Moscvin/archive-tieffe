<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\CoreUsers;
use App\Models\Equipment;
use App\Models\Mean;
use App\Models\Clienti;
use App\Models\Report;
use App\Models\ReportEquipment;
use App\Models\ReportPhoto;
use App\Models\Operation;

class GeneralDataController extends Controller
{
    public function getTehniciansByUserType(Request $request) {
        $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
        $tehnicians = CoreUsers::where('id_group', $user->id_group)->get();
        $data = [];
        foreach($tehnicians as $tehnician) {
            $data[] = (object)[
                'id_user' => $tehnician->id_user,
                'tecnico' => $tehnician->family_name . ' ' . $tehnician->name
            ];
        }
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function getEquipment(Request $request) {
        $equipment = Equipment::where('attivo', 1)->get();
        $data = [];
        foreach($equipment as $item) {
            $data[] = (object)[
                'id_materiali' => $item->id_materiali,
                'denominazione_materiali' => $item->denominazione_materiali
            ];
        }
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function getMeans(Request $request) {
        $means = Mean::where('attivo', 1)->get();
        $data = [];
        foreach($means as $mean) {
            $data[] = (object)[
                'id_mezzo' => $mean->id_mezzo,
                'targa' => $mean->targa,
                'marca' => $mean->marca,
            ];
        }
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function getClients(Request $request) {
        $clients = Clienti::where([['cliente_visibile', 1], ['ragione_sociale', 'like', '%' . $request->value . '%']])->get();
        $data = [];
        foreach($clients as $client) {
            $data[] = (object)[
                'id_client' => $client->id,
                'denominazione' =>  $client->ragione_sociale,
            ];
        }
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    public function rapporti(Request $request ) {
        $validator = \Validator::make($request->all(), [
            'id_rapporto' => 'required|integer',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status'=>'error'
            ], 400);
        } else {
            $materiale = ReportEquipment::where('id_rapporto', $request->id_rapporto)->get();
            if($materiale) {
                $m = array();
                $foto = array();
                $ph = ReportPhoto::where('id_rapporto',$request->id_rapporto)->get();
                foreach($ph as $p){
                    $foto[] = 'https://tieffe.altuofianco.com/images/'.$p->filename;
                }
                foreach($materiale as $mt){
                    $den = Equipment::where('id_materiali',$mt->id_materiali)->first();
                    $m[] = (object)[
                        "id_materiale" => $mt->id_materiali,
                        "quantita" => $mt->quantita,
                        "denominazione_materiali" => $den->denominazione_materiali,
                    ];
                }
           
                $rapporti = Report::where('id_rapporto', $request->id_rapporto)->first(['id_mezzo', 'data_inizio', 'data_fine', 'difetto', 'descrizione_intervento',
                    'altri_note', 'data_invio', 'fatturato', 'nr_rapporto', 'tipo_rapporto', 'altri_ore']);
                $getintervent = Report::where('id_rapporto',$request->id_rapporto)->first();
                $interv = Operation::where('id_intervento',$getintervent->id_intervento)->first();
                return response()->json([
                        'materiali' => $m,
                        'rapporti' => $rapporti,
                        'foto' => $foto,
                        'stato' => $interv->stato,
                        'conto_di' =>  $interv->conto_di,
                        'created_at' => date('Y-m-d H:i:s', strtotime($interv->created_at)),
                    ], 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);  
            } else {
                return response()->json([
                    'status'=>'error'
                ],400);
            }                        
        }
    }
}