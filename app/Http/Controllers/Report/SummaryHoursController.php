<?php

namespace App\Http\Controllers\Report;

use App\Models\Clienti;
use App\Models\Report\Report;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SummaryHoursController extends Controller
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
        $items = $this->reportRepository->getAllByDate();
        $activeTechnicians = $this->userRepository->getActiveTechnicians();
        return view('reports.summary_hours.index', compact('items', 'chars', 'activeTechnicians'));
    }
    private function toHours($val)
    {
        $hours = floor(($val ?? 0) / 60);
        $minutes = ($val ?? 0) % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function toMinutes($val)
    {
        $array = preg_split('/:/', $val ?? '00:00');
        return ($array[0] ?? 0) * 60 + ($array[1] ?? 0);
    }

    public function getData(Request $request)
    {

        $from = isset( $request->date_from ) ? date('Y-m-d', strtotime(str_replace('/','-', $request->date_from) )) : null;
        $to = isset( $request->date_to ) ? date('Y-m-d', strtotime(str_replace('/','-', $request->date_to) )) : null;
        $tecnic = isset($request->tecnic ) && $request->tecnic != '' ? $request->tecnic : null;
        //dd($from);
        $reports = new Report();

        $reports = $reports->when($tecnic != null, function($query) use ($tecnic) {
            $query->whereHas('operation', function($query) use ($tecnic){
                $query->where('tecnico','like', $tecnic.';%')->orWhere('tecnico','like', '%;'.$tecnic.';%');
            }); 
        })->when($from != null, function ($query) use ($from) { 
            $query->whereDate('data_intervento', '>=', $from );
        })->when( $to != null, function ($query) use ($to) { 
            $query->whereDate('data_intervento', '<=', $to );
        })->with('operation');

        $reports = $reports->orderBy('data_intervento', 'desc')->get();

        $rows = [];
        $totalWorkedMinutes = 0;
        $totalTravelMinutes = 0;
        $totalTravelKilometers = 0;

        foreach($reports as $report){
            foreach($report->operation->technicians() as $key => $technician){
                $i = $key + 1;
                if($technician->id_user == $tecnic || !$tecnic) {
                    $totalWorkedMinutes += $this->toMinutes($report->{"ore_lavoro".$i});
                    $totalTravelMinutes += $this->toMinutes($report->{"ore_viaggio".$i});
                    $totalTravelKilometers += $report->{"km_viaggio".$i};

                    $client = $report->operation->headquarter->client;
    
                    $ore_lavoro = "ore_lavoro" . "$i";
                    $ore_viaggio = "ore_viaggio" . "$i";
                    $km_viaggio = "km_viaggio" . "$i";
    
                    $rows[] = [
                        "$report->formattedDate",
                        "$client->ragione_sociale",
                        $technician->fullName,
                        $report->$ore_lavoro,
                        $report->$ore_viaggio,
                        $report->$km_viaggio,
                        $report->reportNumber
                    ];
                }
            }
        }


        if (count($rows) > 0) {        
            $footer = 
            '<tfoot>'.
                '<tr>'.
                '<td colspan="3" class="dt-right"><b>TOTALE:</b></td>'.
                '<td class=""><b>'. $this->toHours($totalWorkedMinutes) .' </b></td>'.
                '<td class=""><b>'. $this->toHours($totalTravelMinutes) .' </b></td>'.
                '<td class=""><b>'. $totalTravelKilometers .' </b></td>'.
                '</tr>'.
            '</tfoot>';

        } else {
            $footer = '';
        }

        return response()->json(['data'=>$rows, 'footer'=>$footer],200);
    }
}
