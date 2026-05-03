<?php

declare(strict_types=1);

namespace App\Csp;

final class CspSources
{
    public static function originFromUrl(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $parts = parse_url($url);

        if (! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $origin = "{$parts['scheme']}://{$parts['host']}";

        if (isset($parts['port'])) {
            $origin .= ":{$parts['port']}";
        }

        return $origin;
    }

    public static function unique(array $values): array
    {
        $sources = [];

        foreach ($values as $value) {
            if ($value !== null && $value !== '' && ! in_array($value, $sources, true)) {
                $sources[] = $value;
            }
        }

        return $sources;
    }

    public static function viteHosts(string $host): array
    {
        return self::unique([
            'localhost',
            '127.0.0.1',
            '[::1]',
            self::viteHost($host),
        ]);
    }

    public static function viteHttpOrigins(array $hosts, int $port): array
    {
        return self::viteOrigins($hosts, $port, ['http', 'https']);
    }

    public static function viteSocketOrigins(array $hosts, int $port): array
    {
        return self::viteOrigins($hosts, $port, ['ws', 'wss']);
    }

    private static function viteOrigins(array $hosts, int $port, array $schemes): array
    {
        $origins = [];

        foreach ($hosts as $host) {
            foreach ($schemes as $scheme) {
                $origins[] = "$scheme://$host:$port";
            }
        }

        return self::unique($origins);
    }

    private static function viteHost(string $host): ?string
    {
        return match ($host) {
            '', '0.0.0.0' => null,
            '::1' => '[::1]',
            default => $host,
        };
    }
}
