<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class ModulosSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modulos:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa os seeds dos módulos';

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
     * @return mixed
     */
    public function handle()
    {
        $modulos = config('modulos.modulos');

        while (list(, $modulo) = each($modulos)) {

            // Load the migrations
            $class = '\Modulos\\'. $modulo .'\Database\Seeds\\'. $modulo .'Seeder';
            if (class_exists($class)) {
                Artisan::call('db:seed', ['--class' => $class]);

                $this->info($modulo . ' Seed complete!');
            }
        }
    }
}
