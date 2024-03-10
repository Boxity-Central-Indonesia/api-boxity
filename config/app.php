<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'fallback_locale' => 'id',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => 'file',
    ],
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\TelescopeServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        Collective\Html\HtmlServiceProvider::class,
        SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,
    ])->toArray(),
    'aliases' => Facade::defaultAliases()->merge([
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,
        'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
    ])->toArray(),
];
