<?php

namespace Hemend\Api\Commands;

use Hemend\Api\Foundation\GeneratorCommand;

class ApiVersionMaker extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-version
            {name : The Service Name of the api service}
            {version : The Service Version of the api service}
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

            return -3;
        }

        $service_namespace = $this->qualifyClass($this->createServiceName($this->getNameInput()));
        $service_namespace = substr($service_namespace, 0, strrpos( $service_namespace, '\\'));
        $service_path = $this->getPath($service_namespace);

        if (! $this->files->isFile($service_path)) {
            $this->error('The service namespace "'.$service_namespace.'" is not exists.');
            return -2;
        }

        $version = $this->argument('version');

        $name = $this->qualifyClass($this->createVersionName($version));

        $path = $this->getPath($name);

        if ($this->files->isFile($path) && !$this->option('force')) {
            $this->error('The Version "'.$version.'" already exists.');
            return -1;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name, $service_namespace, $version)));

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
    protected function buildClass($name, $service_namespace=null, $version=null)
    {
        $stub = parent::buildClass($name);
        $service_name = substr($service_namespace, strrpos( $service_namespace, '\\')+1);

        $stub = str_replace(['{{ service }}', '{{ version }}'], [$service_name, $version], $stub);

        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/version.stub';
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
        return $rootNamespace.'\Http\Controllers\Api\\'.$service_name;
    }
}
