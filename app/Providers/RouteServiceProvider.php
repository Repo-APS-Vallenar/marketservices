<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "/home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string|null
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Get the post-login redirect path based on user role.
     */
    public static function redirectTo(Request $request): string
    {
        $user = $request->user();
        if (!$user) {
            return '/';
        }
        return match ($user->role) {
            'admin' => '/admin/dashboard',
            'service_provider' => '/provider/dashboard',
            'customer' => '/customer/dashboard',
            default => '/dashboard',
        };
    }
} 