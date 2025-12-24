<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'auth' => fn () => auth()->check()
                ? [
                    'user' => auth()->user(),
                    'role' => auth()->user()->role,
                    'isAdmin' => auth()->user()->role === 'admin',
                ]
                : null,

            'notifications' => fn () => auth()->check()
                ? auth()->user()
                    ->unreadNotifications
                    ->map(fn ($n) => [
                        'id'         => $n->id,
                        'title'      => $n->data['title'] ?? '',
                        'message'    => $n->data['message'] ?? '',
                        'url'        => $n->data['url'] ?? '#',
                        'created_at' => $n->created_at->diffForHumans(),
                    ])
                : [],
        ]);
    }
}
