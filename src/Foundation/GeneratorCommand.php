<?php

namespace Hemend\Api\Foundation;

use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;

abstract class GeneratorCommand extends LaravelGeneratorCommand
{
  /**
   * Create a proper service name:
   * @return string
   */
  protected function createServiceName($name)
  {
    $name = preg_replace('/[^a-zA-Z0-9]+/', '', ucwords(str_replace('.', ' ', $name)));
    $pieces = preg_split('/(?=[A-Z])/', $name, null, PREG_SPLIT_NO_EMPTY);

    $string = '';
    foreach ($pieces as $piece) {
      $string .= ucfirst(strtolower($piece));
    }

    return $string;
  }

  /**
   * Create a proper version name:
   * @return string
   */
  protected function createVersionName($version)
  {
    $string = 'Version_'.str_replace('.', '_', $version);
    return $string;
  }

  /**
   * Create a proper package name:
   * @return string
   */
  protected function createPackageName($name)
  {
    return $this->createServiceName($name);
  }

  /**
   * Create a proper version name:
   * @return string
   */
  protected function createEndpointName($name)
  {
    $name = preg_replace('/[^a-zA-Z0-9]+/', '', ucwords(str_replace('.', ' ', $name)));
    $pieces = preg_split('/(?=[A-Z])/', $name, null, PREG_SPLIT_NO_EMPTY);

    $string = '';
    foreach ($pieces as $piece) {
      $string .= ucfirst(strtolower($piece));
    }

    return $string;
  }
}
