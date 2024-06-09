<?php

namespace Hemend\Api\Commands;

use Illuminate\Support\Facades\Artisan;
use Hemend\Api\Foundation\GeneratorCommand;
use Hemend\Library\Glob;

class ApiBasic extends GeneratorCommand
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:api-basic
      {name : The Service Name of the api service}
      {version : The Service Version of the api service}
      {--guard=}
      {--mode=client : The Flag of the api method. (client|admin)}
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
    if (!in_array($this->option('mode'), ['client', 'admin'])) {
      $this->error('The flag is not valid. please use `client` or `admin`');
      return -1;
    }

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

    if($service == -1) {
      $this->warn('The service `'.$this->argument('name').'` already exists.');
    } else if($service != 1) {
      $this->error('Service error code: ' . $service );
    }

    if($version == -1) {
      $this->warn('The version `'.$this->argument('version').'` already exists.');
    } else if($version != 1) {
      $this->error('Version error code: ' . $version );
    }

    $version_name = $this->createVersionName($this->argument('version'));

    $version_namespace = $this->qualifyClass($version_name);
    $version_namespace = substr($version_namespace, 0, strrpos( $version_namespace, '\\'));
    $service_namespace = substr($version_namespace, 0, strrpos( $version_namespace, '\\'));

    $version_base_path = __DIR__ . '/stubs/'.$this->option('mode').'/';
    $offset = strlen($version_base_path);
    foreach(Glob::recursive($version_base_path, '*.stub') as $stub_path) {
      $file_remove_stub_ext = preg_replace('/\.stub$/', '', substr($stub_path, $offset));
      $name = $this->qualifyClass($file_remove_stub_ext);
      $path = $this->getPath($name);

      if ($this->files->isFile($path) && !$this->option('force')) {
        $this->warn('The Method "'.$name.'" already exists.');
        continue;
      }

      $this->makeDirectory($path);

      $this->files->put($path, $this->sortImports($this->buildClass($name, $stub_path, $version_namespace)));

      $this->info($name.' created successfully.');
    }

    return 1;
  }

  /**
   * Build the class with the given name.
   *
   * @param  string  $name
   * @return string
   *
   * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
   */
  protected function buildClass($name, $stub_path=null, $version_namespace=null)
  {
    $content = $this->files->get($stub_path);

    $stub = $this->replaceNamespace($content, $name)->replaceClass($content, $name);

    $version_name = substr($version_namespace, strrpos( $version_namespace, '\\')+1);

    return str_replace(['{{ version }}', '{{ version_namespace }}'], [$version_name, $version_namespace], $stub);
  }

  /**
   * Build the class with the given name.
   *
   * @param  string  $name
   * @return string
   *
   * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
   */
  protected function buildTraitClass($name, $stub_path=null, $service_namespace=null)
  {
    $content = $this->files->get($stub_path);

    $stub = $this->replaceNamespace($content, $name)->replaceClass($content, $name);

    $service_name = substr($service_namespace, strrpos( $service_namespace, '\\')+1);

    return str_replace(['{{ service }}'], [$service_name], $stub);
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return null;
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    $service_name = $this->createServiceName($this->getNameInput());
    $version_name = $this->createVersionName($this->argument('version'));
    return $rootNamespace.'\Http\Controllers\Api\\'.$service_name.'\\'.$version_name;
  }
}
