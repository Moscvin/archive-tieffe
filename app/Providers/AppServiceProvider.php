<?php

namespace App\Providers;

use App\CoreAdminOptions;
use App\CorePermissionsExceptions;
use App\CorePermissions;
use App\Http\View\Composers\TipologiaComposer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        Schema::defaultStringLength(191);


        $this->boot_Menu($events);
        $this->myConfig();
         View::composer(['machinery.*','clienti.*'], TipologiaComposer::class);

    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    public function boot_Menu($events){
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {

            //$event->menu->add('------- MY MENU -------');

            $menus = app(\App\Http\Controllers\MainController::class)->menu_user();


            foreach ($menus as $menu) {
                $submenu = array();
                $submenu_temp = array();
                if (count($menu->children) > 0){
                    foreach ($menu->children as $child) {

                        $submenu2 = array();
                        if (count($child->children) > 0) {
                            foreach ($child->children as $child2) {

                                $submenu2[] = [
                                    'text' => $child2['description'],
                                    'url' => $child2['link'],
                                    'icon' => $child2['icon'],
                                    'id_menu'=>$child2['id_menu_item'],
                                    'menu_active'=>$this->permissionMangement($child2['id_menu_item']),
                                    'label'=> $child2['label'] ?? '',
                                    'label_color' => $child2['label_color'] ?? '',
                                ];

                            }
                        }

                        $submenu_temp = [
                            'text' => $child['description'],
                            'url' => $child['link'],
                            'icon' => $child['icon'],
                            'id_menu'=>$child['id_menu_item'],
                            'menu_active'=>$this->permissionMangement($child['id_menu_item']),
                            'label'=> $child['label'] ?? '',
                            'label_color' => $child['label_color'] ?? '',
                        ];
                        if(count($submenu2) > 0){
                            $submenu_temp['submenu'] = $submenu2;
                        }
                        $submenu[] = $submenu_temp;

                    }

                }

                $item = [
                    'text' => $menu['description'],
                    'url' => $menu['link'],
                    'icon' => $menu['icon'],
                    'id_menu'=>$menu['id_menu_item'],
                    'menu_active'=>$this->permissionMangement($menu['id_menu_item']),
                    'label'=> $menu['label'] ?? '',
                    'label_color' => $menu['label_color'] ?? '',
                ];
                if(count($submenu) > 0){
                    $item['submenu'] = $submenu;
                }

                $event->menu->add($item);
            }
        });
    }

    public function permissionMangement($menu_id = 0){

        $group_id = (!Auth::check()) ? '7' : Auth::user()->core_groups->id_group;

        $permissionEx = CorePermissionsExceptions::where('id_menu_item',$menu_id)->where('id_user', Auth::id());

        if ($permissionEx->count() != 0){

            $permissionEx = $permissionEx->first();

            if (empty($permissionEx->permission)){

                return  false;

            }else{

                return  true;
            }

        }else{
            $permission = CorePermissions::where('id_menu_item',$menu_id)->where('id_group',$group_id);
            $permission = $permission->first();

            if (empty($permission->permission)){

                return  false;

            }else{

                return  true;
            }
        }
    }

    public function myConfig()
    {
        $config = CoreAdminOptions::pluck('value', 'description')->toArray();

        if(count($config) > 0){
            \Config::set($config);
        }

        return true;

    }



}
