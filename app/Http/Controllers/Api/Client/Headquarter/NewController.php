<?php

namespace App\Http\Controllers\Api\Client\Headquarter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Http\Requests\Api\Headquarter\NewHeadquarterRequest;

class NewController extends Controller
{
    public function main(NewHeadquarterRequest $request, $idClient)
    {
        try {
            $headquarter = Location::create([
                'id_cliente' => $idClient,
                'tipologia' => $request->tipologia,
                'indirizzo_via' => $request->indirizzo_via,
                'indirizzo_civico' => $request->indirizzo_civico,
                'indirizzo_cap' => $request->indirizzo_cap,
                'indirizzo_comune' => $request->indirizzo_comune,
                'indirizzo_provincia' => $request->indirizzo_provincia,
                'telefono1' => $request->telefono1,
                'telefono2' => $request->telefono2,
                'email' => $request->email,
                'note' => $request->note,
                'attivo' => 1,
                'alldata' => !empty($request->telefono1) &&
                              !empty($request->indirizzo_via) &&
                              !empty($request->indirizzo_civico) &&
                              !empty($request->indirizzo_cap) &&
                              !empty($request->indirizzo_comune) &&
                              !empty($request->indirizzo_provincia),
            ]);

            return response()->json([
                'success' => 'ok',
                'id_sedi' => $headquarter->id_sedi
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'success' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
