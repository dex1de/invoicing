<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\Repositories\ProductLineRepositoryInterface;
use Modules\Invoices\Infrastructure\Repositories\EloquentInvoiceRepository;
use Modules\Invoices\Infrastructure\Repositories\EloquentProductLineRepository;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;

final class InvoicingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InvoiceRepositoryInterface::class,
            EloquentInvoiceRepository::class
        );

        $this->app->bind(
            ProductLineRepositoryInterface::class,
            EloquentProductLineRepository::class
        );

        $this->app->bind(
            UuidFactoryInterface::class,
            UuidFactory::class
        );
    }
}
