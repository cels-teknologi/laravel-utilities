<?php

namespace Cels\Utilities\Tests\OAuth2;

use GuzzleHttp\Psr7\Stream;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Tool\QueryBuilderTrait;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CelsIdentityProviderTest extends TestCase
{
    use QueryBuilderTrait;

    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new \Cels\Utilities\OAuth2\CelsIdentityProvider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }


    public function testScopes(): void
    {
        $scopeSeparator = ' ';
        $options = ['scope' => [\uniqid(), \uniqid()]];
        $query = ['scope' => implode($scopeSeparator, $options['scope'])];
        $url = $this->provider->getAuthorizationUrl($options);
        $encodedScope = $this->buildQueryString($query);

        $this->assertStringContainsString($encodedScope, $url);
    }

    public function testGetAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals('/oauth/authorize', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl(): void
    {
        $params = [];

        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);

        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testGetAccessToken(): void
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $this->mockResponseBody($response, \json_encode([
            'access_token' => 'mock_access_token',
            'token_type' => 'bearer',
        ]));
        $response->shouldReceive('getHeader')
                 ->andReturn(['content-type' => 'application/json']);
        $response->shouldReceive('getStatusCode')
                 ->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNull($token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testUserData(): void
    {
        $name = \uniqid();
        $picture = \uniqid();
        $email = \uniqid();

        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $this->mockResponseBody($postResponse, \json_encode([
            'token_type' => 'Bearer',
            'access_token' => 'mock_access_token',
            'expires' => 3600,
            'refresh_token' => 'mock_refresh_token',
        ]));
        $postResponse->shouldReceive('getHeader')
                     ->andReturn(['content-type' => 'application/json']);
        $postResponse->shouldReceive('getStatusCode')
                     ->andReturn(200);

        $userResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $this->mockResponseBody($userResponse, \json_encode([
            'name' => $name,
            'email' => $email,
            'profile_photo_url' => $picture,
            'is_email_verified' => true,
            'has_two_factor' => false,
        ]));
        $userResponse->shouldReceive('getHeader')
                     ->andReturn(['content-type' => 'application/json']);
        $userResponse->shouldReceive('getStatusCode')
                     ->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(2)
            ->andReturn($postResponse, $userResponse);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = $this->provider->getResourceOwner($token);

        $this->assertEquals($name, $user->getName());
        $this->assertEquals($name, $user->toArray()['name']);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($email, $user->toArray()['email']);
        $this->assertEquals($picture, $user->getPhotoUrl());
        $this->assertEquals($picture, $user->toArray()['profile_photo_url']);
    }

    public function testExceptionThrownWhenErrorObjectReceived(): void
    {
        $status = rand(400, 600);
        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $this->mockResponseBody($postResponse, json_encode([
            'message' => 'Validation Failed',
            'errors' => [
                ['resource' => 'Issue', 'field' => 'title', 'code' => 'missing_field'],
            ],
        ]));
        $postResponse->shouldReceive('getHeader')
                     ->andReturn(['content-type' => 'application/json']);
        $postResponse->shouldReceive('getStatusCode')
                     ->andReturn($status);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(1)
            ->andReturn($postResponse);
        $this->provider->setHttpClient($client);

        $this->expectException(IdentityProviderException::class);

        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }

    public function testExceptionThrownWhenOAuthErrorReceived(): void
    {
        $status = 200;
        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $this->mockResponseBody($postResponse, json_encode([
            'error' => 'invalid_request',
        ]));
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(1)
            ->andReturn($postResponse);
        $this->provider->setHttpClient($client);

        $this->expectException(IdentityProviderException::class);

        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }

    protected function mockResponseBody(&$response, $text)
    {
        $response->shouldReceive('getBody')
                 ->andReturn(new Stream(fopen('data://text/plain,' . $text, 'r')));
    }
}