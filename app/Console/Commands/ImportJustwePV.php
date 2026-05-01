<?php

namespace App\Console\Commands;

use App\Http\Controllers\Import\ImportJustwePvController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportJustwePV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'justwe-pv:import';
    protected $description = 'Import justwe pv from a source';


    public function handle()
    {
        $importController = new ImportJustwePvController();
        $importController->import(); // Call the import method from your controller
        Log::alert('Justwe PV imported successfully!');
    }
}
