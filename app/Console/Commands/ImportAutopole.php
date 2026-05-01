<?php

namespace App\Console\Commands;


use App\Http\Controllers\ImportAutopoleController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportAutopole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vp-autopole:import';
    protected $description = 'Import Autopole from a source';


    public function handle()
    {
        $importController = new ImportAutopoleController();
        $importController->import();
        Log::alert('VDL Autopole imported!');
    }
}
