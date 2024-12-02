<?php
namespace App\Http\Controllers;
use App\Models\Equipment;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class EquipmentController extends MainController
{
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->data['pages'] = Equipment::get();

        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        return view('equipments.equipment', $this->data);
    }

    public function equipment_add($id = null, Request $request) {
        $this->data['page'] = [];

        if(Input::get('save')) {
            return $this->save_equipment($id, $request);
        }

        if(!empty($id)) {
            $this->data['page'] = Equipment::where('id_materiali', '=', $id)->first();
        }
        return view('equipments.equipment_add', $this->data);
    }

    private function save_equipment($id = null, $request) {
        $validator = \Validator::make($request->all(), [
            'denominazione_materiali' => 'required',
        ]);

        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = [
            'denominazione_materiali' => (Input::get('denominazione_materiali')) ? Input::get('denominazione_materiali') : '',
            'attivo' => Input::get('attivo'),
        ];

        if(empty($id)){
            Equipment::create($data);
            Session::flash('success', 'Il materiale è stato aggiunto!');

        } else {
            Equipment::where('id_materiali', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state correttamente salvate!');

        }
        return redirect('/equipment/');
    }

    public function equipment_block() {
        $id = Input::get('id');
        $block = (int)Input::get('block');
        if(!empty($id)){
            $data = [
                'attivo' => ($block != 1) ? 1 : 0
            ];
            Equipment::where('id_materiali', $id)->update($data);
        }

        return response()->json(array('statut'=> 'Success'), 200);
    }

    public function equipment_del() {
        $id = Input::get('id');
        if(!empty($id)){
            Equipment::where('id_materiali', '=' ,$id)->delete();
        }

        return response()->json(array('statut'=> 'Success'), 200);
    }
}