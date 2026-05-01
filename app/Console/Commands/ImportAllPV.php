<?php

namespace App\Console\Commands;

use App\Http\Controllers\Import\ImportAllPVController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportAllPV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pv-all:import';
    protected $description = 'Import All from a source';


    public function handle()
    {
        $importController = new ImportAllPVController();
        $importController->import();
        Log::alert('PV ALL imported!');
    }
}
