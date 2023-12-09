<?php

namespace Hemend\Api\Commands;

use Hemend\Api\Foundation\GeneratorCommand;

class ApiPackageMaker extends GeneratorCommand
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:api-package
      {name : The Service Name of the api service}
      {version : The Service Version of the api service}
      {package : The Service Package of the api service}
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
    if ($this->isReservedName($this->getNameInput())) {
      $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

      return -4;
    }

    $service_namespace = $this->qualifyClass($this->createServiceName($this->getNameInput()));
    $service_namespace = substr($service_namespace, 0, strrpos( $service_namespace, '\\'));
    $service_path = $this->getPath($service_namespace);

    if (! $this->files->isFile($service_path)) {
      $this->error('The service namespace "'.$service_namespace.'" is not exists.');
      return -3;
    }

    $version_namespace = $this->qualifyClass($this->createVersionName($this->argument('version')));
    $version_namespace = substr($version_namespace, 0, strrpos( $version_namespace, '\\'));
    $version_path = $this->getPath($version_namespace);

    if (! $this->files->isFile($version_path)) {
      $this->error('The service namespace "'.$version_namespace.'" is not exists.');

      return -2;
    }

    $package = $this->argument('package');

    $name = $this->qualifyClass($this->createPackageName($package));

    $path = $this->getPath($name);

    if ($this->files->isFile($path) && !$this->option('force')) {
      $this->error('The Package "'.$package.'" already exists.');
      return -1;
    }

    $this->makeDirectory($path);

    $this->files->put($path, $this->sortImports($this->buildClass($name, $service_namespace, $version_namespace, $package)));

    $this->info($name.' created successfully.');

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
  protected function buildClass($name, $service_namespace=null, $version_namespace=null, $package=null)
  {
    $stub = parent::buildClass($name);
    $service_name = substr($service_namespace, strrpos( $service_namespace, '\\')+1);
    $version_name = substr($version_namespace, strrpos( $version_namespace, '\\')+1);

    $stub = str_replace(['{{ service }}', '{{ version }}', '{{ package }}'], [$service_name, $version_name, $package], $stub);

    return $stub;
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return __DIR__ . '/stubs/package.stub';
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
