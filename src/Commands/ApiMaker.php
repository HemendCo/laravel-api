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
      {package : The Service Package of the api service}
      {endpoint : The Service Endpoint of the api service}
      {--guard=}
      {--flag=private : The Flag of the api method. (public|public_only|private|private_only)}
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
        '--guard' => $this->option('guard'),
        '--force' => $this->option('force'),
      ]);

      $version = Artisan::call('make:api-version', [
        'name' => $this->argument('name'),
        'version' => $this->argument('version'),
        '--force' => $this->option('force'),
      ]);

      $package = Artisan::call('make:api-package', [
        'name' => $this->argument('name'),
        'version' => $this->argument('version'),
        'package' => $this->argument('package'),
        '--force' => $this->option('force'),
      ]);

      $endpoint = Artisan::call('make:api-endpoint', [
        'name' => $this->argument('name'),
        'version' => $this->argument('version'),
        'package' => $this->argument('package'),
        'endpoint' => $this->argument('endpoint'),
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

      if($package == -1) {
        $this->error('The package `'.$this->argument('package').'` already exists.');
      } else if($package != 1) {
        $this->error('Package error code: ' . $package );
      }

      if($endpoint == -1) {
        $this->error('The endpoint `'.$this->argument('endpoint').'` already exists.');
      } else if($endpoint != 1) {
        $this->error('Endpoint error code: ' . $endpoint );
      } else {
        $this->info('The Endpoint was created successfully.');
      }
    }
}
