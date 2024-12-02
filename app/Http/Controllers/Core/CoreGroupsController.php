<?php

namespace App\Http\Controllers\Core;

use App\CoreGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MainController;

class CoreGroupsController extends MainController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function core_groups(){

        $this->data['roles'] = CoreGroups::get();

        return view('core.core_groups', $this->data);

    }

    public function core_groups_add($id = null, Request $request){

        $this->data['pages'] = [];

        if(Input::get('save'))
            return $this->save_core_groups($id, $request);

        if(!empty($id)) {

            $this->data['pages'] = CoreGroups::where('id_group', '=', $id)
                ->limit(1)
                ->get();
            
        }

        return view('core.core_groups_add', $this->data);

    }

    public function core_groups_del() {

        $id = Input::get('id');
        if(!empty($id)){
            
            CoreGroups::where('id_group', '=' ,$id)->limit(1)->delete();

        }

        return response()->json(array('statut'=> 'Success'), 200);

    }

    public function save_core_groups($id=null, $request){

        $validator = \Validator::make($request->all(), [
            'description' => 'required|min:3'
        ]);

        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = array();

        if(!empty(Input::get('description'))){
            $data = [
                'description' => (Input::get('description')) ? Input::get('description') : '',
            ];
        }

        if(empty($id) && !empty($data)){

            $action_page = CoreGroups::create($data);

            if($action_page){
                $id = $action_page->id_group;
                Session::flash('success', 'Il gruppo è stato aggiunto!');
            }


        } else {

            if(!empty($data)){

                CoreGroups::where('id_group', $id)->update($data);
                Session::flash('success', 'Le modifiche sono state correttamente salvate!');

            }

        }

        return redirect('/core_groups/');

    }

}
