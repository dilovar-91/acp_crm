<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportJustweController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportJustwe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:justwe';
    protected $description = 'Import justwe from a source';


    public function handle()
    {
        $importController = new ImportJustweController();
        $importController->import(); // Call the import method from your controller
        Log::info('Justwe imported successfully!' . date('d-M-Y H:i:s'));
    }
}
