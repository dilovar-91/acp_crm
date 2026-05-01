<?php

namespace App\Console\Commands;

use App\Http\Controllers\Import\ImportVLLController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportVLL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-all-vll:import';
    protected $description = 'Import All LL && S from a source';


    public function handle()
    {
        $importController = new ImportVLLController();
        $importController->import(); // Call the import method from your controller
        Log::alert('LL && S All imported!');
    }
}
