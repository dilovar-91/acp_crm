<?php

namespace App\Console\Commands;

use App\Http\Controllers\Import\ImportVaController;
use Illuminate\Console\Command;

class ImportVA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request-va:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $item = new ImportVaController();
        $item->import();
    }
}
