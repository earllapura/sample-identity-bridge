<?php

namespace Tests\Feature;

use App\KongClient\Contracts\OAuthLinkInterface;
use App\KongClient\OAuthLink;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AuthorizeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if endpoint is protected by basic authentication.
     */
    public function testProtectedByAuth()
    {
        $response = $this->get('/authorize');
        $response->assertRedirect('/login');
    }

    /**
     * Test if error 400 is thrown when client_id is not supplied.
     */
    public function testErrorOnUnfilledClient()
    {
        $user     = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/authorize');
        $response->assertStatus(400);
    }

    /**
     * Test if other HTTP error are reflected
     */
    public function testHttpErrorReflection()
    {
        $statusCode = 422;
        $client     = Mockery::mock(OAuthLink::class);
        $client->shouldReceive('getClientInfo')->with('test_app')->andReturn((object) ['client' => null, 'statusCode' => $statusCode]);
        $client->shouldReceive('getScopeInfo')->with('email')->andReturn((object) ['scope' => null, 'statusCode' => $statusCode]);
        $this->app->instance(OAuthLinkInterface::class, $client);
        $user     = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/authorize?client_id=test_app&response_type=code&scopes=email');
        $response->assertStatus($statusCode);
    }

    /**
     * Test if views has been sent with correct parameters
     */
    public function testViewHasCorrectParams()
    {
        $client     = Mockery::mock(OAuthLink::class);
        $client->shouldReceive('getClientInfo')->with('test_app')->andReturn((object) ['client' => json_decode('"client_id":"test_app","id":"12121212121","redirect_uri":["http:\/\/mockbin.org\/"],"name":"Test Application","client_secret":"testapp123","consumer_id":"212121"}'), 'statusCode' => 200]);
        $client->shouldReceive('getScopeInfo')->with('email')->andReturn((object) ['scope' => json_decode('{"name":"email", "description":"Grants permission to read your email address"}'), 'statusCode' => 200]);
        $this->app->instance(OAuthLinkInterface::class, $client);
        $user     = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/authorize?client_id=test_app&response_type=code&scopes=email');
        $response->assertViewHas(['application_name'=>'Test Application', 'scopes'=>['email'=>'Grants permission to read your email address']]);
    }
}
