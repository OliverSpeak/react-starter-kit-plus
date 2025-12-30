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

        session()->put('locale', $validated['locale']);

        return redirect()->back();
    }
}
