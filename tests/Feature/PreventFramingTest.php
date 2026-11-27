<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PreventFramingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_pages_refuse_cross_origin_framing(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Content-Security-Policy', "frame-ancestors 'self'");
    }

    public function test_authenticated_pages_refuse_cross_origin_framing(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('profile.edit'));

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Content-Security-Policy', "frame-ancestors 'self'");
    }
}
