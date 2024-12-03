<?php

namespace App\Http\Controllers\Query;

use App\Models\Clienti;
use App\Models\Report\Report;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use Google\Auth\Cache\Item;

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
        return view('query.to_check.index', compact('items', 'chars', 'activeTechnicians', 'title', 'link'));
    }

    // public function show($id)
    // {
    //     $item = $this->reportRepository->getById($id) ?? abort(404);

    //     $item->update(['letto' => 1]);
    //     //dd($item->letto);

    //     $link = '/query/';
    //     return view('query.to_check.show', compact('item', 'link'));
    // }

    // public function read($id, Request $request)
    // {
    //     $item = $this->reportRepository->getById($id) ?? abort(404);
    //     $status = $item->update(['letto' => 0]);
    //     return response()->json([
    //         'status' => $status,
    //         'debug' => $item
    //     ], 200);
    // }
    public function ajax(Request $request)
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);

        $data = $this->reportRepository->getFiltered($request->all());
        $index = 0;
        $data_json = [];
        foreach ($data->items as $item) {
            foreach ($item->intervention->materials as $material) {
            }

            $statusColor = $item->statusColor;
            $data_json[] = [
                $item->intervention->location->client->ragione_sociale ?? '',
                $item->intervention->location->client->committente ?? '',
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
                $item->intervention->data ?? '',
                $item->intervention->tipologia ?? '',
                $item->intervention->location->address ?? '',
                $item->intervention->report->id_rapporto ?? '',
                $item->intervention->report->data_invio ?? '',
                $item->intervention->report->garanzia ?? '',
                $item->intervention->report->dafatturare ?? '',
                $item->intervention->cestello ?? '',
                $item->intervention->report->aggiuntivo ?? '',
                $item->intervention->report->incasso_pos ?? '',
                $item->intervention->report->incasso_in_contanti ?? '',
                $item->intervention->report->incasso_con_assegno ?? '',
                $item->intervention->report->note_riparazione ?? '',
                $item->intervention->report->stato ?? '',

            ];
            array_push($data_json[$index]);
            $index++;
        }
        return response()->json([
            'draw' => $request->draw ?? 1,
            'recordsTotal' => $data->recordsTotal,
            'recordsFiltered' => $data->recordsFiltered,
            "data" => $data_json,
        ]);
    }
}
