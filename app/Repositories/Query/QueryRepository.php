<?php

namespace App\Repositories\Query;

use App\Models\Equipment;
use App\Models\EquipmentOrderIntervention;
use Illuminate\Http\Request;
use App\Repositories\Query\IRepository;


class QueryRepository implements IRepository
{
    public function getDataTableFilteredAndFormatted(Request $request, array $chars)
    {
        $dataTables = $this->getDataTablesFiltered($request);

        return [
            'start' => $request->start,
            'length' => $request->length,
            'draw' => $request->draw,
            'recordsTotal' => $dataTables->recordsTotal,
            'recordsFiltered' => $dataTables->recordsFiltered,
            "data" => $this->getItemsFormatted($dataTables->items, $chars),
        ];
    }
    public function getDataTablesFiltered(Request $request)
    {
        $query = $this->getBaseQuery($request);

        $recordsTotalCount = (clone $query)->count();

        $query = $this->applyJoins($query);
        $query = $this->applyAllFieldFilter($query, $request);
        $query = $this->applyCustomFilters($query, $request);
        $query = $this->applySorting($query, $request);

        $recordsFilteredCount = (clone $query)->count();

        $dataTablesItems = $this->getItems($query, $request);


        return (object) [
            'items' => $dataTablesItems,
            'recordsTotal' => $recordsTotalCount,
            'recordsFiltered' => $recordsFilteredCount,
        ];
    }
    public function getItemsFormatted(array $dataTablesItems, array $chars)
    {
        $index = 0;
        $formattedItems = [];

        foreach ($dataTablesItems as $item) {
            $formattedItems[$index] = [
                $item->intervention->location->client->ragione_sociale ?? '',
                ($item->intervention->location->client->committente ?? null) === 1 ? 'Si' : 'No',
                $item->intervention->location->client->partita_iva ?? '',
                $item->intervention->location->client->codice_fiscale ?? '',
                $item->intervention->location->address ?? '',
                $item->intervention->location->phones ?? '',
                $item->intervention->location->note ?? '',
                $item->intervention->location->tipologia ?? '',
                $item->intervention->location->macchinari->descrizione ?? '',
                $item->intervention->location->macchinari->tipologia ?? '',
                $item->intervention->location->macchinari->note ?? '',
                $item->intervention->location->macchinari->tetto ?? '',
                $item->intervention->id_intervento ?? '',
                $item->intervention->data ? date('d/m/Y', strtotime($item->intervention->data)) : '',
                $item->intervention->tipologia ?? '',
                $item->intervention->location->address ?? '',
                $item->intervention->report->id_rapporto ?? '',
                $item->intervention->report->data_invio ? date('d/m/Y', strtotime($item->intervention->report->data_invio)) : '',
                $item->intervention->report->garanzia ?? '',
                $item->intervention->report->dafatturare ?? '',
                $item->intervention->cestello ?? '',
                $item->intervention->report->aggiuntivo ?? '',
                $item->intervention->report->incasso_pos ?? '',
                $item->intervention->report->incasso_in_contanti ?? '',
                $item->intervention->report->incasso_con_assegno ?? '',
                $item->intervention->report->note_riparazione ?? '',
                $item->intervention->report->stato ?? '',
                $item->quantita ?? '',
                $item->descrizione ?? '',
                $item->codice ?? '',
            ];
            $index++;
        }
        dd($formattedItems);
        return $formattedItems;
    }

    private function getBaseQuery()
    {
        return EquipmentOrderIntervention::query();
    }
    private function applyJoins($query)
    {
        return $query->join('interventi', 'interventi.id_intervento', '=', 'interventi.id_intervento')
            ->join
            ->join('sedi', 'sedi.id_sedi', '=', 'interventi.id_sede')
            ->join('rapporti', 'rapporti.id_rapporto', '=', 'interventi.id_intervento');
    }


    private function applyAllFieldFilter($query, $request)
    {
        if ($request->search['value']) {
            $query->where('interventi.id_intervento', 'like', '%' . $request->search['value'] . '%');
        }
        return $query;
    }
    private function applyCustomFilters($query, $request)
    {
        if ($request->has('filter')) {
            $filter = $request->filter;
            if (isset($filter['id_intervento'])) {
                $query->where('interventi.id_intervento', $filter['id_intervento']);
            }
        }
        return $query;
    }
    private function applySorting($query, $request)
    {
        foreach ($request->order as $condition) {
            switch ($condition['column']) {
                case 0:
                    $query->orderBy('ragione_sociale', $condition['dir']);
                    break;
                case 1:
                    $query->orderBy('committente', $condition['dir']);
                    break;
                case 2:
                    $query->orderBy('partita_iva', $condition['dir']);
                    break;
                case 3:
                    $query->orderBy('codice_fiscale', $condition['dir']);
                    break;
                case 4:
                    $query->orderBy('address', $condition['dir']);
                    break;
                case 5:
                    $query->orderBy('phones', $condition['dir']);
                    break;
                case 6:
                    $query->orderBy('note', $condition['dir']);
                    break;
                case 7:
                    $query->orderBy('tipologia', $condition['dir']);
                    break;
                case 8:
                    $query->orderBy('descrizione', $condition['dir']);
                    break;
                case 9:
                    $query->orderBy('tipologia', $condition['dir']);
                    break;
                case 10:
                    $query->orderBy('note', $condition['dir']);
                    break;
                case 11:
                    $query->orderBy('tetto', $condition['dir']);
                    break;
                case 12:
                    $query->orderBy('id_intervento', $condition['dir']);
                    break;
                case 13:
                    $query->orderBy('data', $condition['dir']);
                    break;
                case 14:
                    $query->orderBy('tipologia', $condition['dir']);
                    break;
                case 15:
                    $query->orderBy('address', $condition['dir']);
                    break;
                case 16:
                    $query->orderBy('id_rapporto', $condition['dir']);
                    break;
                case 17:
                    $query->orderBy('data_invio', $condition['dir']);
                    break;
                case 18:
                    $query->orderBy('garanzia', $condition['dir']);
                    break;
                case 19:
                    $query->orderBy('dafatturare', $condition['dir']);
                    break;
                case 20:
                    $query->orderBy('incasso_pos', $condition['dir']);
                    break;
                case 21:
                    $query->orderBy('incasso_in_contanti', $condition['dir']);
                    break;
                case 22:
                    $query->orderBy('incasso_con_assegno', $condition['dir']);
                    break;
                case 23:
                    $query->orderBy('note_riparazione', $condition['dir']);
                    break;
                case 24:
                    $query->orderBy('stato', $condition['dir']);
                    break;
                case 25:
                    $query->orderBy('quantita', $condition['dir']);
                    break;
                case 26:
                    $query->orderBy('descrizione', $condition['dir']);
                    break;
                case 27:
                    $query->orderBy('codice', $condition['dir']);
                    break;
            }
        }
        return $query;
    }
    private function getItems($query, $request)
    {
        $itemsIds = (clone $query)->when(($request['length'] ?? 0) > 0, function ($q) use ($request) {
            $q->skip($request['start'])->take($request['length']);
        })->get(['interventi.id_intervento'])->pluck(['id_intervento']);

        return $this->getBaseQuery($request)->whereIn('id_intervento', $itemsIds)->get()->sortBy(function ($item) use ($itemsIds) {
            return array_search($item->getKey(), $itemsIds->toArray());
        })->values();

        return $items;
    }
}
