<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Sitemap\SitemapUrl;
use DateTimeInterface;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;
use Stringable;

final class SitemapService
{
    public function response(): Response
    {
        return response()->view('sitemap', [
            'urls' => $this->urls(),
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return Collection<int, SitemapUrl>
     */
    public function urls(): Collection
    {
        return collect()
            ->merge($this->entryUrls('pages', fn ($entry): string => $entry->absoluteUrl()))
            ->merge($this->entryUrls('articles', fn ($entry): string => $entry->absoluteUrl()))
            ->merge($this->entryUrls('team', fn ($entry): string => url('/team/'.$entry->slug())))
            ->unique('loc')
            ->values();
    }

    /**
     * @return Collection<int, SitemapUrl>
     */
    private function entryUrls(string $collection, Closure $locResolver, ?string $changefreq = null, ?float $priority = null): Collection
    {
        return Entry::query()
            ->where('collection', $collection)
            ->where('published', true)
            ->get()
            ->reject(fn ($entry) => (bool) $entry->value('noindex'))
            ->map(fn ($entry) => $this->url(
                $locResolver($entry),
                $this->lastModified($entry->lastModified()),
                $changefreq,
                $priority,
            ))
            ->filter(fn (SitemapUrl $url) => filled($url->loc));
    }

    /**
     * @return Collection<int, SitemapUrl>
     */
    private function termUrls(string $taxonomy, ?string $changefreq = null, ?float $priority = null): Collection
    {
        return Term::query()
            ->where('taxonomy', $taxonomy)
            ->get()
            ->map(fn ($term) => $this->url(
                $term->absoluteUrl(),
                $this->lastModified($term->lastModified()),
                $changefreq,
                $priority,
            ))
            ->filter(fn (SitemapUrl $url) => filled($url->loc));
    }

    private function url(string $loc, ?string $lastmod, ?string $changefreq, ?float $priority): SitemapUrl
    {
        return new SitemapUrl($loc, $lastmod, $changefreq, $priority);
    }

    private function lastModified(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        return null;
    }
}
