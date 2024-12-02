<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InternalWork;
use App\Models\OrdersWork;
use App\Models\Orders;
use App\Models\Operation;
use App\CoreUsers;

class OrdersController extends Controller {
    public function list(Request $request) {
        try {

            $Orders = new Orders();
            $Orders = $Orders->where('attiva', 1)->orderBy('nome','asc')->get();

            $commesse_list = [];

            foreach($Orders as $Order){


                $hours = floor($Order->ore_lavorate);
                $mins = round(($Order->ore_lavorate - $hours) * 60);

                $commesse_list[] = [
                    'id_commessa' => isset($Order) ? $Order->id_commessa : "",
                    'nome' => (isset($Order->nome) ? $Order->nome : "")
                ];

            }

            return response()->json([
                'commesse_list' => $commesse_list
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
