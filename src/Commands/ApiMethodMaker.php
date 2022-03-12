<?php

namespace Hemend\Api\Commands;

use Hemend\Api\Foundation\GeneratorCommand;

class ApiMethodMaker extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-method
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
        if (!in_array($this->option('flag'), ['public', 'private'])) {
            $this->error('The flag is not valid. please use `public` or `private`');

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

        $method = $this->argument('method');

        $name = $this->qualifyClass($this->createMethodName($method));

        $path = $this->getPath($name);

        if ($this->files->isFile($path) && !$this->option('force')) {
            $this->error('The Method "'.$name.'" already exists.');

            return -1;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name, $version_namespace)));

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
    protected function buildClass($name, $version_namespace=null)
    {
        $stub = parent::buildClass($name);
        $version_name = substr($version_namespace, strrpos( $version_namespace, '\\')+1);

        $stub = str_replace(['{{ version }}'], [$version_name], $stub);

        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/method-'.$this->option('flag').'.stub';
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
