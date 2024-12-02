<?php

namespace App\Http\Controllers\Core;

use App\CoreAdminOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MainController;

class CoreAdminOptionsController extends MainController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function core_admin_options(){

        $this->data['roles'] = CoreAdminOptions::get();

        return view('core.core_admin_options', $this->data);

    }

    public function core_admin_options_add($id = null, Request $request){

        $this->data['pages'] = [];

        if(Input::get('save'))
            return $this->save_core_admin_options($id, $request);

        if(!empty($id)) {

            $this->data['pages'] = CoreAdminOptions::where('id_option', '=', $id)
                ->limit(1)
                ->get();

        }

        return view('core.core_admin_options_add', $this->data);

    }

    public function core_admin_options_del($id = null) {

        $id = Input::get('id');
        if(!empty($id)){

            CoreAdminOptions::where('id_option', '=' ,$id)->limit(1)->delete();

        }

        return response()->json(array('statut'=> 'Success'), 200);

    }

    public function save_core_admin_options($id=null, $request){

        $validator = \Validator::make($request->all(), [
            'value' => 'required|min:1',
            'description' => 'required|min:1'
        ]);

        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = array();

        if(!empty(Input::get('description'))){
            $data = [
                'description' => (Input::get('description')) ? Input::get('description') : '',
                'value' => (Input::get('value')) ? Input::get('value') : '',
            ];
        }

        if(empty($id) && !empty($data)){

            $action_page = CoreAdminOptions::create($data);

            if($action_page){
                $id = $action_page->id_option;
                Session::flash('success', 'L\'opzioni è stato aggiunto!');
            }

        } else {

            if(!empty($data)){

                CoreAdminOptions::where('id_option', $id)->update($data);
                Session::flash('success', 'Le modifiche sono state correttamente salvate!');

            }

        }


        return redirect('/core_admin_options/');

    }

}
