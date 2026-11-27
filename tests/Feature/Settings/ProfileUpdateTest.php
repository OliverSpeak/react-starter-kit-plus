<?php

declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Notifications\EmailAddressChanged;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: array<string, string>}>
     */
    public static function missingOrWrongCurrentPassword(): array
    {
        return [
            'missing' => [[]],
            'wrong' => [['current_password' => 'wrong-password']],
        ];
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('profile.edit'));

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'current_password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_is_stored_in_lowercase_so_the_user_can_still_log_in(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => 'Test@Example.com',
                'current_password' => 'password',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('test@example.com', $user->refresh()->email);

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'email' => 'Test@Example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_email_must_be_unique_regardless_of_case(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => 'Taken@Example.com',
            ]);

        $response
            ->assertSessionHasErrors('email')
            ->assertRedirect(route('profile.edit'));
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->refresh()->email_verified_at);
        Notification::assertNothingSent();
    }

    /**
     * @param  array<string, string>  $passwordInput
     */
    #[DataProvider('missingOrWrongCurrentPassword')]
    public function test_email_change_is_rejected_without_the_current_password(array $passwordInput): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'owner@example.com']);

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => 'attacker@example.com',
                ...$passwordInput,
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertSame('owner@example.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
        Notification::assertNothingSent();
    }

    public function test_email_change_notifies_the_previous_address(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'owner@example.com']);

        $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => 'new-owner@example.com',
                'current_password' => 'password',
            ])
            ->assertSessionHasNoErrors();

        Notification::assertSentOnDemand(
            EmailAddressChanged::class,
            fn (EmailAddressChanged $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'owner@example.com',
        );
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->delete(route('profile.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->fresh());
    }

    public function test_the_page_after_deleting_an_account_clears_browser_history(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->delete(route('profile.destroy'), ['password' => 'password']);

        $this->assertGuest();
        $response->assertInertia(function (Assert $page): Assert {
            $this->assertTrue($page->toArray()['clearHistory'] ?? false);

            return $page;
        });
    }
}
