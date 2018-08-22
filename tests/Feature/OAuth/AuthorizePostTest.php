<?php

namespace Tests\Feature\OAuth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
