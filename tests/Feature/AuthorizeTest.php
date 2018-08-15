<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/authorize');
        $response->assertStatus(400);
    }
}
