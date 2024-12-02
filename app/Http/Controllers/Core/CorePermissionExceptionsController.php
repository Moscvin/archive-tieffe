<?php

namespace App\Http\Controllers\Core;

use App\CoreGroups;
use App\CoreMenu;
use App\CorePermissions;
use App\CorePermissionsExceptions;
use App\CoreUsers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MainController;

class CorePermissionExceptionsController extends MainController
{
    public function __construct()
    {
        parent::__construct();
    }


    public function core_permission_exceptions()
    {

      $this->data['permissionsexceptions'] = CorePermissionsExceptions::with('core_permissions_user')->distinct()->select('id_user')->get();

      return view('core.core_permission_exceptions',$this->data);

    }
    public function core_permission_exceptions_add(Request $request,$id)
    {
      $this->data['pages'] = app(\App\Http\Controllers\MainController::class)->menu_user();
      $this->data['user'] = CoreUsers::findOrFail($id);
      $this->data['groups'] = CoreGroups::all();
      //$this->data['perm'] = CorePermissions::where('id_menu_item','2')->get();
      $this->data['perm'] = CorePermissions::all();

      $this->data['permissionsexceptions'] = CorePermissionsExceptions::with('core_permissions_user')->where('id_user',$id)->get();

      return view('core.core_permission_exceptions_add',$this->data);
      //  return dd($this->data['user']);
    }

  public function core_permission_exceptions_edit(Request $request)
  {
      $permissions_exception = json_decode($request->permission);

      foreach ($permissions_exception as $permission){

          $permission->value = (empty($permission->value)) ? '' : $permission->value;
          $data =[
              'permission'=>$permission->value,
              'id_user'=>$permission->id_user,
              'id_menu_item'=>$permission->id_menu_item,
          ];
          if ($permission->id_perm_expt == 0){

              CorePermissionsExceptions::create($data);

          }else{
              CorePermissionsExceptions::where([
                  'id_permission_exception' => $permission->id_perm_expt,
                  'id_menu_item' => $permission->id_menu_item,
                  'id_user' => $permission->id_user
              ])->update(['permission'=>$permission->value]);
          }
      }

      Session::flash('success', 'Eccezioni salvati con successo!');
      return response()->json(['status'=>true],200);

  }

  public function core_permission_exceptions_del(Request $request)
  {
      if ($request->isMethod('post')) {
          $done = CorePermissionsExceptions::where('id_user',Input::get('id'))->delete();

          return response()->json($done);
      }


  }




}
