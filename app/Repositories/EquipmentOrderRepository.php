<?php

namespace App\Repositories;

use App\Models\EquipmentOrderIntervention;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class EquipmentOrderRepository.
 */
class EquipmentOrderRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return EquipmentOrderIntervention::class;
    }
}
