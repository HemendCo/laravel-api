<?php

namespace Hemend\Api\Libraries;

use Hemend\Api\Traits\ClaimsAccessTokenTrait;
use Laravel\Passport\Bridge\AccessToken as LaravelAccessToken;

class AccessToken extends LaravelAccessToken
{
    use ClaimsAccessTokenTrait;
}
