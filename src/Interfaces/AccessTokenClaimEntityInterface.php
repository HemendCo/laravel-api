<?php

namespace Hemend\Api\Interfaces;

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
