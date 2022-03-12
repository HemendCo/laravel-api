<?php

namespace Hemend\Api\Commands;

use Hemend\Api\Foundation\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ApiVersionCopy extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:api-version-copy
			{name : The Service Name of the api service}
			{old_version : The Service Old Version of the api service}
			{new_version : The Service New Version of the api service}
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
		$service_namespace = '\Http\Controllers\Api\\'.$this->createServiceName($this->getNameInput());

		$old_version_name = $this->createVersionName($this->argument('old_version'));
		$old_version_namespace = $this->qualifyClassVersion($old_version_name);
		$old_version_path = $this->getPath($old_version_namespace);

		if (! $this->files->isFile($old_version_path)) {
			$this->error('The version file "'.$old_version_path.'" is not exists.');
			return -3;
		}

		$new_version_name = $this->createVersionName($this->argument('new_version'));
		$new_version_namespace = $this->qualifyClassVersion($new_version_name);
		$new_version_path = $this->getPath($new_version_namespace);

		if ($this->files->isFile($new_version_path) && !$this->option('force')) {
			$this->error('The Version "'.substr($new_version_namespace, strpos($new_version_namespace, '\\', 21)+1).'.php" already exists.');
		} else {
			$this->makeDirectory($new_version_path);

			$stub = __DIR__ . '/stubs/version-copy.stub';
			$this->files->put($new_version_path, $this->sortImports($this->buildClassVersion($stub, $new_version_namespace, $old_version_namespace, $this->argument('new_version'))));

			$this->info('The Version "'.substr($new_version_namespace, strpos($new_version_namespace, '\\', 21)+1).'.php" created successfully.');
		}

		$stub = __DIR__ . '/stubs/method-copy.stub';
		$_version_dir = dirname($new_version_path).'/'.$new_version_name;
		foreach(glob(dirname($new_version_path).'/'.$old_version_name.'/*.php') as $method_path) {
			$name = basename($method_path, '.php');
			$path = $_version_dir .'/'. $name . '.php';

			$old_method_namespace = $old_version_namespace . '\\' . $name;
			$namespace = $this->qualifyClass($name, $new_version_name);

			if ($this->files->isFile($path) && !$this->option('force')) {
				$this->error('The Method "'.substr($namespace, strpos($namespace, '\\', 21)+1).'.php" already exists.');
				continue;
			}

			$this->makeDirectory($path);

			$this->files->put($path, $this->sortImports($this->buildClass($namespace, $stub, $old_method_namespace)));

			$this->info('The Method "'.substr($namespace, strpos($namespace, '\\', 21)+1).'.php" created successfully.');
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
	protected function buildClassVersion($stub_path, $name, $old_version_namespace, $version)
	{
		$content = $this->files->get($stub_path);

		$stub = $this->replaceNamespace($content, $name)->replaceClass($content, $name);
		$old_version_name = substr($old_version_namespace, strrpos( $old_version_namespace, '\\')+1);

		return str_replace(['{{ oldNamespace }}', '{{ oldVersion }}', '{{ version }}'], [$old_version_namespace, $old_version_name, $version], $stub);
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function buildClass($name, $stub_path=null, $old_method_namespace=null)
	{
		$content = $this->files->get($stub_path);

		$stub = $this->replaceNamespace($content, $name)->replaceClass($content, $name);

		$old_version_name = substr($old_method_namespace, strrpos( $old_method_namespace, '\\')+1);

		return str_replace(['{{ oldNamespace }}', '{{ oldMethod }}'], [$old_method_namespace, $old_version_name], $stub);
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
	 * @param  string  $version_name
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace, $version_name=null)
	{
		$service_name = $this->createServiceName($this->getNameInput());
		return $rootNamespace.'\Http\Controllers\Api\\'.$service_name.'\\'.$version_name;
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getVersionNamespace($rootNamespace)
	{
		$service_name = $this->createServiceName($this->getNameInput());
		return $rootNamespace.'\Http\Controllers\Api\\'.$service_name;
	}

	/**
	 * Parse the class name and format according to the root namespace.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function qualifyClass($name, $version_name=null)
	{
		$name = ltrim($name, '\\/');

		$name = str_replace('/', '\\', $name);

		$rootNamespace = $this->rootNamespace();

		if (Str::startsWith($name, $rootNamespace)) {
			return $name;
		}

		return $this->qualifyClass(
			$this->getDefaultNamespace(trim($rootNamespace, '\\'), $version_name).'\\'.$name
		);
	}

	/**
	 * Parse the class name and format according to the root namespace.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function qualifyClassVersion($name)
	{
		$name = ltrim($name, '\\/');

		$name = str_replace('/', '\\', $name);

		$rootNamespace = $this->rootNamespace();

		if (Str::startsWith($name, $rootNamespace)) {
			return $name;
		}

		return $this->qualifyClass(
			$this->getVersionNamespace(trim($rootNamespace, '\\')).'\\'.$name
		);
	}
}
