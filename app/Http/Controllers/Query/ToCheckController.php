<?php

namespace App\Http\Controllers\Report;

use App\Models\Clienti;
use App\Models\Report\Report;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToCheckController extends Controller
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
        $items = Report::where('letto', 0)->get();        
        $activeTechnicians = $this->userRepository->getActiveTechnicians();
        $title = 'Rapporti da verificare';
        $link = '/reports_to_check/';

        return view('reports.to_check.da_verificare', compact('items', 'chars', 'activeTechnicians', 'title', 'link'));
    }

    public function searchClient(Request $request)
    {
        if($request->value){
            $result = Clienti::where('ragione_sociale', 'like', '%' . $request->value . '%')
                ->where('cliente_visibile', 1)
                ->get();
        } else {
            $result = null;
        }
        return response()->json($result, 200);
    }

    public function show($id)
    {
        $item = $this->reportRepository->getById($id) ?? abort(404);
        $item->update(['letto' => 1]);
        $item->save();
        $link = '/reports_to_check/';
        return view('reports.to_check.show', compact('item', 'link'));
    }
}
