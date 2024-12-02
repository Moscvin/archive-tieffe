<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoreAdminOptions extends Model
{
    protected $table = 'core_admin_options';

    protected $primaryKey = 'id_option';

    protected $fillable = [
        'id_option',
        'description',
        'value',
    ];


}