<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\StripeServiceInterface;
use Illuminate\Support\Collection;
use NumberFormatter;
use Stripe\StripeClient;

final readonly class StripeService implements StripeServiceInterface
{
    private readonly NumberFormatter $currencyFormatter;

    public function __construct(
        private StripeClient $stripe,
    ) {
        $this->currencyFormatter = new NumberFormatter(config('app.locale', 'en_GB'), NumberFormatter::CURRENCY);
    }

    public function getActivePrices(): Collection
    {
        $prices = $this->stripe->prices->all([
            'active' => true,
            'expand' => ['data.product'],
        ]);

        return collect($prices->data)->map(function ($price) {
            return [
                'id' => $price->id,
                'title' => $price->product->name,
                'description' => $price->product->description ?? '',
                'price' => $this->currencyFormatter->formatCurrency($price->unit_amount / 100, $price->currency),
                'term' => $this->formatInterval($price->recurring->interval, $price->recurring->interval_count),
                'featured' => $price->product->metadata->featured ?? false,
                'order' => (int) ($price->product->metadata->order ?? 999),
            ];
        })
            ->sortBy('order')
            ->values();
    }

    private function formatInterval(string $interval, int $count): string
    {
        return $count > 1 ? "{$count} {$interval}s" : "1 {$interval}";
    }
}
