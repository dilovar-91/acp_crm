<?php

namespace App\Console\Commands;

use App\Http\Controllers\Import\ImportJustweController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportJustweAPlus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:justwe-ap';
    protected $description = 'Import justwe ap from a source';


    public function handle()
    {
        $importController = new ImportJustweController();
        $importController->import(); // Call the import method from your controller
        Log::info('Justwe imported ap successfully!' . date('d-M-Y H:i:s'));
    }
}
