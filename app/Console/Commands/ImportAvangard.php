<?php

namespace App\Console\Commands;


use App\Http\Controllers\ImportAvangardController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportAvangard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vp-avangard:import';
    protected $description = 'Import Avangard from a source';


    public function handle()
    {
        $importController = new ImportAvangardController();
        $importController->import();
        Log::alert('VDL Avangard imported!');
    }
}
