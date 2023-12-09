<?php

namespace Hemend\Api\Commands;

use Hemend\Api\Foundation\GeneratorCommand;

class ApiServiceMaker extends GeneratorCommand
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:api-service
      {name : The Service Name of the api service}
      {--guard=}
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

      return -2;
    }

    $name = $this->qualifyClass($this->createServiceName($this->getNameInput()));

    $path = $this->getPath($name);

    if ($this->files->isFile($path) && !$this->option('force')) {
      $this->error('The Service "'.$name.'" already exists.');

      return -1;
    }

    $guard = !empty($this->option('guard')) ? $this->option('guard') : config('auth.defaults.guard');

    $this->makeDirectory($path);

    $this->files->put($path, $this->sortImports($this->buildClass($name, $guard)));

    $this->info($this->type.' created successfully.');

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
  protected function buildClass($name, $guard=null)
  {
    $stub = parent::buildClass($name);
    $stub = str_replace(['{{ guard }}'], [$guard], $stub);

    return $stub;
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return __DIR__ . '/stubs/service.stub';
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Http\Controllers\Api';
  }
}
