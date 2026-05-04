<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    public function __invoke(SitemapService $sitemapService): Response
    {
        return $sitemapService->response();
    }
}
