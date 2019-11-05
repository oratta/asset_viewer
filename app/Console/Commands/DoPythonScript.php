<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DoPythonScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:python 
                            {scriptFile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'do script';

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
        $venvActivatePath = app_path('Console/Commands/script/py/venv/bin/activate');
        $venvActivateScript = "source $venvActivatePath";

        $pyPath = app_path('Console/Commands/script/py/');
        $scriptFilePath = $pyPath . $this->argument('scriptFile');

        system($venvActivateScript . ";" . "python " . $scriptFilePath);
    }
}
