<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Policy;
use Spatie\Csp\Value;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy extends Policy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function configure(): void
    {
        //        $this->addDirective(Directive::UPGRADE_INSECURE_REQUESTS, Value::NO_VALUE)
        //            ->addDirective(Directive::BLOCK_ALL_MIXED_CONTENT, Value::NO_VALUE);
    }

    protected function addDirectivesForGoogleFonts(): self
    {
        return $this
            ->addDirective(Directive::FONT, 'fonts.gstatic.com')
            ->addDirective(Directive::SCRIPT, 'fonts.googleapis.com')
            ->addDirective(Directive::STYLE, 'fonts.googleapis.com');
    }
}
