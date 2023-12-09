<?php

namespace Hemend\Api\Commands;

use Hemend\Api\Foundation\GeneratorCommand;

class ApiEndpointMaker extends GeneratorCommand
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:api-endpoint
      {name : The Service Name of the api service}
      {version : The Service Version of the api service}
      {package : The Service Package of the api service}
      {endpoint : The Service Endpoint of the api service}
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
    if (!in_array($this->option('flag'), ['public', 'public_only', 'private', 'private_only'])) {
      $this->error('The flag is not valid. please use `public` or `public_only` or `private` or `private_only`');

      return -5;
    }

    if ($this->isReservedName($this->getNameInput())) {
      $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

      return -4;
    }

    $service_namespace = $this->qualifyClass($this->createServiceName($this->getNameInput()));
    $service_namespace = substr($service_namespace, 0, strrpos( $service_namespace, '\\'));
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

    $package_namespace = $this->qualifyClass($this->createPackageName($this->argument('package')));
    $package_namespace = substr($package_namespace, 0, strrpos( $package_namespace, '\\'));
    $package_path = $this->getPath($package_namespace);

    if (! $this->files->isFile($package_path)) {
      $this->error('The service namespace "'.$package_namespace.'" is not exists.');

      return -2;
    }

    $endpoint = $this->argument('endpoint');

    $name = $this->qualifyClass($this->createEndpointName($endpoint));

    $path = $this->getPath($name);

    if ($this->files->isFile($path) && !$this->option('force')) {
      $this->error('The Method "'.$name.'" already exists.');

      return -1;
    }

    $this->makeDirectory($path);

    $this->files->put($path, $this->sortImports($this->buildClass($name, $package_namespace, $this->getFlag())));

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
  protected function buildClass($name, $package_namespace=null, $flag=null)
  {
    $stub = parent::buildClass($name);
    $package_name = substr($package_namespace, strrpos( $package_namespace, '\\')+1);

    $stub = str_replace(['{{ package }}', '{{ flag }}'], [$package_name, $flag], $stub);

    return $stub;
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return __DIR__ . '/stubs/endpoint.stub';
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
    $package_name = $this->createPackageName($this->argument('package'));
    return $rootNamespace.'\Http\Controllers\Api\\'.$service_name.'\\'.$version_name.'\\'.$package_name;
  }

  protected function getFlag() {
    switch ($this->option('flag')) {
      case 'public':
        $flag = 'PERMISSION_FLAG_PUBLIC';
        break;
      case 'public_only':
        $flag = 'PERMISSION_FLAG_PUBLIC_ONLY';
        break;
      case 'private':
        $flag = 'PERMISSION_FLAG_PRIVATE';
        break;
      case 'private_only':
        $flag = 'PERMISSION_FLAG_PRIVATE_ONLY';
        break;
    }

    return $flag;
  }
}
