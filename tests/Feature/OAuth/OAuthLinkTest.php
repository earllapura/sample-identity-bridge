<?php

namespace Tests\Feature\OAuth;

use App\KongClient\OAuthLink;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OAuthLinkTest extends TestCase
{
    /**
     * Creates a mock API client
     * @param  array  $mockResponses    Array of Guzzle Response objects
     * @param  array  $additionalParams Additional parameters to create Guzzle Client object
     * @return OAuthLink                The mock API client
     */
    private function createMockAPIClient(array $mockResponses, array $additionalParams = [])
    {
        $mock    = new MockHandler($mockResponses);
        $handler = HandlerStack::create($mock);
        $client  = new Client(array_merge(['handler' => $handler], $additionalParams));
        return new OAuthLink($client);
    }

    /**
     * Test if error thrown when no path for client info is configured
     */
    public function testErrorThrownWhenNoClientInfoPath()
    {
        $mockResponse = new Response(206, ['Content-Type' => 'application/json'], '
            {
                "total":1,
                "data":[
                    {
                        "created_at":1534239617000,
                        "client_id":"test_app",
                        "id":"12121212121",
                        "redirect_uri":["http:\/\/mockbin.org\/"],
                        "name":"Test Application",
                        "client_secret":"testapp123",
                        "consumer_id":"212121"
                    }
                ]
            }'
        );
        Config::set('api.client_path', null);
        $apiLink    = $this->createMockAPIClient([$mockResponse]);
        $clientInfo = $apiLink->getClientInfo("test_app");
        $this->assertEquals(500, $clientInfo->statusCode);
    }

    /**
     * Test if class gets application info. The HTTP status code is just to test
     * if status code is reflected.
     */
    public function testGetsClientInfo()
    {
        $mockResponse = new Response(206, ['Content-Type' => 'application/json'], '
            {
                "total":1,
                "data":[
                    {
                        "created_at":1534239617000,
                        "client_id":"test_app",
                        "id":"12121212121",
                        "redirect_uri":["http:\/\/mockbin.org\/"],
                        "name":"Test Application",
                        "client_secret":"testapp123",
                        "consumer_id":"212121"
                    }
                ]
            }'
        );
        $apiLink    = $this->createMockAPIClient([$mockResponse]);
        $expectedId = "12121212121";
        $clientInfo = $apiLink->getClientInfo("test_app");
        $this->assertEquals(206, $clientInfo->statusCode);
        $this->assertEquals($expectedId, $clientInfo->client->id);
    }

    /**
     * Tests if class gets scope information from the Kong API
     */
    public function testGetsScopeInfo()
    {
        $scopeName       = "testscope scopetest";
        $scopeDataString = '[
            {
                "name": "testscope",
                "description": "Lorem ipsum desu ka"
            }, {
                "name": "scopetest",
                "description": "Lorem ipsum desu"
            }
        ]';
        $mockResponse = new Response(200, ['Content-Type' => 'application/json'],
            '{
                "data": ' . $scopeDataString . '
            }'
        );
        $apiLink   = $this->createMockAPIClient([$mockResponse], ['http_errors' => false]);
        $scopeInfo = $apiLink->getScopeInfo($scopeName);
        $this->assertEquals(json_decode($scopeDataString), $scopeInfo->scopes);
        $this->assertEquals(200, $scopeInfo->statusCode);
    }

    /**
     * Test sending POST request for authorization
     */
    public function testSendsAuthorize()
    {
        $user = factory(User::class)->make();
        $this->be($user);
        $redirect_uri = "http://mockbin.org/";
        $mockResponse = new Response(200, ['Content-Type' => 'application/json'],
            '{
                "redirect_uri": "'.$redirect_uri.'"
            }'
        );
        $apiLink           = $this->createMockAPIClient([$mockResponse], ['http_errors' => false]);
        $authorizeResponse = $apiLink->authorize('test_app', 'code', 'email');
        $this->assertEquals(200, $authorizeResponse->statusCode);
        $this->assertEquals($redirect_uri, $authorizeResponse->data->redirect_uri);
    }

    public function testThrowErrorInNoProvisionKey()
    {
        $user = factory(User::class)->make();
        $this->be($user);
        $mockResponse = new Response(200, ['Content-Type' => 'application/json'],
            '{
                "redirect_uri": "http://localhost"
            }'
        );
        $apiLink   = $this->createMockAPIClient([$mockResponse], ['http_errors' => false]);
        Config::set('api.provision_key', null);
        $authorizeResponse = $apiLink->authorize('test_app', 'code', 'email');
        $this->assertEquals(500, $authorizeResponse->statusCode);
    }

    /**
     * Throws error when no configuration is set for API path
     */
    public function testThrowErrorWhenNoApiPath()
    {
        $user = factory(User::class)->make();
        $this->be($user);
        $mockResponse = new Response(200, ['Content-Type' => 'application/json'],
            '{
                "redirect_uri": "http://localhost"
            }'
        );
        $apiLink   = $this->createMockAPIClient([$mockResponse], ['http_errors' => false]);
        Config::set('api.path', null);
        $authorizeResponse = $apiLink->authorize('test_app', 'code', 'email');
        $this->assertEquals(500, $authorizeResponse->statusCode);
    }
}
