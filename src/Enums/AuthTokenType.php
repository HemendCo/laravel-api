<?php

namespace Hemend\Api\Enums;

use Hemend\Api\Traits\EnumToArray;

enum AuthTokenType: string
{
  use EnumToArray;

  case AccessToken = 'AccessToken';
  case RefreshToken = 'RefreshToken';
}
