<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;
use Statamic\Events\EntrySaved;
use Statamic\Facades\Stache;

class ClearStatamicCache
{
    /**
     * Handle the event.
     */
    public function handle(EntrySaved $event): void
    {
        Stache::clear();
        Cache::clear();
    }
}
