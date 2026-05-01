<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportAuraController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportAura extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vp-aura:import';
    protected $description = 'Import Aura from a source';


    public function handle()
    {
        $importController = new ImportAuraController();
        $importController->import();
        Log::alert('VDL Aura imported!');
    }
}
