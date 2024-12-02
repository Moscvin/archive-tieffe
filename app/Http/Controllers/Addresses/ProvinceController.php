<?php

namespace App\Http\Controllers\Addresses;

use App\Models\Addresses\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class ProvinceController extends MainController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('addresses.province',$this->data);
    }
    public function ajax()
    {
        $this->data['pages'] = Province::all();
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        $index =0;

        foreach ($this->data['pages'] as $items){
            $data_json [] = [
                "{$items->sigla_provincia}",
                "{$items->nome_provincia}",
            ];
            if (in_array("E", $chars)){

                array_push($data_json[$index],"<a href=\"/province_add/$items->id_provincia\" class=\"btn btn-xs btn-info\" title=\"Modifica\"><i class=\"fa fa-edit\"></i></a>");
            }
            if (in_array("D", $chars)){

                array_push($data_json[$index],"<button onclick='deleteBlock(this)' data-my-id=\"$items->id_provincia\" type=\"button\" class=\"action_del btn btn-xs btn-warning\" title=\"Elimina\"><i class=\"fa fa-trash\"></i></button>");
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

            $this->data['pages'] = Province::where('id_provincia', '=', $id)
                ->limit(1)
                ->get();

            if ($this->data['pages']->count() == 0){
                return  view('error_autorization');
            }

        }


        return view('addresses.province_add', $this->data);
    }

    public function save($id=null, $request){
        $validator = \Validator::make($request->all(), [
            'sigla_provincia' => 'required',
            'nome_provincia' => 'required',
        ]);



        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = [
            'sigla_provincia'=>strtoupper($request->sigla_provincia),
            'nome_provincia' => $request->nome_provincia,
        ];

        if(empty($id)){

            $action_page = Province::create($data);
            Session::flash('success', 'Province aggiunta correttamente!');

        } else {

            $action_page = Province::where('id_provincia', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state correttamente salvate!');

        }

        return redirect('/province');
    }

    public function delete()
    {
        $id = Input::get('id');
        if(!empty($id)){

            Province::where('id_provincia', '=' ,$id)->limit(1)->delete();

        }

        return response()->json(array('statut'=> 'Success'), 200);
    }

}
