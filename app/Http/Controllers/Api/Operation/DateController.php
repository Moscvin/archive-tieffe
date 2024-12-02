<?php

namespace App\Http\Controllers\Api\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoreUsers;
use App\Models\Operation\Operation;
use App\Models\Report\Report;

class DateController extends Controller
{
    public function main(Request $request)
    {
        try {
            $dateTo = $request->dateTo ? $request->dateTo : date('Y-m-d', strtotime($request->dateFrom . ' + 1 week'));
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
            $operations = Operation::with(['headquarter.client', 'machineries.machinery'])
                ->where('tecnico', 'like', '%'.$user->id_user . '%')
                ->where('data', '>=', $request->dateFrom)
                ->where('data', '<=', $dateTo)
                ->where('stato', '!=', 0)
                ->where('pronto_intervento', 0)
                ->orderBy('data', 'asc')
                ->orderBy('ora_dalle', 'asc')
                ->get();
            $data = [];
            foreach($operations as $operation) {
                if(in_array($user->id_user, preg_split('/;/', $operation->tecnico))) {
                    $new_data = [
                        'id_intervento' => $operation->id_intervento,
                        'data' => $operation->data,
                        'tipologia' => $operation->tipologia,
                        'ora_dalle' => $operation->ora_dalle,
                        'ora_alle' => $operation->ora_alle,
                        'cestello' => $operation->cestello,
                        'cliente' => (object)[
                            'cliente_id' => $operation->headquarter->client->id ?? 0,
                            'cliente_denominazione' => $operation->headquarter->client->ragione_sociale ?? ''
                        ],
                        'sede' => (object)[
                            'id_sede' => $operation->headquarter->id_sedi ?? 0,
                            'telefono1' => $operation->headquarter->telefono1 ?? '',
                            'telefono2' => $operation->headquarter->telefono2 ?? '',
                            'denominazione' => $operation->headquarter->address ?? '',
                            'lat' => $operation->lat,
                            'long' => $operation->long,
                        ],
                        'descrizione_intervento' => $operation->machineries->implode('desc_intervento', ', ') ?? '',
                        'non_assegnati' => $operation->pronto_intervento,
                        'star' => $this->isResponsible($user->id_user, $operation->tecnico) ? 1 : 0,
                        'stato' => $operation->stato,
                        'urgente' => $operation->urgente ?? 0,
                        'updated_at' => (string)$operation->updated_at,
                        'rapporto' => $operation->report->id_rapporto ?? 0,
                        'incasso' => $operation->incasso
                    ];
                    if($operation->machineries->count() > 0)
                       $new_data['machineries'] = (object)[
                           'id_macchinario' => $operation->machineries->first()->id_macchinario,
                           'descrizione' => $operation->machineries->first()->machinery->descrizione,
                           'modello' => $operation->machineries->first()->machinery->modello,
                           'matricola' => $operation->machineries->first()->machinery->matricola,
                           'anno' => $operation->machineries->first()->machinery->anno,
                           'note' => $operation->machineries->first()->machinery->note,
                           'attivo' => $operation->machineries->first()->machinery->attivo,
                           'tetto' => $operation->machineries->first()->machinery->tetto,
                           '2tecnici' => $operation->machineries->first()->machinery['2tecnici'],
                       ];
                    $data[] = $new_data;
                }
            }
            return response()->json([
                'status' => 'ok',
                'data' => $data,
                'dateFrom' => $request->dateFrom,
                'dateTo' => $dateTo,
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    private function isResponsible($idUser, $techniciansArray) {
        return $idUser == $techniciansArray[0] ?? 0;
    }
}
