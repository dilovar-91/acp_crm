<?php

namespace App\Console\Commands;

use App\Http\Controllers\Import\ImportVLLKrasnodar2Controller;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportVLLKrasnodar2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-krasnodar-vll2:import';
    protected $description = 'Import Kr vll 2 from a source';


    public function handle()
    {
        $importController = new ImportVLLKrasnodar2Controller();
        $importController->import(); // Call the import method from your controller
        Log::alert('VLL Kr 2 imported!');
    }
}
