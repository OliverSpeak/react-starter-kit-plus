<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleController extends Controller
{
    /**
     * Update the user's preferred locale.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', array_keys(config('locale.supported', [])))],
        ]);

        // Save to user record if authenticated
        $user = $request->user();
        if ($user) {
            $user->update(['locale' => $validated['locale']]);
        }

        // Save to cookie to persist across logout/login
        return redirect()->back()->cookie('locale', $validated['locale'], 60 * 24 * 365); // 1 year
    }
}
