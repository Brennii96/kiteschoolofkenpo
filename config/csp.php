<?php

use App\Csp\ContentSecurityPolicy;
use App\Csp\CspSources;
use Spatie\Csp\Nonce\RandomString;

$environment = (string) env('APP_ENV', 'production');
$isProduction = $environment === 'production';
$allowsViteDevServer = (bool) env('CSP_ALLOW_VITE_DEV_SERVER', $environment === 'local');

$vitePort = (int) env('VITE_DEV_SERVER_PORT', 5173);
$viteHost = trim((string) env('VITE_DEV_SERVER_HOST', 'localhost'));
$viteHosts = CspSources::viteHosts($viteHost);
$viteHttpOrigins = CspSources::viteHttpOrigins($viteHosts, $vitePort);
$viteSocketOrigins = CspSources::viteSocketOrigins($viteHosts, $vitePort);

$r2Origins = CspSources::unique([
    CspSources::originFromUrl(env('CLOUDFLARE_R2_URL')),
    CspSources::originFromUrl(env('CLOUDFLARE_R2_ENDPOINT')),
    'https://*.r2.dev',
    'https://*.r2.cloudflarestorage.com',
]);

$bunnyHostname = trim((string) env('BUNNY_CDN_HOSTNAME', ''));
$bunnyOrigins = CspSources::unique([
    $bunnyHostname !== '' ? "https://{$bunnyHostname}" : null,
    'https://*.b-cdn.net',
    'https://iframe.mediadelivery.net',
]);

$assetOrigins = CspSources::unique([
    'blob:',
    ...$r2Origins,
    ...$bunnyOrigins,
]);

$connectOrigins = CspSources::unique([
    ...$r2Origins,
    ...$bunnyOrigins,
    ...($allowsViteDevServer ? $viteHttpOrigins : []),
    ...($allowsViteDevServer ? $viteSocketOrigins : []),
]);

$presets = [ContentSecurityPolicy::class];
$reportOnly = (bool) env('CSP_REPORT_ONLY', ! $isProduction);

return [
    'presets' => $reportOnly ? [] : $presets,
    'directives' => [],
    'report_only_presets' => $reportOnly ? $presets : [],
    'report_only_directives' => [],
    'report_uri' => env('CSP_REPORT_URI', ''),
    'enabled' => env('CSP_ENABLED', true),
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', true),
    'nonce_generator' => RandomString::class,
    'nonce_enabled' => env('CSP_NONCE_ENABLED', false),
    'upgrade_insecure_requests' => (bool) env('CSP_UPGRADE_INSECURE_REQUESTS', $isProduction),
    'sources' => [
        'asset' => $assetOrigins,
        'bunny' => $bunnyOrigins,
        'connect' => $connectOrigins,
        'vite_http' => $allowsViteDevServer ? $viteHttpOrigins : [],
    ],
];
