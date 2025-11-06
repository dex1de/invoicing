<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Application\Contracts\InvoiceUseCaseFactory;
use Modules\Invoices\Application\UseCases\{
    CreateInvoiceUseCase,
    SendInvoiceUseCase,
    ViewInvoiceUseCase
};

final class InvoiceUseCaseServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(InvoiceUseCaseFactory::class, function ($app) {
            return new InvoiceUseCaseFactory(
                $app->make(CreateInvoiceUseCase::class),
                $app->make(SendInvoiceUseCase::class),
                $app->make(ViewInvoiceUseCase::class)
            );
        });
    }

    /** @return array<class-string> */
    public function provides(): array
    {
        return [
            InvoiceUseCaseFactory::class
        ];
    }
}
