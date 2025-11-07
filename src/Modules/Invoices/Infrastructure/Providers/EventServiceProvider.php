<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Invoices\Infrastructure\Listeners\InvoiceEventSubscriber;

class EventServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        InvoiceEventSubscriber::class,
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
