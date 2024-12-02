<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoreGroups extends Model
{
    protected $table = 'core_groups';

    protected $primaryKey = 'id_group';

    protected $fillable = [
        'id_group',
        'description',
    ];

    public function roles()
    {
        return $this->hasMany('App\CoreGroups');
    }

}
