<?php

namespace Hemend\Api\Traits;

use Lcobucci\JWT\Token;
use Hemend\Api\Implements\AccessTokenClaimEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

trait ClaimsAccessTokenTrait
{
    use AccessTokenTrait;

    /**
     * @var AccessTokenClaimEntityInterface[]
     */
    protected $claims = [];

    /**
     * Associate a claim with the token.
     *
     * @param AccessTokenClaimEntityInterface $claim
     */
    public function addClaim(AccessTokenClaimEntityInterface $claim)
    {
        $this->claims[$claim->getIdentifier()] = $claim;
    }

    /**
     * Associate a claim with the token.
     *
     * @return AccessTokenClaimEntityInterface[]
     */
    public function getClaims()
    {
      return $this->claims;
    }

    /**
     * Generate a JWT from the access token
     *
     * @return Token
     */
    private function convertToJWT()
    {
        $this->initJwtConfiguration();

        return $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new \DateTimeImmutable())
            ->canOnlyBeUsedAfter(new \DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string)$this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->withClaim('mainData', $this->getClaims())
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    /**
     * Generate a string representation from the access token
     */
    public function __toString()
    {
        return $this->convertToJWT()->toString();
    }
}
