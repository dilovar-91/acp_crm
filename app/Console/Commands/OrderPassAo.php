<?php

namespace App\Console\Commands;

use App\Http\Controllers\Victory\PassAoController;
use Illuminate\Console\Command;

class OrderPassAo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pass-ao:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pass AO orders to victory';

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
        $item = new PassAoController();
        $item->execute();
    }
}
