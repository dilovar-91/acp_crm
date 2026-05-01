<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_response_time_of_posts_endpoint()
    {
        $start = microtime(true);

        $this->get('/api/test-orders');

        $end = microtime(true);

        $responseTime = $end - $start;

        $this->assertLessThan(100, $responseTime); // You can adjust the threshold as needed.
    }
}
