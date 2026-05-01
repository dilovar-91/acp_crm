<?php

namespace App\Console\Commands;

use App\Http\Controllers\Import\ImportVLLKrasnodarController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportVLLKrasnodar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-krasnodar-vll:import';
    protected $description = 'Import Kr vll from a source';


    public function handle()
    {
        $importController = new ImportVLLKrasnodarController();
        $importController->import(); // Call the import method from your controller
        Log::alert('VLL Kr imported!');
    }
}
