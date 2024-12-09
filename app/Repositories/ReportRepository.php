<?php

namespace App\Repositories;

use App\Models\Report\Report;
use Carbon\Carbon;
use DB;

/**
 * Class ReportRepository.
 */
class ReportRepository
{
    public function getAll()
    {
        return Report::get();
    }

    public function getAllByDate()
    {
        return Report::orderBy('data_intervento', 'desc')->get();
    }
    

    public function getById($id)
    {
        return Report::where('id_rapporto', $id)->first();
    }

    public function getForToday()
    {
        return Report::where('data_intervento', DB::raw('CURDATE()'))->get();
    }

    public function getToCheck()
    {
        return Report::where('letto', 0)->get();
    }

    public function getFiltered($fields)
    {
        $query = Report::query();

        if ($fields['client']) {
            $query->with('intervention.location.client')->whereHas('intervention.location.client', function ($q) use ($fields) {
                $q->where('ragione_sociale', 'like', '%' . $fields['client'] . '%');
            });
        }
        if ($fields['search']) {
            $query->with('intervention.location.client')->whereHas('intervention.location.client', function ($q) use ($fields) {
                $q->where('ragione_sociale', 'like', '%' . $fields['search']['value'] . '%');
            });
        }

            $dateFrom = $fields['dateFrom'] ?? null;
            $dateTo = $fields['dateTo'] ?? null;

            $query->when($dateFrom, function ($q) use ($fields) {
              return $q->where('data_intervento', '>=', Carbon::createFromFormat('d/m/Y', $fields['dateFrom'])->format('Y-m-d'));
            })->when($dateTo, function ($q) use ($fields) {
                return $q->where('data_intervento', '<=', Carbon::createFromFormat('d/m/Y', $fields['dateTo'])->format('Y-m-d'));
            });
            if(isset($fields['order'])){
                foreach($fields['order'] as $condition) {
                    switch($condition['column']) {
                        case 0: {

                            $query->join('interventi', 'interventi.id_intervento', '=', 'rapporti.id_intervento')
                            ->join('sedi', 'sedi.id_sedi', '=', 'interventi.id_sede')
                            ->join('clienti', 'clienti.id', '=', 'sedi.id_cliente')
                            ->orderBy('clienti.ragione_sociale', $condition['dir']);

                            break;
                        }
                        case 1: {
                            $query->orderBy('data_intervento', $condition['dir']);
                            break;
                        }
                        case 2:
                            {
                                $query->join('interventi', 'interventi.id_intervento', '=', 'rapporti.id_intervento')
                                ->orderBy('interventi.data', $condition['dir']);
                                break;
                            }
                        case 3: {
                            $query->join('interventi', 'interventi.id_intervento', '=', 'rapporti.id_intervento')
                            ->orderBy('interventi.tipologia', $condition['dir']);
                            break;
                        }
                        case 5: {
                            $query->orderBy('progressivo', $condition['dir']);

                            break;
                        }
                        case 6: {

                            $status = [
                                2 => 'Completato',
                                3 => 'Non completato',
                                4 => 'Annullato'
                            ];

                            $statusFlipped = array_flip($status);

                            $statusNumber1 = $statusFlipped[$status[2]];
                            $statusNumber2 = $statusFlipped[$status[3]];
                            $statusNumber3 = $statusFlipped[$status[4]];

                            $statusText1 = $status[2];
                            $statusText2 = $status[3];
                            $statusText3 = $status[4];

                            $query->orderBy( DB::raw("IF(strcmp(stato, {$statusNumber1}), '{$statusText1}',  IF(strcmp(stato, {$statusNumber2}), '{$statusText2}',IF(strcmp(stato, {$statusNumber3}), '{$statusText3}', '')))"), $condition['dir']);
                        }
                    }
                }
            } else {
                $query->orderBy('data_intervento', 'desc');
            }


        return (object)[
            'items' => (clone $query)->skip($fields['start'])->take($fields['length'])->get(),
            'recordsFiltered' => (clone $query)->count(),
            'recordsTotal' => Report::count()
        ];
    }
}
