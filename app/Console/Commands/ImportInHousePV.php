<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportInHousePVController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportInHousePV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pv-inhouse:import';
    protected $description = 'Import All InHouse';


    public function handle(): void
    {
        $importController = app(ImportInHousePVController::class);
        $importController->import();
        Log::alert('PV InHouse imported!');
    }
}
