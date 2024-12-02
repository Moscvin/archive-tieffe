<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoreMenu extends Model
{
    protected $table = 'core_menu';

    protected $primaryKey = 'id_menu_item';
    
    protected $fillable = [
        'id_menu_item',
        'description',
        'id_parent',
        'list_order',
        'icon',
        'link',
    ];

    public function parent() {
        return $this->hasOne('App\CoreMenu', 'id', 'id_parent')->orderBy('list_order', 'asc');
    }

    public function children() {
        return $this->hasMany('App\CoreMenu', 'id_parent', 'id_menu_item')->orderBy('list_order', 'asc');
    }

    public static function tree() {
        return static::with(implode('.', array_fill(0, 100, 'children')))->where('id_parent', '=', '0')->orderBy('list_order')->get();
    }

    public function parent_menu()
    {
        return $this->belongsTo('App\CoreMenu', 'id_parent', 'id_menu_item');
    }

    public function core_permission()
    {
        return $this->hasMany('App\CorePermissions', 'id_menu_item', 'id_menu_item');
    }

    public function core_permission_except()
    {
        return $this->hasOne('App\CorePermissionsExceptions','id_menu_item');
    }

}

