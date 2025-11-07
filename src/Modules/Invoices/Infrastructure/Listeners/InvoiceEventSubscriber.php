<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Events\Dispatcher;
use Modules\Invoices\Domain\Events\InvoiceSendingEvent;
use Modules\Invoices\Domain\Events\InvoiceSendToClientEvent;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class InvoiceEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
    ){}

    public function handleInvoiceSendingEvent(InvoiceSendingEvent $event): void
    {

    }

    public function handleInvoiceSendToClientEvent(InvoiceSendToClientEvent $event): void
    {

    }

    public function handleResourceDelivered(ResourceDeliveredEvent $event): void
    {
        // add delay to prevent race condition
        $this->release(10);

        $invoice = $this->invoiceRepository->findById($event->resourceId->toString());

        $invoice->markAsSentToClient();

        $this->invoiceRepository->save($invoice);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            InvoiceSendingEvent::class,
            [InvoiceEventSubscriber::class, 'handleInvoiceSendingEvent']
        );

        $events->listen(
            InvoiceSendToClientEvent::class,
            [InvoiceEventSubscriber::class, 'handleInvoiceSendToClientEvent']
        );

        $events->listen(
            ResourceDeliveredEvent::class,
            [InvoiceEventSubscriber::class, 'handleResourceDelivered']
        );
    }
}
