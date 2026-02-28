<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Support\Collection;

interface StripeServiceInterface
{
    public function getActivePrices(): Collection;
}
