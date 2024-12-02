<?php

namespace App\Http\Controllers\Core;

use App\CoreGroups;
use App\CoreMenu;
use App\CorePermissions;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\MainController;

class CorepermissionController extends MainController
{

    public function __construct()
    {
        parent::__construct();
    }


    public function core_permission()
    {
        //$this->data['pages'] = CoreMenu::with('core_permission')->get();
        $this->data['pages'] = app(\App\Http\Controllers\MainController::class)->menu_user();


        $this->data['groups'] = CoreGroups::get();

        return view('core.core_permission', $this->data);
    }

    public function core_permission_update(Request $request) 
    {
        $permissions = json_decode($request->permission);

        foreach ($permissions as $permission){

            $permission->value = (empty($permission->value)) ? '' : $permission->value;
            $data =[
                'permission'=>$permission->value,
                'id_group'=>$permission->group,
                'id_menu_item'=>$permission->menu,
            ];

            if ($permission->value == ''){
                CorePermissions::where('id_permission',$permission->id)->update(['permission'=>'']);
            }
            $count_gm = CorePermissions::where('id_group',$permission->group)->where('id_menu_item',$permission->menu);
            if ($permission->id == 0 ){

                if ($count_gm->count() == 1){

                    $count_gm->update(['permission'=>$permission->value]);

                }else{

                    CorePermissions::create($data);
                }

            }else{
                CorePermissions::where('id_permission',$permission->id)->update($data);
            }
        }

        return response()->json(array('statut'=> 'ok'), 200);
    }



    public function core_permission_add() {

        $id = Input::get('id');
        if(!empty($id)){

            /*CorePermissions::where('id_permission', '=' ,$id)->limit(1)->delete();*/

        }

        return response()->json(array('statut'=> 'Success'), 200);

    }
    





}