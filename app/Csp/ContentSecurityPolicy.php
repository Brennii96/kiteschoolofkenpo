<?php

declare(strict_types=1);

namespace App\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;
use Spatie\Csp\Presets\Basic;
use Spatie\Csp\Presets\GoogleFonts;
use Spatie\Csp\Presets\GoogleMaps;
use Spatie\Csp\Presets\Stripe;
use Spatie\Csp\Value;

final class ContentSecurityPolicy implements Preset
{
    public function configure(Policy $policy): void
    {
        $this->applyPackagePresets($policy);

        $policy
            ->add(Directive::FRAME_ANCESTORS, Keyword::SELF)
            ->add(Directive::SCRIPT, [Keyword::UNSAFE_INLINE, Keyword::UNSAFE_EVAL])
            ->add(Directive::STYLE, Keyword::UNSAFE_INLINE)
            ->add(Directive::FONT, 'data:')
            ->add(Directive::WORKER, 'blob:')
            ->add(Directive::MANIFEST, Keyword::SELF)
            ->add(Directive::CONNECT, [
                'https://api.stripe.com',
                'https://r.stripe.com',
                'https://m.stripe.network',
            ]);

        $this->addConfiguredSources($policy, Directive::SCRIPT, 'vite_http');
        $this->addConfiguredSources($policy, Directive::STYLE, 'vite_http');
        $this->addConfiguredSources($policy, Directive::IMG, 'asset');
        $this->addConfiguredSources($policy, Directive::MEDIA, 'asset');
        $this->addConfiguredSources($policy, Directive::FRAME, 'bunny');
        $this->addConfiguredSources($policy, Directive::CONNECT, 'connect');

        if (config('csp.upgrade_insecure_requests')) {
            $policy->add(Directive::UPGRADE_INSECURE_REQUESTS, Value::NO_VALUE);
        }
    }

    private function applyPackagePresets(Policy $policy): void
    {
        /** @var array<class-string<Preset>> $presets */
        $presets = [Basic::class, GoogleFonts::class, GoogleMaps::class, Stripe::class];

        foreach ($presets as $preset) {
            new $preset()->configure($policy);
        }
    }

    private function addConfiguredSources(Policy $policy, Directive $directive, string $sourceGroup): void
    {
        $sources = config("csp.sources.{$sourceGroup}", []);

        if (is_array($sources) && $sources !== []) {
            $policy->add($directive, $sources);
        }
    }
}
