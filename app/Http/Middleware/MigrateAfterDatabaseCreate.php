<?php

namespace App\Http\Middleware;

use App\Http\Controllers\databaseController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class MigrateAfterDatabaseCreate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->route()->action['controller'] === databaseController::class && $request->method() === 'create') {
            // Jalankan php artisan migrate
            Artisan::call('migrate');
        }

        return $next($request);
    }
}
