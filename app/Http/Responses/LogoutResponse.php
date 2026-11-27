<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Http\Responses\LogoutResponse as FortifyLogoutResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's logout response, plus clearing the encrypted browser history so the next visitor cannot go Back into
 * the signed-out account's pages. The flag is set after Fortify invalidated the session, so it survives into the
 * redirected page.
 */
final class LogoutResponse extends FortifyLogoutResponse
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        Inertia::clearHistory();

        return parent::toResponse($request);
    }
}
