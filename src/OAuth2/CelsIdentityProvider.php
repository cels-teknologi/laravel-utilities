<?php

namespace Cels\Utilities\OAuth2;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class CelsIdentityProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $endpoint = 'https://id.cels.co.id';

    public function __construct(array $options = [], array $collaborators = [])
    {
        if (\array_key_exists('endpoint', $options)) {
            $this->endpoint = $options['endpoint'];
            unset($options['endpoint']);
        }

        parent::__construct($options, $collaborators);
    }

    /**
     * Get authorization URL to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->endpoint . '/oauth/authorize';
    }

    /**
     * Get access token URL to retrieve token
     *
     * @param  array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->endpoint . '/oauth/token';
    }

    /**
     * Get provider URL to retrieve user details
     *
     * @param  AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->endpoint . '/api/userinfo';
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string Scope separator
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * @return string|null
     */
    protected function getPkceMethod()
    {
        return self::PKCE_METHOD_S256;
    }
    
    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['email', 'profile'];
    }

    /**
     * Check a provider response for errors.
     *
     * @param  ResponseInterface $response
     * @param  array $data Parsed response data
     * @return void
     *
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw CelsIdentityProviderException::clientException($response, $data);
        }
        else if (isset($data['error'])) {
            throw CelsIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return CelsResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new CelsResourceOwner($response);
    }
}