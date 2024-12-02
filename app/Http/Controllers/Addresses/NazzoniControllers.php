<?php

namespace App\Http\Controllers\Addresses;

use App\Models\Addresses\Nazioni;
use Illuminate\Http\Request;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class NazzoniControllers extends MainController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->data['pages'] = Nazioni::all();
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('addresses.nazioni',$this->data);
    }
    public function ajax()
    {
        $this->data['pages'] = Nazioni::all();
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        $index =0;

        foreach ($this->data['pages'] as $items){
            $data_json [] = [
                "{$items->nazione}",
                "{$items->sigla_nazione}",
            ];
            if (in_array("E", $chars)){

                array_push($data_json[$index],"<a href=\"/nazioni_add/$items->id_nazione\" class=\"btn btn-xs btn-info\" title=\"Modifica\"><i class=\"fa fa-edit\"></i></a>");
            }
            if (in_array("D", $chars)){

                array_push($data_json[$index],"<button onclick='deleteBlock(this)' data-my-id=\"$items->id_nazione\" type=\"button\" class=\"action_del btn btn-xs btn-warning\" title=\"Elimina\"><i class=\"fa fa-trash\"></i></button>");
            }

            $index++;
        }
        return response()->json([
            'draw'=>1,
            'recordsTotal'=>$this->data['pages']->count(),
            "data"=>$data_json
        ]);
    }

    public function add($id = null, Request $request){
        $this->data['pages'] = [];

        if(Input::get('save'))
            return $this->save($id, $request);

        if(!empty($id)) {

            $this->data['pages'] = Nazioni::where('id_nazione', '=', $id)
                ->limit(1)
                ->get();

            if ($this->data['pages']->count() == 0){
                return  view('error_autorization');
            }

        }


        return view('addresses.nazioni_add', $this->data);
    }

    public function save($id=null, $request){
        $validator = \Validator::make($request->all(), [
            'nazione' => 'required',
            'sigla_nazione' => 'required|max:2  ',
        ]);



        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = [
            'nazione'=>$request->nazione,
            'sigla_nazione'=>strtoupper($request->sigla_nazione),

        ];

        if(empty($id)){

            $action_page = Nazioni::create($data);
            Session::flash('success', 'Nazioni aggiunta correttamente!');

        } else {

            $action_page = Nazioni::where('id_nazione', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state correttamente salvate!');

        }

        return redirect('/nazioni');
    }

    public function delete()
    {
        $id = Input::get('id');
        if(!empty($id)){

            Nazioni::where('id_nazione', '=' ,$id)->limit(1)->delete();

        }

        return response()->json(array('statut'=> 'Success'), 200);
    }

}
