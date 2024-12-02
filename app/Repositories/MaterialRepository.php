<?php

namespace App\Repositories;

use App\Models\EquipmentOrderIntervention;

class MaterialRepository
{
    public function getAll()
    {
        return EquipmentOrderIntervention::get();
    }

    public function getById($id)
    {
        return EquipmentOrderIntervention::where('id_materiale', $id)->first();
    }

    public function getByWorkId($id)
    {
        return EquipmentOrderIntervention::where('id_lavoro', $id)->get();
    }
}
