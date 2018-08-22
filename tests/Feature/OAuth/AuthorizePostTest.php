<?php

namespace Tests\Feature\OAuth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $response = $this->actingAs($user)->post('/authorize', ['client_id'=>'5214215', 'response_type'=>'code']);
        $response->assertStatus(400);
        $response = $this->actingAs($user)->post('/authorize', ['client_id'=>'5214215', 'scope'=>'email']);
        $response->assertStatus(400);
        $response = $this->actingAs($user)->post('/authorize', ['response_type'=>'code', 'scope'=>'email']);
        $response->assertStatus(400);
    }
}
