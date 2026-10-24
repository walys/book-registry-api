<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiJsonResponseTest extends TestCase
{
    public function test_unauthenticated_api_request_returns_json(): void
    {
        $response = $this->get('/api/user');

        $response->assertUnauthorized();
        $response->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_unknown_api_route_returns_json(): void
    {
        $response = $this->get('/api/does-not-exist');

        $response->assertNotFound();
        $response->assertJsonStructure(['message']);
    }
}
