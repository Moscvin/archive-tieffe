<?php
namespace App\Http\Controllers\Core;

use App\CoreGroups;
use App\CoreMenu;
use App\CorePermissions;
use App\CorePermissionsExceptions;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MainController;

class CoreMenuController extends MainController
{

    public function __construct()
    {

        parent::__construct();

    }
    

    public function getMenuAdmin()
    {
        $Menu = CoreMenu::with(implode('.', array_fill(0, 100, 'children')))
            ->where('id_parent', '=', '0')
            ->orderBy('list_order', 'asc')
            ->get();
            
        return response()->json(array('statut'=> 'Success', 'arr' => $Menu->toJson()), 200);
    }

    public function setMenuAdmin()
    {
        $json = $_POST['menus'];

        $itemafterupdate = $this->updateMenuChildren(json_decode($json));

        $itemfromdb = CoreMenu::select("id_menu_item")->pluck('id_menu_item')->toArray();

        foreach ($itemfromdb as $item) {
            if(!in_array($item, $itemafterupdate) && !empty($item)){
            	CorePermissions::where('id_menu_item',$item)->delete();
                CorePermissionsExceptions::where('id_menu_item',$item)->delete();
                CoreMenu::where('id_menu_item', '=' ,$item)->limit(1)->delete();
            }
        }

        return response()->json(array('statut' => 'Success'/*, 'arr' => $itemfromdb */), 200);
    }

    public function updateMenuChildren($json, $parent=null, $itemafterupdate=null)
    {

        if($parent == null){
            $parent = 0;
        }

        if(!empty($json) && count($json) > 0){

            foreach ($json as $key => $value) {

                $icon = (string)$value->icon;
                if(!empty($icon)){
                    $icon = str_replace("fa fa-", "", $icon);
                }


                $data = [
                    'id_parent' => (int)$parent,
                    'list_order' => (int)$key,
                    'description' => (string)$value->description,
                    'link' => (string)$value->link,
                    'icon' => (string)$icon,
                ];

                if(!empty($value->id_menu_item)){

                    CoreMenu::where('id_menu_item', $value->id_menu_item)->update($data);

                    $iddd = $value->id_menu_item;

                } else {

                    $action_page = CoreMenu::create($data);
                    $iddd = $action_page->id_menu_item;

                }

                $itemafterupdate[] = $iddd;

                if(!empty($value->children) && count($value->children) > 0){

                    $itemafterupdate = $this->updateMenuChildren($value->children, $iddd, $itemafterupdate);

                }


            }
        }

        return $itemafterupdate;


    }













    public function core_menu(){


        $this->data['pages'] = CoreMenu::orderBy('id_parent', 'asc')
            ->orderBy('list_order', 'asc')
            ->with('core_permission')
            ->with('parent_menu')
            ->get();

        return view('core.core_menu', $this->data);

    }

    public function core_menu_add($id = null){

        $this->data['pages'] = [];

        if(Input::get('save'))
            return $this->save_core_menu($id);

        if(!empty($id)) {

            $this->data['pages'] = CoreMenu::where('id_menu_item', '=', $id)
                ->with('core_permission')
//                ->with('role_permision_except')
                ->limit(1)
                ->get();
        }

        $this->data['menus_c'] = CoreMenu::get()->toArray();
        
        //$this->data['roles'] = CoreGroups::select('description', 'id_group')->pluck('description', 'id_group')->toArray();
        $this->data['roles2'] = CoreGroups::get();
//        $this->data['users'] = User::get();

        return view('core.core_menu_add', $this->data);

    }

    public function core_menu_del($id = null) {

        if(!empty($id)){

            CoreMenu::where('id_menu_item', '=' ,$id)->limit(1)->delete();
            CoreMenu::where('id_parent', '=' ,$id)->delete();
            CoreGroups::where('id_group', '=' ,$id)->delete();

        }

        return redirect('/core_menu/');

    }

    public function del_role_permision() {

        $iidd_attr = Input::get('iidd_attr');
        if(!empty($iidd_attr)){

            CorePermissions::where('id_permission', '=' ,$iidd_attr)->limit(1)->delete();

        }

        return response()->json(array('statut'=> 'Success'), 200);

    }

    public function save_core_menu($id=null){

        $data = [
            'list_order' => (Input::get('list_order')) ? Input::get('list_order') : 0,
            'id_parent' => (Input::get('id_parent')) ? Input::get('id_parent') : 0,
            'description' => (Input::get('description')) ? Input::get('description') : '',
            'link' => (Input::get('link')) ? Input::get('link') : '#',
            'icon' => (Input::get('icon')) ? Input::get('icon') : '',
        ];

        if(empty($id)){

            $action_page = CoreMenu::create($data);
            $id = $action_page->id_menu_item;

        } else {

            $action_page = CoreMenu::where('id_menu_item', $id)->update($data);
        }

        if($id && count(Input::get('id_group')) > 0 ){
            $this->save_role_permision($id, Input::get('id_group'));
        }

        return redirect('/core_menu_add/'.$id.'/');

    }

    public function save_role_permision($id, $role_ids){

        foreach ($role_ids as $key => $role_id) {
            foreach ($role_id as $kkey => $role_i) {
                if(!empty($role_i)) {
                    $atr = CorePermissions::where('id_permission', '=', $key)->first();

                    $data = [
                        'id_menu_item' => $id,
                        'id_group' => $role_ids[$key][$kkey],
                        'permission' => '-',
                    ];

                    if ($atr == null) {
                        CorePermissions::create($data);
                    } else {
                        CorePermissions::where('id_permission', $key)->update($data);
                    }

                }
                if($role_i == 0){
                    CorePermissions::where('id_permission', '=' ,$key)->limit(1)->delete();
                }
            }

        }

        return true;

    }



}