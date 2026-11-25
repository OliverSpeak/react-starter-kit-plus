<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SecuritySettingsChanged;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
/* @chisel-2fa */
use Laravel\Fortify\Events\RecoveryCodesGenerated;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
/* @end-chisel-2fa */
/* @chisel-passkeys */
use Laravel\Passkeys\Events\PasskeyDeleted;
use Laravel\Passkeys\Events\PasskeyRegistered;
/* @end-chisel-passkeys */
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SecuritySettingsNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: Closure(User): object, 1: string}>
     */
    public static function securityEvents(): array
    {
        return [
            /* @chisel-passkeys */
            'passkey added' => [
                fn (User $user): object => new PasskeyRegistered($user, $user->passkeys()->create(['name' => 'Laptop', 'credential_id' => 'cred-1', 'credential' => ['id' => 'cred-1']])),
                'A passkey named "Laptop" was added to your account.',
            ],
            'passkey removed' => [
                fn (User $user): object => new PasskeyDeleted($user, $user->passkeys()->create(['name' => 'Laptop', 'credential_id' => 'cred-1', 'credential' => ['id' => 'cred-1']])),
                'The passkey named "Laptop" was removed from your account.',
            ],
            /* @end-chisel-passkeys */
            /* @chisel-2fa */
            'two-factor confirmed' => [
                fn (User $user): object => new TwoFactorAuthenticationConfirmed($user),
                'Two-factor authentication was turned on for your account.',
            ],
            'two-factor disabled' => [
                fn (User $user): object => new TwoFactorAuthenticationDisabled($user),
                'Two-factor authentication was turned off for your account.',
            ],
            'recovery codes regenerated' => [
                fn (User $user): object => new RecoveryCodesGenerated($user),
                'New two-factor recovery codes were generated for your account. The previous codes no longer work.',
            ],
            /* @end-chisel-2fa */
        ];
    }

    /**
     * @param  Closure(User): object  $makeEvent
     */
    #[DataProvider('securityEvents')]
    public function test_each_sign_in_method_change_emails_the_account(Closure $makeEvent, string $description)
    {
        Notification::fake();
        $user = User::factory()->create();

        event($makeEvent($user));

        Notification::assertSentTo(
            $user,
            SecuritySettingsChanged::class,
            fn (SecuritySettingsChanged $notification): bool => $notification->description === $description,
        );
    }

    /* @chisel-2fa */
    public function test_disabling_two_factor_emails_the_account()
    {
        Notification::fake();
        $user = User::factory()->withTwoFactor()->create();

        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->delete(route('two-factor.disable'))
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, SecuritySettingsChanged::class);
    }
    /* @end-chisel-2fa */

    public function test_the_mail_escapes_user_supplied_text()
    {
        $user = User::factory()->create();

        $mail = (new SecuritySettingsChanged('A passkey named "<script>alert(1)</script>" was added to your account.'))->toMail($user);
        $html = (string) $mail->render();

        $this->assertSame('Your account security settings changed', $mail->subject);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertSame(route('security.edit'), $mail->actionUrl);
    }
}
