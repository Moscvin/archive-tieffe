<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\CoreUsers;
use App\Models\Operation\Operation;
use App\Models\Report;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
        return response()->json([
            'status' => 'ok',
            'data' => $this->getTodayOperations($user),
            'no_report' => $this->getNoReportOperations($user)
        ], 200);
    }

    public function getOperationsByDate(Request $request)
    {
        $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
        $operations = Operation::with(['headquarter.client', 'machineries.machinery'])->where([['tecnico', 'like', '%' . $user->id_user . '%'], ['data', 'like', $request->date . '%']])->get();
        $data = [];

        foreach ($operations as $operation) {
            $tehniciansArray = preg_split('/;/', $operation->tecnico);
            $reportid = Report::where('id_intervento', $operation->id_intervento)->first();
            if (in_array($user->id_user, preg_split('/;/', $operation->tecnico))) {
                $new_data = [
                    'id_intervento' => $operation->id_intervento,
                    'data' => $operation->data,
                    'ora_dalle' => $operation->ora_dalle,
                    'ora_alle' => $operation->ora_alle,
                    'tipologia' => $operation->tipologia,

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
                    'star' => $this->isResponsible($user->id_user, $operation->techniciansArray),
                    'stato' => $operation->stato,
                    'urgente' => $operation->urgente ?? 0,
                    'updated_at' => (string)$operation->updated_at,
                    'rapporto' => $operation->report->id_rapporto ?? 0,
                    'note' => $operation->note
                ];

                if ($operation->machineries->count() > 0)
                    $new_data['machineries'] = (object)[
                        'id_macchinario' => $operation->machineries->first()->id_macchinario,
                        'descrizione' => $operation->machineries->first()->machinery->descrizione ?? '',
                        'modello' => $operation->machineries->first()->machinery->modello ?? '',
                        'matricola' => $operation->machineries->first()->machinery->matricola ?? '',
                        'anno' => $operation->machineries->first()->machinery->anno ?? 0,
                        'note' => $operation->machineries->first()->machinery->note ?? '',
                        'attivo' => $operation->machineries->first()->machinery->attivo ?? 1,
                        'tetto' => $operation->machineries->first()->machinery->tetto,
                        '2tecnici' => $operation->machineries->first()->machinery['2tecnici']
                    ];

                $data[] = $new_data;
            }
        }
        return response()->json([
            'status' => 'ok',
            'data' => $data,
        ], 200);
    }

    private function isResponsible($idUser, $techniciansArray)
    {
        return $idUser == ($techniciansArray[0] ?? 0) ? 1 : 0;
    }

    private function getNoReportOperations($user)
    {
        $operations = Operation::with(['headquarter.client', 'machineries.machinery'])->where([['tecnico', 'like', '%' . $user->id_user . '%'], ['stato', 1]])
            ->where('data', '<', date('Y-m-d'))->get();
        $response = [];
        foreach ($operations as $operation) {
            if ((in_array($user->id_user, $operation->techniciansArray) ||
                (!$operation->report) && $this->isResponsible($user->id_user, $operation->techniciansArray))) {
                $new_response = [
                    'id_intervento' => $operation->id_intervento,
                    'data' => $operation->data,
                    'ora_dalle' => $operation->ora_dalle,
                    'ora_alle' => $operation->ora_alle,
                    'tipologia' => $operation->tipologia,
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
                    'star' => $this->isResponsible($user->id_user, $operation->techniciansArray),
                    'stato' => $operation->stato,
                    'urgente' => $operation->urgente ?? 0,
                    'updated_at' => (string)$operation->updated_at,
                    'non_assegnati' => $operation->pronto_intervento,
                    'note' => $operation->note
                ];
                if ($operation->machineries->count() > 0)
                    $new_response['machineries'] = [
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

                $response[] = $new_response;
            }
        }
        return $response;
    }

    private function getTodayOperations($user)
    {
        $operations = Operation::with(['headquarter.client', 'machineries.machinery'])
            ->where(function ($q) use ($user) {
                $q->where([['tecnico', 'like', '%' . $user->id_user . '%'], ['stato', 1]])->where('data', date('Y-m-d'));
            })->get();
        $response = [];
        foreach ($operations as $operation) {
            if (in_array($user->id_user, $operation->techniciansArray)) {
                $response[] = [
                    'id_intervento' => $operation->id_intervento,
                    'data' => $operation->data,
                    'ora_dalle' => $operation->ora_dalle,
                    'ora_alle' => $operation->ora_alle,
                    'tipologia' => $operation->tipologia,
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
                    'star' => $this->isResponsible($user->id_user, $operation->techniciansArray),
                    'stato' => $operation->stato,
                    'urgente' => $operation->urgente ?? 0,
                    'updated_at' => (string)$operation->updated_at,
                    'non_assegnati' => $operation->pronto_intervento,
                    'note' => $operation->note
                ];
            }
        }
        return $response;
    }
}
