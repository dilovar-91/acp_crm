<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportVdlKrasnodar2Controller;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportVDLKrasnodar2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-krasnodar2-vdl:import';
    protected $description = 'Import Krasnodar2 vdl from a source';


    public function handle()
    {
        $importController = new ImportVdlKrasnodar2Controller();
        $importController->import(); // Call the import method from your controller
        Log::alert('VDL Krasnodar2 imported!');
    }
}
