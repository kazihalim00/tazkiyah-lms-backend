<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest; // এই ইমপোর্টটি ঠিক আছে

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // if ($this->app->environment('production')) {
        //     // এখানে Request এর বদলে SymfonyRequest ব্যবহার করুন
        //     SymfonyRequest::setTrustedProxies(
        //         ['0.0.0.0/0'],
        //         SymfonyRequest::HEADER_X_FORWARDED_FOR | SymfonyRequest::HEADER_X_FORWARDED_PROTO | SymfonyRequest::HEADER_X_FORWARDED_AWS_ELB
        //     );
        // }
    }
}