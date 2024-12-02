<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InternalWork;
use App\Models\OrdersWork;
use App\Models\Orders;
use App\Models\Operation;
use App\Models\Clienti;
use App\CoreUsers;

class BuyersController extends Controller {
    public function list(Request $request) {
        try {
            

            $committenti_list = [];


            $Clients = Clienti::where('committente', 1)->orderBy('ragione_sociale', 'asc')->get();


            foreach($Clients as $Client){

                $committenti_list[] = [
                    'id' => isset($Client->id) ? $Client->id : "",
                    'name' => (isset($Client->ragione_sociale) ? $Client->ragione_sociale : "")
                ];

            }

            return response()->json([
                'commitenti_list' => $committenti_list
            ], 200);



        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

}
