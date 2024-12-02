<?php

namespace App\Http\Controllers\Api\Client;

use App\Helpers\VatNumber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clienti as Client;
use App\Http\Requests\Api\Client\NewClientRequest;

class NewController extends Controller
{
    public function main(NewClientRequest $request)
    {
        try {
            $client = Client::where('partita_iva', $request->iva)->when($request->cf, function($q) use ($request) {
                $q->orWhere('codice_fiscale', $request->cf);
            })->first();
            if($client) {
                $status = 'exist';
            } else {
                $client = Client::create([
                    'ragione_sociale' => $request->rs,
                    'partita_iva' => $request->iva,
                    'codice_fiscale' => $request->cf,
                    'cliente_visibile' => 1,
                    'partita_iva_errata' => VatNumber::isNotValid($request->iva),
                    'alldata' => !empty($request->rs) || !empty($request->iva) || !empty($request->cf)
                ]); 
                $status = 'ok';
            }
            return response()->json([
                'success' => $status,
                'id_client' => $client->id
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'success' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
