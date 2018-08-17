<?php

namespace Tests\Feature;

use App\KongClient\OAuthLink;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OAuthLinkTest extends TestCase
{
    /**
     * Test if class gets application info. The HTTP status code is just to test
     * if status code is reflected.
     */
    public function testGetsClientInfo()
    {
        $unsuccessfulResponse = new Response(206, ['Content-Type' => 'application/json'], '{"total":1,"data":[{"created_at":1534239617000,"client_id":"test_app","id":"12121212121","redirect_uri":["http:\/\/mockbin.org\/"],"name":"Test Application","client_secret":"testapp123","consumer_id":"212121"}]}');
        $mock = new MockHandler([$unsuccessfulResponse]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler'=>$handler]);
        $apiLink = new OAuthLink($client);
        $expectedId = "12121212121";
        $clientInfo = $apiLink->getClientInfo("test_app");
        $this->assertEquals($expectedId, $clientInfo->client->id);
        $this->assertEquals(206, $clientInfo->statusCode);
    }
}
