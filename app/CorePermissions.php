<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorePermissions extends Model
{
    protected $table = 'core_permissions';

    protected $primaryKey = 'id_permission';

    protected $fillable = [
        'id_permission',
        'id_menu_item',
        'id_group',
        'permission',
    ];

    public function core_permissions()
    {
        return $this->belongsTo('App\CoreGroups', 'id_group');
    }

    
}
