<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnsureAllowedEmailDomain
{
    /**
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domains = config('catiq.allowed_email_domains', []);

        if ($domains === []) {
            return $next($request);
        }

        $email = strtolower((string) $request->input('email', ''));
        $domain = Str::after($email, '@');

        if ($domain === '' || ! in_array($domain, $domains, true)) {
            throw ValidationException::withMessages([
                'email' => ['The email domain is not allowed.'],
            ]);
        }

        return $next($request);
    }
}
