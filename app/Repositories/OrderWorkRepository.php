<?php

namespace App\Repositories;

use App\CoreUsers;
use App\Models\OrdersWork;

class OrderWorkRepository
{
    public function getAll()
    {
        return OrdersWork::get();
    }

    public function getById($id)
    {
        return OrdersWork::where('id_lavoro', $id)->first();
    }
}
