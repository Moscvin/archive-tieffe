<?php

namespace App\Console\Commands;

use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;
use App\Models\Clienti as Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateOperationsCommand extends Command
{
    protected $signature = 'update:operations';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $statistic = [
            0 => [0, []],
            1 => [0, []],
            2 => [0, []],
            3 => [0, []]
        ];

        Client::with(['locations' => function($q) {
            $q->with(['machineries' => function($q) {
                $q->with(['operations' => function($q) {
                    $q->orderBy('data', 'desc');
                }]);
            }]);
        }])->get()->each(function($client) use(&$statistic) {
            $client->locations && $client->locations->each(function($location) use(&$statistic) {
                $location->machineries && $location->machineries->each(function($machinery) use(&$statistic) {
                    $data = $this->runProcess($machinery->operations->first());
                    if ($data) {
                        $statistic[$data['type']][0]++;
                        array_push($statistic[$data['type']][1], $data['dates']);
                    }
                });
            });
        });
        Storage::put('text.txt', print_r($statistic, true));
    }

    private function runProcess($operation)
    {
        if (!$operation) {
            return false;
        }
        if ($operation->data < '2020-01-01') {
            $date = date('2022-m-d', strtotime($operation->data));
            $type = 1;
        } elseif($operation->data >= '2020-01-01' && $operation->data < '2020-03-01') {
            $date = date('2023-m-d', strtotime($operation->data));
            $type = 2;
        } elseif($operation->data >= '2020-03-01' && $operation->data < '2021-11-01') {
            $year = date('Y', strtotime($operation->data)) + 2;
            $date = date($year . '-m-d', strtotime($operation->data));
            $type = 3;
        } else {
            return false;
        }

        $newOperation = $this->createOperation($operation, $date);

        $operation->machineries->each(function($item) use($newOperation) {
            $this->createOperationMachinery($item, $newOperation);
        });

        return [
            'type' => $type,
            'dates' => [
                'oldDate' => $operation->data,
                'newDate' => $date
            ]
        ];
    }

    private function createOperation($operation, $newDate)
    {
        return Operation::create([
            'tipologia' => $operation->tipologia,
            'ora_dalle' => $operation->ora_dalle,
            'ora_alle' => $operation->ora_alle,
            'incasso' => $operation->incasso,
            'cestello' => $operation->cestello,
            'data' => $newDate,
            'ora' => $operation->ora,
            'urgente' => $operation->urgente,
            'a_corpo' => $operation->a_corpo,
            'tecnico' => $operation->tecnico,
            'pronto_intervento' => $operation->pronto_intervento,
            'stato' => 0,
            'file' => $operation->file,
            'note' => $operation->note,
            'id_sede' => $operation->id_sede,
            'long' => $operation->long,
            'lat' => $operation->lat,
            'old_id_intervento' => $operation->id_intervento,
            'fatturare_a' => $operation->fatturare_a,
        ]);
    }

    private function createOperationMachinery($operationMachinery, $newOperation)
    {
        return OperationMachinery::create([
            'id_intervento' => $newOperation->id_intervento,
            'id_macchinario' => $operationMachinery->id_macchinario,
            'desc_intervento' => $operationMachinery->desc_intervento,
            'rapporto_initial' => $operationMachinery->rapporto_initial,
            'rapporto_state' => $operationMachinery->rapporto_state,
        ]);
    }
}
