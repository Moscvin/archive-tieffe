<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use Symfony\Component\Process\Process;

class ReportsImagesResizeCron extends Command {

    protected $signature = 'reports_photos_resize:cron';

    protected $description = 'Reports photos resize cron';

    public function __construct()
    {

        parent::__construct();

    }

    public function handle()
    {
	 	exec('find ' . storage_path('app/reports/photos') . ' -type f -name "*.png" | while read file; do convert "$file" "${file%.png}.jpeg" && rm "$file"; done');
        exec('find '.storage_path('app/reports/photos').' -type f -name "*.jpeg" -size +300k | xargs jpegoptim --max=60 -f --strip-all --size=300k \'{}\' \;');
		DB::update("UPDATE `rapporti_foto` SET `filename` = REPLACE(`filename`, '.png', '.jpeg') WHERE `filename` LIKE '%.png%'");
        $this->info('Reports images resized successfully! ');

    }

}


