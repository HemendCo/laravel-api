<?php

namespace Hemend\Api\Implements;

use JsonSerializable;

interface AccessTokenClaimEntityInterface extends JsonSerializable
{
    /**
     * Get the scope's identifier.
     *
     * @return string
     */
    public function getIdentifier();
}
