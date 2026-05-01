<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportVdlController;
use App\Http\Controllers\ImportVdlKrasnodarController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportVDLKrasnodar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-krasnodar-vdl:import';
    protected $description = 'Import Krasnodar vdl from a source';


    public function handle()
    {
        $importController = new ImportVdlKrasnodarController();
        $importController->import(); // Call the import method from your controller
        Log::alert('VDL Krasnodar imported!');
    }
}
