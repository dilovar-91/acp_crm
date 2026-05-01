<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportVdlController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportVDL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-vdl:import';
    protected $description = 'Import vdl from a source';


    public function handle()
    {
        $importController = new ImportVdlController();
        $importController->import(); // Call the import method from your controller
        Log::alert('VDL imported successfully!');
    }
}
