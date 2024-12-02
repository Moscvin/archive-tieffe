<?php

namespace App\Http\Middleware;

use App\CoreUsers;
use Closure;
use App\CoreMenu;
use App\CoreGroups;
use App\CorePermissions;
use App\CorePermissionsExceptions;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class PermissionManagement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guest() && $request->path() != 'error_autorization'){

            //if user inactive so logout
            if (!Auth::user()->isactive) {
                Auth::logout();
                return redirect('/');
            }

            $user_id = Auth::id();
            $routeName= $request->route()->getName();
            $core_menu = CoreMenu::where('link', $routeName)->orWhere('link', 'like', '%' . $routeName )->get();
            if ($core_menu->count() == 0){
                return $next($request);
            }else{
                $menu_id = $core_menu->first()->id_menu_item;

                $permissionEx = CorePermissionsExceptions::where('id_menu_item',$menu_id)->where('id_user',$user_id);
                
                if($permissionEx->count() != 0){

                    $permissionEx = $permissionEx->first();
                    
                    if (empty($permissionEx->permission)){

                        return  redirect('/error_autorization');

                    }else{
                        $request->attributes->add(['permissionAttribute' => $permissionEx->permission]);
                        return $next($request);
                    }

                }else{

                    $permission = CorePermissions::where('id_menu_item',$menu_id)->where('id_group',CoreUsers::find($user_id)->core_groups->id_group)->first();
                    if (empty($permission->permission)){
                        return  redirect('/error_autorization');

                    }else{
                        $request->attributes->add(['permissionAttribute' => $permission->permission]);
                        return $next($request);
                    }

                }

            }

        }else{
            return $next($request);
        }

    }
}
