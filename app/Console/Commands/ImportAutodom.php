<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportAutodomController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportAutodom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vp-autodom:import';
    protected $description = 'Import Autodom from a source';


    public function handle()
    {
        $importController = new ImportAutodomController();
        $importController->import();
        Log::alert('VDL Autodom imported!');
    }
}
