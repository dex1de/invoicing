<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

final class RouteServiceProvider extends ServiceProvider
{
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        $this->loadRoutesFrom(base_path('src/Modules/Invoices/Presentation/routes.php'));
    }
}
