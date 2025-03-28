<?php

namespace Tests\Unit;

use Tests\TestCase;

class ServerStatusTest extends TestCase
{
    /** @test */
    public function it_returns_server_status()
    {
        $response = $this->getJson('/api/server-status');

        $response->assertStatus(200)->assertJson([
            'data' => [
                'status' => 'healthy'
            ]
        ]);
    }
}

