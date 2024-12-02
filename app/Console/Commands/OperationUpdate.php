<?php
namespace App\Console\Commands;

use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;

use Illuminate\Console\Command;

class OperationUpdate extends Command {

    protected $signature = 'operation_machinery:update';

    protected $description = 'Update description of interventi machinery by intervent';

    public function __construct()
    {

        parent::__construct();

    }

    public function handle()
    {

        $this->info('Start...');

        try{

            $this->operationsLoop();

        } catch(\Throwable $e) {

            $this->info($e);
            $this->writeLog('imagesTransferLogs.txt', '{' . date('Y-m-d H:i:s') . '}' . json_encode( (array)($e->getMessage().":". $e->getLine()) ), 'a');

        } catch(\Exception $e){

            $this->info('Exception: ' . $e);
            $this->writeLog('imagesTransferLogs.txt', '{' . date('Y-m-d H:i:s') . '}' . json_encode( (array)($e->getMessage().":". $e->getLine()) ), 'a');

        }

        $this->info('Finished');

    }

    function operationsLoop() : int
    {

        $page = 0;
        $limit = 150;
        
        while($this->pageLoop($page, $limit)){ //infinite while

            $operations = Operation::take($limit)->skip($page * $limit)->get();

            //dd($operations);

            try {

                if(isset($operations) && $operations){

                    foreach($operations as $operation){
                        //dump($operation->note);

                        $exists = OperationMachinery::where('id_intervento', $operation->id_intervento)->count();
                        //dd($exists);
                        if($exists){
                            OperationMachinery::where('id_intervento', $operation->id_intervento)->update(['desc_intervento' => $operation->note ?? null]);
                        }

                        dump('Looped intervent ' . $operation->id_intervento);

                    }

                }


                //OperationMachinery::where('')
                

            } catch(\Throwable $e) {
                $this->info($e);
                $this->writeLog('operationUpdateLogs.txt', '{' . date('Y-m-d H:i:s') . '}' . json_encode( (array)($e->getMessage().":". $e->getLine()) ), 'a');
            } catch(\Exception $e){
                $this->info('Exception: ' . $e);
                $this->writeLog('operationUpdateLogs.txt', '{' . date('Y-m-d H:i:s') . '}' . json_encode( (array)($e->getMessage().":". $e->getLine()) ), 'a');
            }

            $this->clearConsole();

            $this->info("Looped page: {$page}");

            $page++;

        } //end while

        return $page;

    }

    private function pageLoop($page = 0, $limit = 100) : int
    {
        return Operation::take($limit)->skip($page * $limit)->get()->count();
    }

    function writeLog($filename, $data, $p = 'a'){
        $path = storage_path($filename);
        $file = fopen($path, $p);
        fwrite($file, $data);
        fclose($file);

    }

    function getFileContents($path){

        $path = storage_path($path);
        if(file_exists($path)){
            $contents = file_get_contents($path);
            return $contents;
        }

        return false;
    }

    function clearConsole(){

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }

    }

}