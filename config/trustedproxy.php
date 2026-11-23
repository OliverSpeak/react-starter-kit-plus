<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted proxies
    |--------------------------------------------------------------------------
    |
    | Proxies whose X-Forwarded-* headers are believed, so request IPs (and
    | per-IP rate limits) reflect the real client. Comma-separated IPs or
    | CIDR ranges, or * to trust the immediate caller. Leave unset to trust
    | none, except on Laravel Cloud and Forge or Vapor default domains,
    | where the framework trusts the platform proxy itself.
    |
    */

    'proxies' => env('TRUSTED_PROXIES') ?: null,

];
