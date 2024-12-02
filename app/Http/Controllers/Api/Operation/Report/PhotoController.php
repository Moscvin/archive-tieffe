<?php

namespace App\Http\Controllers\Api\Operation\Report;

use App\Models\Report\ReportPhoto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; 
use App\Models\Report\Report;
use App\Http\Controllers\Auth\EmailController;
use App\Export\Pdf\ReportPdf;
use App\CoreAdminOptions;


class PhotoController extends Controller
{
    public function main(Request $request) {
        try {
            if(isset($request->firma) && $request->firma) {
                Storage::delete(Storage::files('reports/signatures/' . $request->id_intervento));
                $firma = Storage::putFile('reports/signatures/' . $request->id_intervento, $request->firma);
                Report::where('id_rapporto', $request->id_rapporto)->update([
                    'firma' => $firma,
                ]);
            }

            if(isset($request->rapporti_foto) && count($request->rapporti_foto)) {
                foreach($request->rapporti_foto as $photo) {
                    $object[] = $photo;
                    $reportPhoto = Storage::putFile('reports/photos/' . $request->id_intervento, $photo);
                    if($reportPhoto) {
                        $photoNew[] = ReportPhoto::create([
                            'id_rapporto' => $request->id_rapporto,
                            'filename' => $reportPhoto
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'ok',
                            'data' => [
                                'message' => 'One photo is not detected'
                            ]
                        ], 200);
                    }
                    
                }
            }
            $report = Report::where('id_rapporto', $request->id_rapporto)->first();

            $mailSent = false;
            if($report->stato != 3 && $report->stato != 4){
                $mailSent = true;
                $this->sendEmail($report);
            }
            
            return response()->json([
                'status' => 'ok',
                'data' => [
                    'message' => 'Uploaded with success',
                    'photos' => count($request->rapporti_foto ?? []),
                    'models' => $request->rapporti_foto,
                    'mailSent' => $mailSent,
                    'report' => $report
                ]
            ], 200);
        } catch(\Exception $e) {
            Storage::put('file.txt', $e->getMessage());
            Storage::put('file.txt', $e->getLine());
            return response()->json([
                'status' => 'error',
                'data' => [
                    'error_code' => '500',
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    private function sendEmail($report)
    {
        $mailsArray = [];
        if ($report->mail_send) {
            $mailsArray = array_filter(explode(';', $report->mail_send));
        }
        $mailsArray[] = $this->getoption('MAIL_FROM_ADDRESS');
        $data = [
            'subject' => "Rapporto di intervento nr. $report->reportNumber del $report->operationDate",
            'to_email_address' => array_shift($mailsArray),
            'to_name' => '',
            'attachment' => (new ReportPdf($report->id_rapporto))->output(),
            'attachmentName' => "Rapporto del $report->operationDate.pdf",
            'text' => "
                <p>In allegato alla presente si invia il Rapporto di intervento numero $report->reportNumber
                relativo al nostro intervento del $report->operationDate.</p>
            ",
        ];

        if(count($mailsArray)) {
            $data['cc_email_address'] = $mailsArray;
        }
        return (new EmailController)->send2($data);
    }

    public function getoption($key){
        $option = CoreAdminOptions::where('description',$key)->first();
        return $option->value;
    }
}
