<?php

namespace App\Http\Controllers\Api\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Operation\Operation;
use App\Models\Location;
use App\Helpers\GeoDecoder;
use App\Models\Operation\OperationMachinery;

class EditController extends Controller
{
    public function main(Request $request, $id)
    {
        try {
            $coors = (new GeoDecoder($this->getAddress($request->id_sede)))->getCoors();
            $operation_old = Operation::where('id_intervento', $id)->first();
            $data = [
                'data' => $request->data ?? $operation_old->data,
                'tipologia' => $request->tipologia ?? $operation_old->tipologia,
                'cestello' => $request->cestello ?? $operation_old->cestello,
                'incasso' => $request->incasso ?? $operation_old->incasso,
                'tecnico' => $request->tecnico,
                'long' => ($coors->lng > 6 && $coors->lng < 20)? $coors->lng : null,
                'lat' => ($coors->lat > 36 && $coors->lat < 50)? $coors->lat : null,
            ];
            if(isset($request->fatturare_a)){
                $data['fatturare_a'] = $request->fatturare_a;
            }

            $operation = Operation::where('id_intervento', $id)->update($data);
            if($request->id_macchinario !== 0)
              $this->updateMachineries($request->macchinario_descrizione, $request->id_macchinario ?? [], $id);
            return response()->json([
                'status' => 'ok',
                'id_intervento' => $id
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    private function updateMachineries($desc_machinery,$machinery_id, $id)
    {

            if($machinery_id) {
                if(OperationMachinery::where([
                  ['id_intervento', $id],
                  ['id_macchinario', $machinery_id]
                ])->first()) {
                    OperationMachinery::where([['id_intervento', $id],
                    ['id_macchinario', $machinery_id]])->update([
                        'desc_intervento' => $desc_machinery ?? ''
                    ]);
                } else {
                    OperationMachinery::create([
                        'id_intervento' => $id,
                        'id_macchinario' => $machinery_id,
                        'desc_intervento' => $desc_machinery ?? ''
                    ]);
                }
            }

        return true;
    }

    private function getAddress($id)
    {
        return Location::where('id_sedi', $id)->first()->address ?? '';
    }
}
