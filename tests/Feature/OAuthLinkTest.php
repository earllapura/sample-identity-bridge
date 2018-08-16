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
     * Test if class gets application info
     */
    public function testGetsClientInfo()
    {
        $unsuccessfulResponse = new Response(200, ['Content-Type' => 'application/json'], '{"total":1,"data":[{"created_at":1534239617000,"client_id":"test_app","id":"a20b986a-c220-409a-b78a-d7ee4942e80c","redirect_uri":["http:\/\/mockbin.org\/"],"name":"Test Application","client_secret":"testapp123","consumer_id":"9dd18971-d706-4f78-bb92-62be43a61c86"}]}');
        $mock = new MockHandler([$unsuccessfulResponse]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler'=>$handler]);
        $apiLink = new OAuthLink($client);
        $expectedId = "a20b986a-c220-409a-b78a-d7ee4942e80c";
        $clientInfo = $apiLink->getClientInfo("test_app");
        $this->assertEquals($expectedId, $clientInfo->id);
    }
}
