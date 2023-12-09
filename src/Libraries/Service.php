<?php

namespace Hemend\Api\Libraries;

abstract class Service
{
  const PERMISSION_FLAG_PUBLIC = 'PUBLIC';
  const PERMISSION_FLAG_PUBLIC_ONLY = 'PUBLIC_ONLY';
  const PERMISSION_FLAG_PRIVATE = 'PRIVATE';
  const PERMISSION_FLAG_PRIVATE_ONLY = 'PRIVATE_ONLY';

  static public function defaultPermissionFlag()
  {
    return self::PERMISSION_FLAG_PRIVATE;
  }
}
