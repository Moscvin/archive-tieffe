<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class FactureCron extends Command {

    protected $signature = 'facture:cron';

    protected $description = 'Facture cron';

    public function __construct()
    {

        parent::__construct();

    }

    public function handle()
    {
        $my_response = "";

        $my_response = app(\App\Http\Controllers\NotificationController::class)->facture("cron");

        $this->info('Facture:Cron Cummand Run successfully! '.$my_response);

    }

}