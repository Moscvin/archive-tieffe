<?php

namespace App\Repositories;

use App\Models\Order;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

class OrderRepository
{
    public function getAll()
    {
        return Order::get();
    }

    public function getById($id)
    {
        return Order::where('id_commessa', $id)->first();
    }
}
