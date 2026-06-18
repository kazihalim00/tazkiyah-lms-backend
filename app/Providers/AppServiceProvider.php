<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }



    public function boot(): void
    {

        if ($this->app->environment('production')) {
            Request::setTrustedProxies(
                ['0.0.0.0/0'],
                Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB
            );
        }
    }
}