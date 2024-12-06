<?php

namespace App\Http\Controllers\Query;

use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EquipmentOrderIntervention;
use App\Models\Intervention;

class ListController extends Controller
{
    private $reportRepository;
    private $userRepository;

    public function __construct(ReportRepository $reportRepository, UserRepository $userRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $items = $this->reportRepository->getAll();
        $activeTechnicians = $this->userRepository->getActiveTechnicians();
        $title = 'Elenco Rapporti';
        $link = '/query/';
        return view('query.index', compact('items', 'chars', 'activeTechnicians', 'title', 'link'));
    }

    public function ajax(Request $request)
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);


        $data_json = [];
        $time = time();
        $query = Intervention::when($request->dateFrom, function ($q) use ($request) {
            $date = date('Y-m-d', strtotime(str_replace('/', '-', $request->dateFrom)));
            $q->where('data', '>=', $date);
        })->when($request->dateTo, function ($q) use ($request) {
            $date = date('Y-m-d', strtotime(str_replace('/', '-', $request->dateTo)));
            $q->where('data', '<=', $date);
        })->whereHas('report')->get();

        $time2 = time() - $time;

        foreach ($query as $item) {
            if ($item->materials->isEmpty()) {

                $data_json[] = [
                    $item->location->client->ragione_sociale ?? '',
                    ($item->location->client->committente ?? null) === 1 ? 'Si' : 'No',
                    $item->location->client->partita_iva ?? '',
                    $item->location->client->codice_fiscale ?? '',
                    $item->location->address ?? '',
                    $item->location->phones ?? '',
                    $item->location->note ?? '',
                    $item->location->tipologia ?? '',
                    $item->location->macchinari->descrizione ?? '',
                    $item->location->macchinari->tipologia ?? '',
                    $item->location->macchinari->note ?? '',
                    ($item->location->macchinari->tetto ?? null) === 1 ? 'Si' : 'No',
                    $item->id_intervento ?? '',
                    $item->data ? date('d/m/Y', strtotime($item->data)) : '',
                    $item->tipologia ?? '',
                    $item->location->address ?? '',
                    $item->report->id_rapporto ?? '',
                    $item->report->data_invio ? date('d/m/Y', strtotime($item->report->data_invio)) : '',
                    ($item->report->garanzia ?? null) === 1 ? 'Si' : 'No',
                    ($item->report->dafatturare ?? null) === 1 ? 'Si' : 'No',
                    ($item->report->aggiuntivo ?? null)  === 1 ? 'Si' : 'No',
                    number_format($item->report->incasso_pos ?? 0, 2, ',', '.') ?? '',
                    number_format($item->report->incasso_in_contanti ?? 0, 2, ',', '.') ?? '',
                    number_format($item->report->incasso_con_assegno ?? 0, 2, ',', '.') ?? '',
                    $item->report->note_riparazione ?? '',
                    ($item->report->stato ?? null) === 2 ? 'Completato' : 'Non Completato',
                    '',
                    '',
                    '',
                ];
            } else {
                foreach ($item->materials as $material) {
                    $data_json[] = [
                        $item->location->client->ragione_sociale ?? '',
                        ($item->location->client->committente ?? null) === 1 ? 'Si' : 'No',
                        $item->location->client->partita_iva ?? '',
                        $item->location->client->codice_fiscale ?? '',
                        $item->location->address ?? '',
                        $item->location->phones ?? '',
                        $item->location->note ?? '',
                        $item->location->tipologia ?? '',
                        $item->location->macchinari->descrizione ?? '',
                        $item->location->macchinari->tipologia ?? '',
                        $item->location->macchinari->note ?? '',
                        ($item->location->macchinari->tetto ?? null) === 1 ? 'Si' : 'No',
                        $item->id_intervento ?? '',
                        $item->data ? date('d/m/Y', strtotime($item->data)) : '',
                        $item->tipologia ?? '',
                        $item->location->address ?? '',
                        $item->report->id_rapporto ?? '',
                        $item->report->data_invio ? date('d/m/Y', strtotime($item->report->data_invio)) : '',
                        ($item->report->garanzia ?? null) === 1 ? 'Si' : 'No',
                        ($item->report->dafatturare ?? null) === 1 ? 'Si' : 'No',
                        ($item->report->aggiuntivo ?? null)  === 1 ? 'Si' : 'No',
                        number_format($item->report->incasso_pos ?? 0, 2, ',', '.') ?? '',
                        number_format($item->report->incasso_in_contanti ?? 0, 2, ',', '.') ?? '',
                        number_format($item->report->incasso_con_assegno ?? 0, 2, ',', '.') ?? '',
                        $item->report->note_riparazione ?? '',
                        ($item->report->stato ?? null) === 2 ? 'Completato' : 'Non Completato',
                        $material->quantita ?? '',
                        $material->descrizione ?? '',
                        $material->codice ?? '',
                    ];
                }
            }
        }
        
        // dd($data_json);
        $time3 = time() - $time;

        return response()->json([
            'draw' => $request->draw ?? 1,
            'recordsTotal' => (clone $query)->count(),
            'recordsFiltered' => $query->count(),
            "data" => $data_json,
            "times" => [
                '1' => $time,
                '2' => $time2,
                '3' => $time3
            ]
        ]);
    }
}
