<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Report\Report;
use App\Models\Operation\Operation;
use App\CoreUsers as User;
use App\Helpers\Converter;
use App\Models\Clienti as Client;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $this->data['todayOperationsCount'] = Operation::where('data', date('Y-m-d'))->count();
        $this->data['unreadReportsCount'] = Report::where('letto', 0)->count();
        $this->data['wrongVatNumberCount'] = Client::where('partita_iva_errata', 1)->count();
      //  $this->data['technicians'] = $this->getTechnicians();
        return view('pages.home', $this->data);
    }

    public function getTechnicians()
    {
        $operations = Operation::where('data', 'like', date('Y-m') . "%")->whereHas('report')->with('report')->get();
        $response = [];
        $operations->each(function($item) use(&$response) {
            collect($item->techniciansArray)->each(function($id, $position) use(&$response, $item) {
                if(!($response[$id] ?? false)) {
                    $response[$id] = [
                        'fullName' => $item->technicians()[$position]->fullName,
                        'workingMinutes' => 0,
                        'travelMinutes' => 0
                    ];
                }
                $response[$id]['workingMinutes'] = 
                    ($response[$id]['workingMinutes'] ?? 0 ) + Converter::toMinutes($item->report->{'ore_lavoro' . ($position + 1)});
                $response[$id]['travelMinutes'] = 
                    ($response[$id]['travelMinutes'] ?? 0 ) + Converter::toMinutes($item->report->{'ore_viaggio' . ($position + 1)});
            });
        });
        return collect($response)->map(function($item) {
            return (object)[
                'fullName' => $item['fullName'],
                'workingHours' => Converter::toHours($item['workingMinutes']),
                'travelHours' => Converter::toHours($item['travelMinutes']),
            ];
        });
    }

    private function deleteOperationsData()
    {
        $operations = Operation::whereIn('id_sede', [26, 27, 38, 39, 40])->get();
        $operations->each(function($item) {
            Storage::deleteDirectory('reports/photos/' . $item->id_intervento);
            Storage::deleteDirectory('reports/signatures/' . $item->id_intervento);
            $item->reports->each(function($report) {
                $report->photos->each(function($photo) {
                    $photo->delete();
                });
                $report->delete();
            });
            
            $item->machineries->each(function($machinery) {
                $machinery->delete();
            });

            $item->equipments->each(function($equipment) {
                $equipment->delete();
            });

            $item->delete();
        });
    }
}