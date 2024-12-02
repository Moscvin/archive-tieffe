<?php

namespace App\Repositories;

use App\CoreUsers;


class UserRepository
{

    public function getActiveTechnicians()
    {
        return CoreUsers::where('id_group', 9)->where('isactive', 1)->get();
    }
}
