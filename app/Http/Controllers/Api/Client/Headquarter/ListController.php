<?php

namespace App\Http\Controllers\Api\Client\Headquarter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Location;

class ListController extends Controller
{
    public function main(Request $request, $idClient)
    {
        try {
            $headquarters = Location::where('id_cliente', $idClient)->with('machineries')->get()->map(function($item) {
                return [
                    'id_sede' => $item->id_sedi,
                    'tipologia' => $item->tipologia,
                    'address' => $item->address,
                    'machineries' => $item->machineries->map(function($item) {
                        return [
                            'id_macchinario' => $item->id_macchinario,
                            'descrizione' => $item->descrizione,
                            'modello' => $item->modello,
                            'matricola' => $item->matricola,
                            'tetto' => $item->tetto,
                            '2tecnici' => $item['2tecnici']                            
                        ];
                    })
                ];
            });
            return response()->json([
                'success' => 'ok',
                'data' => $headquarters
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'success' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
