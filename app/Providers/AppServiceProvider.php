<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Dynamic system settings at runtime
        try {
            if (!app()->runningInConsole() && \Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $sessionLifetime = \App\Models\SystemSetting::get('session_lifetime_minutes');
                if ($sessionLifetime) {
                    config(['session.lifetime' => (int) $sessionLifetime]);
                }

                $appName = \App\Models\SystemSetting::get('app_name');
                if ($appName) {
                    config(['app.name' => $appName]);
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika database belum siap/migrasi
        }

        // Superadmin bypasses all gate checks
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
        });

        // Saat pengguna yang sudah login membuka /login, arahkan ke dashboard
        // sesuai perannya. Tanpa ini, middleware "guest" selalu memakai route
        // "dashboard" (dashboard operasional PPA) sehingga Superadmin ikut
        // dilempar ke sana.
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = $request->user();

            if (! $user) {
                return route('login');
            }

            return match ($user->role) {
                'superadmin' => route('superadmin.dashboard'),
                'admin_dinsos' => route('dinsos.dashboard'),
                default => route('dashboard'),
            };
        });
    }
}
