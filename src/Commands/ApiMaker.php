<?php

namespace Hemend\Api\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ApiMaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-maker
            {name : The Service Name of the api service}
            {version : The Service Version of the api service}
            {method : The Service Method of the api service}
            {--flag=private : The Flag of the api method. (public|private)}
            {--force}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = Artisan::call('make:api-service', [
            'name' => $this->argument('name'),
            '--force' => $this->option('force'),
        ]);

        $version = Artisan::call('make:api-version', [
            'name' => $this->argument('name'),
            'version' => $this->argument('version'),
            '--force' => $this->option('force'),
        ]);

        $method = Artisan::call('make:api-method', [
            'name' => $this->argument('name'),
            'version' => $this->argument('version'),
            'method' => $this->argument('method'),
            '--flag' => $this->option('flag'),
            '--force' => $this->option('force'),
        ]);

        if($service == -1) {
            $this->error('The service `'.$this->argument('name').'` already exists.');
        } else if($service != 1) {
            $this->error('Service error code: ' . $service );
        }

        if($version == -1) {
            $this->error('The version `'.$this->argument('version').'` already exists.');
        } else if($version != 1) {
            $this->error('Version error code: ' . $version );
        }

        if($method == -1) {
            $this->error('The method `'.$this->argument('method').'` already exists.');
        } else if($method != 1) {
            $this->error('Method error code: ' . $method );
        } else {
            $this->info('The Method was created successfully.');
        }
    }
}
