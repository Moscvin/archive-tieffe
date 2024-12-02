<?php

namespace App\Http\Controllers\Api\Operation;

use App\Helpers\GeoDecoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;

class NewController extends Controller
{
    public function main(Request $request)
    {
        try {
            $coors = (new GeoDecoder($this->getAddress($request->id_sede)))->getCoors();
            $operation = Operation::create([
                'data' => date('Y-m-d'),
                'tipologia' => $request->tipologia,
                'ora_dalle' => $request->ora_dalle,
                'ora_alle' => $request->ora_alle,
                'incasso' => $request->incasso,
                'urgente' => $request->urgente,
                'fatturare_a' => isset($request->fatturare_a) ? $request->fatturare_a : 0,
                'tecnico' => $request->tecnico,
                'pronto_intervento' => 0,
                'stato' => 1,
                'id_sede' => $request->id_sede,
                'long' => ($coors->lng > 6 && $coors->lng < 20)? $coors->lng : null,
                'lat' => ($coors->lat > 36 && $coors->lat < 50)? $coors->lat : null,
            ]);
            foreach($request->intervento_macchinari as $item) {
                OperationMachinery::create([
                    'id_intervento' => $operation->id_intervento,
                    'id_macchinario' => $item['id_macchinario'],
                    'desc_intervento' => $item['desc_intervento'] ?? '',
                ]);
            }
            return response()->json([
                'status' => 'ok',
                'id_intervento' => $operation->id_intervento
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getAddress($id)
    {
        return Location::where('id_sedi', $id)->first()->address ?? '';
    }
}
