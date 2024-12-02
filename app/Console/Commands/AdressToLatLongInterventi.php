<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\GeoDecoder;
use  App\Models\Operation\Operation;

class AdressToLatLongInterventi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interventi:coors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate latitude and longitude coordinates for interventi where null';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $items = Operation::whereNull('lat')->whereNull('long')->whereDate('created_at','<', '2021-10-01')->get();
      if($items->count())
        foreach ($items as $item) {
          $coors = (new GeoDecoder($item->location->first()->indirizzo_via))->getCoors();
          $dataCoors = [
            'long' => $coors->lng,
            'lat' => $coors->lat,
          ];
          //if($item->update($dataCoors))
            $this->info($coors->lat.', '. $coors->lng. ', '.$item->location->indirizzo_via);
          sleep(3);
        }

    }
}
