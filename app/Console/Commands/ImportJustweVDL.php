<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportJustweVdlController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportJustweVDL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'justwe-vdl:import';
    protected $description = 'Import justwe vdl from a source';


    public function handle()
    {
        $importController = new ImportJustweVdlController();
        $importController->import(); // Call the import method from your controller
        Log::alert('Justwe VDL imported successfully!');
    }
}
