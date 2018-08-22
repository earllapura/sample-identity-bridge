<?php

namespace Tests\Feature;

use App\KongClient\OAuthLink;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class OAuthLinkTest extends TestCase
{
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
        $this->assertEquals($expectedId, $clientInfo->client->id);
        $this->assertEquals(206, $clientInfo->statusCode);
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
        $apiLink    = $this->createMockAPIClient([$mockResponse], ['http_errors' => false]);
        $scopeInfo = $apiLink->getScopeInfo($scopeName);
        $this->assertEquals(json_decode($scopeDataString), $scopeInfo->scopes);
        $this->assertEquals(200, $scopeInfo->statusCode);
    }

    /**
     * Creates a mock API client
     * @param  array  $mockResponses    Array of Guzzle Response objects
     * @param  array  $additionalParams Additional parameters to create Guzzle Client object
     * @return OAuthLink                The mock API client
     */
    private function createMockAPIClient(array $mockResponses, array $additionalParams = [])
    {
        $mock      = new MockHandler($mockResponses);
        $handler   = HandlerStack::create($mock);
        $client    = new Client(array_merge(['handler' => $handler], $additionalParams));
        return new OAuthLink($client);
    }
}
