<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorePermissionsExceptions extends Model
{
    protected $table = 'core_permissions_exceptions';

    protected $primaryKey = 'id_permission_exception';

    protected $fillable = [
        'id_permission_exception',
        'id_menu_item',
        'id_user',
        'permission',
    ];

    public function core_permissions()//role_permisions
    {
        return $this->belongsTo('App\CoreGroups', 'id_group');
    }

    public function core_permissions_user()//role_permisions_user
    {

        return $this->belongsTo('App\CoreUsers', 'id_user');

    }
    public function getUser(){

        return $this->hasOne('App\CoreUsers', 'id_user','id_user');
    }

}
