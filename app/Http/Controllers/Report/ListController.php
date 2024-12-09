<?php

namespace App\Http\Controllers\Report;

use App\Models\Clienti;
use App\Models\Report\Report;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

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
        $link = '/reports_list/';
        // dd($items);
        return view('reports.to_check.index', compact('items', 'chars', 'activeTechnicians', 'title', 'link'));
    }

    public function show($id)
    {
        $item = $this->reportRepository->getById($id) ?? abort(404);

        $item->update(['letto' => 1]);
        //dd($item->letto);

        $link = '/reports_list/';
        return view('reports.to_check.show', compact('item', 'link'));
    }

    public function read($id, Request $request)
    {
        $item = $this->reportRepository->getById($id) ?? abort(404);
        $status = $item->update(['letto' => 0]);
        return response()->json([
          'status' => $status,
          'debug' => $item
        ], 200);
    }

    public function ajax(Request $request)
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);

        $data = $this->reportRepository->getFiltered($request->all());
        $index = 0;
        $data_json = [];

        foreach ($data->items as $item){

            $statusColor = $item->statusColor;

            $data_json[] = [
                $item->intervention->location->client->ragione_sociale ?? '',
                $item->formattedDate ?? '',
                $item->intervention->formattedDate ?? '',
                $item->intervention->tipologia ?? '',
                $item->technicianNames ?? '',
                $item->reportNumber ?? '',
                $item->statusText ?? '',
                $item->letto ?? '',
            ];

            if (in_array("V", $chars)){
                array_push($data_json[$index], "<a href=\"/reports_list/$item->id_rapporto/\" class=\"btn btn-xs btn-info\" title=\"Visualizza\"><i class=\"fas fa-eye\"></i></a>");
            }
            if (in_array("D", $chars)){
                array_push($data_json[$index], "<button onclick='deleteItem(this)' data-id=\"".$item->id_rapporto."\" class=\"btn btn-xs btn-warning\" title=\"Elimina\"><i class=\"fas fa-trash\"></i></button>");
            }

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
