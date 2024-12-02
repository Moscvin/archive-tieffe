<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CoreMenu;
use App\CorePermissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use DB;

class MainController extends Controller
{

    public function __construct()
    {
        $request = Request();
        $middleware = $request->route()->action['middleware'];
        $m_count = count($middleware) - 1;
        $request->attributes->add(['verify_public' => request()->route()->getName() == 'public.']);

        $this->data = [];

        $this->middleware($middleware[$m_count]);
        $this->middleware(function ($request, $next) use ($middleware,$m_count) {
            if ($middleware[$m_count] == 'auth') {
                if (Auth::user()->isactive != true) {
                    Auth::logout();
                    return redirect()->route('home');
                }
            }

            $this->data['menu'] = $this->menu_user();
            return $next($request);
        });
    }

    public function menu_user()
    {
        $Menu = CoreMenu::with(implode('.', array_fill(0, 100, 'children')))
            ->where('id_parent', '=', '0')
            ->orderBy('list_order', 'asc')
            ->get();
        $Menu = $this->setCount($Menu);
        if(count($Menu) > 0){
            $this->data['menu'] = $Menu;
        } else {
            $this->data['menu'] = [];
        }

        $thirtt = array();
        $role_permisions = CorePermissions::get()->toArray();
        foreach ($role_permisions as $kkv => $role_permision){
            $thirtt[$role_permision['id_menu_item']][] = $role_permision['id_group'];
        }

        Session::put('role_permisions', $thirtt);
        return $this->data['menu'];
    }

    public function check_permited_page($role_id)
    {
        $permited_pages = array(
            "home",
            "login"
        );
        if(!empty(\Request::segment(1)) && !in_array(\Request::segment(1), $permited_pages)){
            $menu = CoreMenu::where('link', "/".\Request::segment(1))
                ->with('core_permission')
                ->limit(1)
                ->get();

            if(count($menu) > 0){
                if(count($menu[0]->role_permision) > 0){
                    foreach ($menu[0]->role_permision as $item) {
                        if($item->role_id == $role_id){
                            return true;
                        }
                    }
                }
            }
            return false;
        }
        return true;
    }

    public function setCount($menus)
    {
        foreach($menus as $menu) {
            $key = array_search($menu->link, $this->countMenus['href']);
            if($key !== false) {
                $menu->label = DB::table($this->countMenus['query'][$key]['table'])
                    ->whereRaw($this->countMenus['query'][$key]['where'])
                    ->count();
                $menu->label_color = $this->countMenus['colors'][$key];
                if($menu->children->count()) {
                    $menu->children = $this->setCount($menu->children);
                }
            }
        }
        return $menus;
    }

    private $countMenus = [
        'href' => [
            '/wrong_vat_number'
        ],
        'colors' => [
            'danger'
        ],
        'query' => [
            [
                'table' => 'clienti',
                'where' => 'partita_iva_errata=1'
            ]
        ]
    ];
}
