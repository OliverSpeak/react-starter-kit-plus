<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\SecuritySettingsChanged;
/* @chisel-2fa */
use Laravel\Fortify\Events\RecoveryCodesGenerated;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
/* @end-chisel-2fa */
/* @chisel-passkeys */
use Laravel\Passkeys\Events\PasskeyDeleted;
use Laravel\Passkeys\Events\PasskeyRegistered;

/* @end-chisel-passkeys */

/**
 * Emails the account whenever a passkey or two-factor setting changes.
 *
 * Two-factor counts as enabled when it is confirmed (the two-factor "confirm" option is on), not when the
 * unconfirmed secret is first generated.
 */
class SendSecuritySettingsChangedNotification
{
    /* @chisel-passkeys */
    public function handlePasskeyRegistered(PasskeyRegistered $event): void
    {
        $this->notify($event->user, __('A passkey named ":name" was added to your account.', ['name' => $event->passkey->name]));
    }

    public function handlePasskeyDeleted(PasskeyDeleted $event): void
    {
        $this->notify($event->user, __('The passkey named ":name" was removed from your account.', ['name' => $event->passkey->name]));
    }
    /* @end-chisel-passkeys */

    /* @chisel-2fa */
    public function handleTwoFactorAuthenticationConfirmed(TwoFactorAuthenticationConfirmed $event): void
    {
        $this->notify($event->user, __('Two-factor authentication was turned on for your account.'));
    }

    public function handleTwoFactorAuthenticationDisabled(TwoFactorAuthenticationDisabled $event): void
    {
        $this->notify($event->user, __('Two-factor authentication was turned off for your account.'));
    }

    public function handleRecoveryCodesGenerated(RecoveryCodesGenerated $event): void
    {
        $this->notify($event->user, __('New two-factor recovery codes were generated for your account. The previous codes no longer work.'));
    }
    /* @end-chisel-2fa */

    private function notify(mixed $user, string $description): void
    {
        if (! $user instanceof User) {
            return;
        }

        $user->notify(new SecuritySettingsChanged($description));
    }
}
