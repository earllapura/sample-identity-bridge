<?php

namespace Tests\Feature;

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
}
