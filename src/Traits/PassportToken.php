<?php

namespace Hemend\Api\Traits;

use DateTimeImmutable;
use Hemend\Api\Libraries\AccessToken;
use Hemend\Api\Libraries\AccessTokenClaim;
use Laravel\Passport\Bridge\Scope;
use Illuminate\Foundation\Auth\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Events\Dispatcher;
//use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\Client;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\ScopeRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;


/**
 * Trait PassportToken
 *
 * @link: https://gist.github.com/messi89/9c988f2790e694963085db0fbfda3c31
 *
 * @package App\Traits
 */
trait PassportToken
{
    use CryptTrait;

    /**
     * Generate a new unique identifier.
     *
     * @param int $length
     *
     * @throws OAuthServerException
     *
     * @return string
     */
    private function generateUniqueIdentifier($length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\TypeError $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred');
        } catch (\Error $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred');
        } catch (\Exception $e) {
            // If you get this message, the CSPRNG failed hard.
            throw OAuthServerException::serverError('Could not generate a random string');
        }
    }

    private function issueRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $maxGenerationAttempts = 10;
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        $refreshToken = $refreshTokenRepository->getNewRefreshToken();
        $refreshToken->setExpiryDateTime((new DateTimeImmutable())->add(Passport::refreshTokensExpireIn()));
        $refreshToken->setAccessToken($accessToken);

        while ($maxGenerationAttempts-- > 0) {
            $refreshToken->setIdentifier($this->generateUniqueIdentifier());
            try {
                $refreshTokenRepository->persistNewRefreshToken($refreshToken);

                return $refreshToken;
            } catch (UniqueTokenIdentifierConstraintViolationException $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }
    }

    private function createPassportTokenByUser($userId, $clientId, $tokenScopes = [], $tokenClaims = [])
    {
        $scopes = [];
        if (is_array($tokenScopes)) {
            foreach ($tokenScopes as $scope) {
                $scopes[] = new Scope($scope);
            }
        }

        $accessToken = new AccessToken($userId, $scopes, new Client(null, null, null));

        if (is_array($tokenClaims)) {
          foreach ($tokenClaims as $name => $data) {
            $accessToken->addClaim(new AccessTokenClaim($name, $data));
          }
        }

        $accessToken->setIdentifier($this->generateUniqueIdentifier());
        $accessToken->setClient(new Client($clientId, null, null));
        $accessToken->setExpiryDateTime((new DateTimeImmutable())->add(Passport::tokensExpireIn()));

        $accessTokenRepository = new AccessTokenRepository(new TokenRepository(), new Dispatcher());
        $accessTokenRepository->persistNewAccessToken($accessToken);
        $refreshToken = $this->issueRefreshToken($accessToken);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    private function sendBearerTokenResponse($accessToken, $refreshToken)
    {
        //set private key
        $privateKey = new CryptKey('file://'.Passport::keyPath('oauth-private.key'), null, false);
        $accessToken->setPrivateKey($privateKey);

        $response = new BearerTokenResponse();
        $response->setAccessToken($accessToken);
        $response->setRefreshToken($refreshToken);

        //not used on laravel 6.x
        //$response->setPrivateKey($privateKey);

        $response->setEncryptionKey(app('encrypter')->getKey());

        return $response->generateHttpResponse(new Response);
    }

    /**
     * @param string $encryptedRefreshToken
     * @param string $clientId
     *
     * @throws OAuthServerException
     *
     * @return array
     */
    private function validateOldRefreshToken($encryptedRefreshToken, $clientId)
    {
        $this->setEncryptionKey(app('encrypter')->getKey());

        if (!\is_string($encryptedRefreshToken)) {
            throw OAuthServerException::invalidRequest('refresh_token');
        }

        try {
            $refreshToken = $this->decrypt($encryptedRefreshToken);
        } catch (\Exception $e) {
            throw OAuthServerException::invalidRefreshToken('Cannot decrypt the refresh token', $e);
        }

        $refreshTokenData = \json_decode($refreshToken, true);
        if ($refreshTokenData['client_id'] != $clientId) {
            throw OAuthServerException::invalidRefreshToken('Token is not linked to client');
        }

        if ($refreshTokenData['expire_time'] < \time()) {
            throw OAuthServerException::invalidRefreshToken('Token has expired');
        }

        $refreshTokenRepository = app(RefreshTokenRepository::class);
        if ($refreshTokenRepository->isRefreshTokenRevoked($refreshTokenData['refresh_token_id']) === true) {
            throw OAuthServerException::invalidRefreshToken('Token has been revoked');
        }

        return $refreshTokenData;
    }

    /**
     * Converts a scopes query string to an array to easily iterate for validation.
     *
     * @param string $scopes
     *
     * @return array
     */
    private function convertScopesQueryStringToArray(string $scopes)
    {
        return \array_filter(\explode(' ', \trim($scopes)), function ($scope) {
            return $scope !== '';
        });
    }

    /**
     * Validate scopes in the request.
     *
     * @param string|array $scopes
     * @param string       $redirectUri
     *
     * @throws OAuthServerException
     *
     * @return ScopeEntityInterface[]
     */
    private function validateScopes($scopes, $redirectUri = null)
    {
        if ($scopes === null) {
            $scopes = [];
        } elseif (\is_string($scopes)) {
            $scopes = $this->convertScopesQueryStringToArray($scopes);
        }

        if (!\is_array($scopes)) {
            throw OAuthServerException::invalidRequest('scope');
        }

        $validScopes = [];

        $scopeRepository = new ScopeRepository();
        foreach ($scopes as $scopeItem) {
            $scope = $scopeRepository->getScopeEntityByIdentifier($scopeItem);

            if ($scope instanceof ScopeEntityInterface === false) {
                throw OAuthServerException::invalidScope($scopeItem, $redirectUri);
            }

            $validScopes[] = $scope;
        }

        return $validScopes;
    }

    /**
     * @param User $user
     * @param array $tokenScopes
     * @param array $tokenClaims Payload
     * @param numeric $clientId default = 1
     * @param bool $output default = false
     * @return array|\Illuminate\Support\Collection|BearerTokenResponse
     */
    protected function getBearerTokenByUser(User $user, $tokenScopes = [], $tokenClaims = [], $clientId = 1, $output = false)
    {
        //you can simply use this method (available only on laravel 6.x)
        //return collect($user->createToken(''))->forget('token');

        $passportToken = $this->createPassportTokenByUser($user->id, $clientId, $tokenScopes, $tokenClaims);
        $bearerToken = $this->sendBearerTokenResponse($passportToken['access_token'], $passportToken['refresh_token']);

        if (! $output) {
            $bearerToken = json_decode($bearerToken->getBody()->__toString(), true);
        }

      return [
        'passport_token' => $passportToken,
        'token' => $bearerToken
      ];
    }

    /**
     * @param string $refreshToken
     * @param numeric $clientId default = 1
     * @param bool $output default = false
     * @return array|\Illuminate\Support\Collection|BearerTokenResponse
     */
    protected function regenerateBearerTokenByRefreshToken($refreshToken, $clientId = 1, $output = false)
    {
        $oldRefreshToken = $this->validateOldRefreshToken($refreshToken, $clientId);

        $scopes = $this->validateScopes($oldRefreshToken['scopes']);
        $api_service_scope_exists = false;
        $scopesArray = [];
        foreach ($scopes as $scope) {
            if (\in_array($scope->getIdentifier(), $oldRefreshToken['scopes'], true) === false) {
                throw OAuthServerException::invalidScope($scope->getIdentifier());
            }

            $scopesArray[] = $scope->getIdentifier();

            if(parent::SERVICE === $scope->getIdentifier()) {
                $api_service_scope_exists = true;
            }
        }

        if(!$api_service_scope_exists) {
            throw OAuthServerException::invalidScope(parent::SERVICE);
        }

        $claimsArray = $oldRefreshToken['mainData'] ?? [];

        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $tokenRepository->revokeAccessToken($oldRefreshToken['access_token_id']);

        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
        $refreshTokenRepository->revokeRefreshToken($oldRefreshToken['refresh_token_id']);

        $passportToken = $this->createPassportTokenByUser($oldRefreshToken['user_id'], $clientId, $scopesArray, $claimsArray);
        $bearerToken = $this->sendBearerTokenResponse($passportToken['access_token'], $passportToken['refresh_token']);

        if (! $output) {
            $bearerToken = json_decode($bearerToken->getBody()->__toString(), true);
        }

        return [
            'passport_token' => $passportToken,
            'token' => $bearerToken
        ];
    }
}
