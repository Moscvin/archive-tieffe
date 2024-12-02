<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CoreUsers extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'core_users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'id_user',
        'name',
        'family_name',
        'username',
        'email',
        'isactive',
        'id_group',
        'first_login',
        'password',
        'serie',
        'lat',
        'lng',
        'coord_updated_at',
        'outsider'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'app_token'
    ];


    public function core_groups() {
        return $this->belongsTo('App\CoreGroups', 'id_group');
    }

    public function getUserTypeAttribute() {
        return $this->id_group == 9 ? 2 : 1;
    }

    public function getFullNameAttribute() {
        return $this->family_name . ' ' . $this->name;
    }

    public function getWebAppPermissionAttribute() {
        return in_array($this->id_group, [1, 8]);
    }
}
