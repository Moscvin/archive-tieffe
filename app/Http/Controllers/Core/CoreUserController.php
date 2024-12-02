<?php

namespace App\Http\Controllers\Core;

use App\CoreGroups;
use App\CoreUsers;
use App\CorePermissionsExceptions;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MainController;


class CoreUserController extends MainController
{
    public function __construct() {
        parent::__construct();
    }

    public function core_user() {
        $this->data['pages'] = CoreUsers::with('core_groups')->get();

        if(Input::get('status_search') == true){
            if(!empty(Input::get('value'))){

                $srch = CoreUsers::with('core_groups')->where('name','LIKE','%'.Input::get('value').'%')
                    ->orWhere('family_name','LIKE','%'.Input::get('value').'%')
                    ->orWhere('username','LIKE','%'.Input::get('value').'%')->get();
                $flag = CorePermissionsExceptions::where('id_user',$srch[0]->id_user)->count();

                if($flag == 0){
                    $search = $srch;
                }else{
                    $search = '';
                }
            } else {
                $search = '';
            }
            return response()->json($search);
        }

        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;

        return view('core.core_user', $this->data);
    }

    public function core_user_add($id = null, Request $request) {

        $this->data['pages'] = [];

        if(Input::get('save'))
            return $this->save_user($id, $request);

        if(!empty($id)) {

            $this->data['pages'] = CoreUsers::where('id_user', '=', $id)
                ->limit(1)
                ->with('core_groups')
                ->get();

        }

        $this->data['groups'] = CoreGroups::get();

        return view('core.core_user_add', $this->data);

    }

    public function save_user($id=null, $request)
    {
        $validator = \Validator::make($request->all(), [
            'id_group' => 'required|not_in:0',
            'family_name' => 'required|min:3',
            'email' => ($id) ? 'required|unique:core_users,email,'.$id.',id_user' : 'required|unique:core_users,email|min:3',
            'username' => ($id) ? 'unique:core_users,username,'.$id.',id_user' : 'required|unique:core_users,username|min:3',
        ]);

        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = [
            'isactive' => (Input::get('isactive')) ? Input::get('isactive') : 1,
            'name' => (Input::get('name')) ? Input::get('name') : '',
            'family_name' => (Input::get('family_name')) ? Input::get('family_name') : '',
            'username' => (Input::get('username')) ? Input::get('username') : '',
            'email' => (Input::get('email')) ? Input::get('email') : '',
            'id_group' => (Input::get('id_group')) ? Input::get('id_group') : 0,
            'password' => (Input::get('password')) ? Input::get('password') : str_random(10),

        ];

        if(empty($id)){

            $action_page = CoreUsers::create($data);
            $id = $action_page->id_user;
            Session::flash('success', 'L\'utente è stato aggiunto!');

        } else {
            unset($data['password']);
            $action_page = CoreUsers::where('id_user', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state correttamente salvate!');

        }

        return redirect('/core_user/');

    }

    public function core_user_block() {

        $id = Input::get('id');
        $block = (int)Input::get('block');
        if(!empty($id)){

            $data = [
                'isactive' => ($block != 1) ? 1 : 0
            ];

            CoreUsers::where('id_user', $id)->update($data);

        }

        return response()->json(array('statut'=> 'Success'), 200);

    }

    public function core_user_del() {
        $id = Input::get('id');
        if(!empty($id))
            CoreUsers::where('id_user', '=' ,$id)->delete();
        return response()->json(array('statut'=> 'Success'), 200);
    }

    public function app_user() {
        $this->data['pages'] = CoreUsers::with('core_groups')->where('id_group', '<>', 1)->get();

        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;

        return view('app_users.core_user', $this->data);
    }


    public function app_user_add($id = null, Request $request) {
        $this->data['pages'] = [];
        if(Input::get('save')) {
            return $this->save_app_user($id, $request);
        }

        if(!empty($id)) {
            $this->data['pages'] = CoreUsers::where([['id_user', '=', $id], ['id_group', '<>', 1]])
                ->limit(1)
                ->with('core_groups')
                ->get();
        }
        $this->data['groups'] = CoreGroups::where('id_group', '<>', 1)->get();

        return view('app_users.core_user_add', $this->data);
    }

    public function save_app_user($id=null, $request) {
        $validator = \Validator::make($request->all(), [
            'id_group' => 'required|not_in:[0,1]',
            'family_name' => 'required|min:3',
            'email' => ($id) ? 'required|unique:core_users,email,'.$id.',id_user' : 'required|unique:core_users,email|min:3',
            'username' => ($id) ? 'unique:core_users,username,'.$id.',id_user' : 'required|unique:core_users,username|min:3',
        ]);

        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $data = [
            'isactive' => (Input::get('isactive')) ? Input::get('isactive') : 1,
            'name' => (Input::get('name')) ? Input::get('name') : '',
            'family_name' => (Input::get('family_name')) ? Input::get('family_name') : '',
            'username' => (Input::get('username')) ? Input::get('username') : '',
            'email' => (Input::get('email')) ? Input::get('email') : '',
            'password' => (Input::get('password')) ? Input::get('password') : str_random(10),
        ];

        if(CoreGroups::where([['id_group', $request->id_group], ['id_group', '<>', 1]])->first()) {
            $data['id_group'] = $request->id_group;
        } else {
            $data['id_group'] = 9;
        }

        if(empty($id)){
            $action_page = CoreUsers::create($data);
            $id = $action_page->id_user;
            Session::flash('success', 'L\'utente è stato aggiunto!');

        } else {
            unset($data['password']);
            $action_page = CoreUsers::where('id_user', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state correttamente salvate!');

        }
        return redirect('/app_user/');
    }
}
