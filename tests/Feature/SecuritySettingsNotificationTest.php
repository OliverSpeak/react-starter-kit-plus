<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SecuritySettingsChanged;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Events\RecoveryCodesGenerated;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
use Laravel\Passkeys\Events\PasskeyDeleted;
use Laravel\Passkeys\Events\PasskeyRegistered;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class SecuritySettingsNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: Closure(User): object, 1: string}>
     */
    public static function securityEvents(): array
    {
        return [
            'passkey added' => [
                fn (User $user): object => new PasskeyRegistered($user, $user->passkeys()->create(['name' => 'Laptop', 'credential_id' => 'cred-1', 'credential' => ['id' => 'cred-1']])),
                'A passkey named "Laptop" was added to your account.',
            ],
            'passkey removed' => [
                fn (User $user): object => new PasskeyDeleted($user, $user->passkeys()->create(['name' => 'Laptop', 'credential_id' => 'cred-1', 'credential' => ['id' => 'cred-1']])),
                'The passkey named "Laptop" was removed from your account.',
            ],
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
        ];
    }

    /**
     * @param  Closure(User): object  $makeEvent
     */
    #[DataProvider('securityEvents')]
    public function test_each_sign_in_method_change_emails_the_account(Closure $makeEvent, string $description): void
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

    public function test_disabling_two_factor_emails_the_account(): void
    {
        Notification::fake();
        $user = User::factory()->withTwoFactor()->create();

        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->delete(route('two-factor.disable'))
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, SecuritySettingsChanged::class);
    }

    public function test_the_mail_escapes_user_supplied_text(): void
    {
        $user = User::factory()->create();

        $mail = new SecuritySettingsChanged('A passkey named "<script>alert(1)</script>" was added to your account.')->toMail($user);
        $html = (string) $mail->render();

        $this->assertSame('Your account security settings changed', $mail->subject);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertSame(route('security.edit'), $mail->actionUrl);
    }
}
