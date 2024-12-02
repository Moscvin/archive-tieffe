<?php

namespace App\Http\Controllers\Location;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Machinery;

class LocationMachineryController extends Controller
{
    public function list($id)
    {
        $response = [];

        $machineries = Machinery::where('id_sedi', $id)->get();
        $response['first_machinery'] = null;

        if(count($machineries) == 1){
            $response['first_machinery'] = $machineries[0];
        }

        $response['machineries'] = $machineries->map(function($item) {
            return (object) [
                'id' => $item->id_macchinario,
                'tipologia' => $item->tipologia,
                'description' => $item->fullDescription
            ];
        }); 

        return response()->json($response, 200);
    }
}
