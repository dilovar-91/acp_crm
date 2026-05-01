<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportAgencyController;
use Illuminate\Console\Command;

class ImportAgency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-agency:import';
    protected $description = 'Import agencies from a source';


    public function handle()
    {
        $importController = new ImportAgencyController();
        $importController->import(); // Call the import method from your controller
        //$this->info('Agencies imported successfully!');
    }
}
