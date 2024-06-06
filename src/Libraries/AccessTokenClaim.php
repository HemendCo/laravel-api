<?php

namespace Hemend\Api\Libraries;

use Hemend\Api\Implements\AccessTokenClaimEntityInterface;

class AccessTokenClaim implements AccessTokenClaimEntityInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $data;

    /**
     * Create a new scope instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct(string $name, $data)
    {
        $this->identifier = $name;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
      return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
      return $this->data;
    }

    /**
     * Get the data that should be serialized to JSON.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getData();
    }
}
