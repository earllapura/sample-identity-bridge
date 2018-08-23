<?php

namespace Tests\Feature\OAuth;

use App\KongClient\Contracts\OAuthLinkInterface;
use App\KongClient\OAuthLink;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AuthorizePostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if endpoint is protected by basic authentication.
     */
    public function testProtectedByAuth()
    {
        $response = $this->post('/authorize');
        $response->assertRedirect('/login');
    }

    /**
     * Test if error 400 is thrown when client_id, response_type or scope is not supplied.
     */
    public function testErrorOnMissingParameters()
    {
        $user     = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/authorize', ['client_id' => '5214215', 'response_type' => 'code']);
        $response->assertStatus(400);
        $response = $this->actingAs($user)->post('/authorize', ['client_id' => '5214215', 'scope' => 'email']);
        $response->assertStatus(400);
        $response = $this->actingAs($user)->post('/authorize', ['response_type' => 'code', 'scope' => 'email']);
        $response->assertStatus(400);
    }

    /**
     * Test redirection on successful authorization
     */
    public function testSuccessfulAuthorization()
    {
        $client = Mockery::mock(OAuthLink::class);
        $client->shouldReceive('authorize')->with('test_app', 'code', 'email', [])->andReturn((object) [
            'data'       => json_decode('
                {
                    "code":"12121212121",
                    "redirect_uri": "http://mockbin.org/"
                }'),
            'statusCode' => 200,
        ]);
        $this->app->instance(OAuthLinkInterface::class, $client);
        $user     = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/authorize', ['client_id' => 'test_app', 'response_type' => 'code', 'scope' => 'email']);
        $response->assertRedirect('http://mockbin.org/');
    }
}
