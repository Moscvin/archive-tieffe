<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Machinery;

class ClientLocationController extends Controller
{
    public function list($id)
    {
        $locations = Location::where('id_cliente', $id)->select([
            'id_sedi as id',
            'tipologia as type',
            'indirizzo_via as adress',
            'indirizzo_comune',
            'indirizzo_provincia',
            'indirizzo_civico',
            'indirizzo_cap',
            'telefono1 as phone',
            'telefono2 as phone2',
        ])->get();



        foreach($locations as $location){

            $locationFull = [];
            $location->type = $location->type ?? '';
            $location->adress = $location->adress ?? '';
            $phone = (!empty($location->phone) && !empty($location->phone2)) ?
              implode(', ',[$location->phone,$location->phone2]) :
              (!empty($location->phone)? $location->phone :
              (!empty($location->phone2)? $location->phone2 : null));

            $location->type ? array_push($locationFull, $location->type) : null;

            $address = '';

            if($location->adress) {
                $address .= ($location->adress . ' ' . $location->indirizzo_civico . ' ');
            }
            if($location->indirizzo_cap) {
                $address .= '(' . $location->indirizzo_cap . ') ';
            }
            if($location->indirizzo_comune) {
                $address .= $location->indirizzo_comune . ' ' . $location->indirizzo_provincia;
            }

            $address ? array_push($locationFull, $address) : null;
            $phone ? array_push($locationFull, $phone) : null;




            $location->fullData = implode(' - ', $locationFull);


        }

        //dd($locations);

        $response['locations'] = $locations;
        $response['first_machinery'] = null;
        $response['first_location'] = null;

        if(count($locations) == 1){
            $response['first_location'] = $locations[0]->id;

            $machineries = Machinery::where('id_sedi', $locations[0]->id)->get();

            if(count($machineries) == 1){

                $response['first_machinery'] = $machineries[0];

            }

        }

        return response()->json($response, 200);
    }
}
