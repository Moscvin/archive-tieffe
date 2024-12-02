<?php

namespace App\Export\Pdf;

use View;

use Illuminate\Http\Request;

use App\Http\Requests;
use PDF;
use App\Models\Report\Report;
use App\Models\Report\ReportPhoto;
use App\Models\EquipmentOrderIntervention;
use App\Models\Location;
use App\CoreUsers;
use App\Models\Clienti;

use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;
class ReportPdf
{
	private $id;
    function __construct($id)
    {
		$this->id = $id;
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

    public function prepare(){

        $id = $this->id;

        $report = Report::where('id_rapporto', $id)->with(['operation' => function($query){
            $query->with(['location' => function($query){
                $query->with('client');
            },'machineries']);
        },'machineries'])->firstOrFail();

/*        $location1 = OperationMachinery::where('id_intervento', $report->id_intervento)->with(['operation' => function($query){
            $query->with('headquarter');
        }])->first();*/


        //dd($location1->operation->headquarter->indirizzo_comune);

        $operation = Operation::where('id_intervento', $report->id_intervento)->first();
        $photos = ReportPhoto::where('id_rapporto', $report->id_rapporto)->get();


        $all_tehnicians = explode(' ', trim(str_replace(';', ' ', $operation->tecnico) ) );
        $tehnicians_number = count($all_tehnicians);

        $tehnicians_array = [];
        foreach($all_tehnicians as $tehnician){
            $tehnicians_array[] = CoreUsers::select('name', 'family_name')->where('id_user', $tehnician)->first()->toArray();
        }


        $tehnicians_string = [];
        foreach($tehnicians_array as $tehnician){

            $tehnicians_string[] = $tehnician['name']. ' ' . $tehnician['family_name'];

        }
        $tehnicians_string = implode(' - ', $tehnicians_string);

        $client = $report->operation->headquarter->client;



        if(($facture_address = Location::where('id_cliente', $client->id)->where('tipologia', 'Sede legale') )->count() > 0){

            $facture_address = $facture_address->first();
        }
        elseif(  ($facture_address = Location::where('id_cliente', $client->id)->where('tipologia', 'Sede amministrativa') )->count() > 0){
            $facture_address = $facture_address->first();
        }
				elseif( ($facture_address = Location::where('id_cliente', $client->id)->where('tipologia', 'Sede operativa') )->count() > 0){
            $facture_address = $facture_address->first();
        }
				else {
            $facture_address = $report->operation->headquarter;
        }

        $address = $facture_address; //$report->operation->headquarter;


        if($report->operation->fatturare_a <> 0){

            $invoice_client = Clienti::where('id', $report->operation->fatturare_a)->first();

        }




/*        if( ($address_2 = Location::where('id_sedi', $report->luogo_intervento) )->count() > 0 ){
            $address_2 = $address_2->first();
        } else {
            $address_2 = $report->operation->headquarter;
        }   */

        $InterventionEquipment = EquipmentOrderIntervention::where('id_intervento',$report->operation->id_intervento)->get();

        $totalWorkedMinutes = 0;
        $totalTravelMinutes = 0;
        $totalTravelKilometers = 0;
        $hours_table = [];

        $_count = 1;

        foreach($tehnicians_array as $tehnician){


            //if($_count <= 3){

                $totalWorkedMinutes += $this->toMinutes($report->{"ore_lavoro".$_count});
                $totalTravelMinutes += $this->toMinutes($report->{"ore_viaggio".$_count});
                $totalTravelKilometers += $report->{"km_viaggio".$_count};

                $hours_table[] = [
                    'full_name'=>$tehnician['name']. ' ' . $tehnician['family_name'],
                    'worked_hours'=>$report->{"ore_lavoro".$_count},
                    'travel_hours'=>$report->{"ore_viaggio".$_count},
                    'travel_km'=>intval($report->{"km_viaggio".$_count})
                ];

            //}
            $_count++;
        }

        //dd($tehnicians_array);
				//dd($address);

        $signatory = $report->firmatario ?? '';
        $signature = $report->firma ?? '';
        $signatureExists = file_exists(storage_path("app/$signature"));

        $data = [
            'id_intervento'=> $report->id_intervento,
            'id_report'=>$report->progressivo,
            'data_intervento'=> date('d/m/Y', strtotime($report->data_intervento)),
            'count_technicians'=>$tehnicians_number,
            'technicians'=>$tehnicians_string,
            'invoiceTo' => $report->operation->fatturare_a,
            'report' => $report,
            'location' => $address,
            'facture_location' => $facture_address,
            'client' => $client,
            //'invoice_client' => $invoice_client,
            'intervention_equipment'=>$InterventionEquipment,
            'hours_table' => $hours_table,
            'total_worked_hours'=>$this->toHours($totalWorkedMinutes),
            'total_travel_hours'=>$this->toHours($totalTravelMinutes),
            'total_travel_km'=>$totalTravelKilometers,
            'note_length'=>str_word_count($report->note),
            'note'=>$report->note,
            'signatory'=>$signatory,
            'signature'=>$signature,
            'signatureExists' => $signatureExists
        ];

        if($report->operation->fatturare_a <> 0) {

            $data['invoice_client'] = $invoice_client;
        }

        return $data;

    }

	public function generate(){


        $data = $this->prepare();
        $pdf = PDF::setOptions([
						'logOutputFile' => storage_path('logs/log.htm'),
						'tempDir' => storage_path('logs/'),
						"enable_remote" => true,
						"enable_html5_parser" => true,
						"default_font" => "Helvetica"
				])->setPaper('A4');
				
				switch ($data['report']->machineries->first()->machinery->tipologia) {
					case 'Caldo':
						$pdf->loadView('report_pdf_caldo', $data);
						break;
					case 'Freddo':
						$pdf->loadView('report_pdf_freddo', $data);
						break;
					case 'Sopralluogo Caldo':
						$pdf->loadView('report_pdf_sopraluogo_caldo', $data);
						break;
					case 'Sopralluogo Freddo':
						$pdf->loadView('report_pdf_sopraluogo_freddo', $data);
						break;
				}
        $filename = 'Rapporto.pdf';

        return [
            'pdf' => $pdf,
            'filename' => $filename
        ];
	}

	public function download(){

		$pdf = $this->generate($this->id);
        return $pdf['pdf']->download($pdf['filename']);
    }

    public function output()
    {
		$pdf = $this->generate($this->id);
        return $pdf['pdf']->download($pdf['filename']);
	}

    public function stream(){

        $pdf = $this->generate($this->id);
        return $pdf['pdf']->stream('example.pdf');

    }
}


?>
