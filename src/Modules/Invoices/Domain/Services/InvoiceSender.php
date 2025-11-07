<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\Services;

use DomainException;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Throwable;

final readonly class InvoiceSender
{
    public function __construct(
        private NotificationFacadeInterface $notificationFacade
    ) {}

    public function send(Invoice $invoice): void
    {
        $data = new NotifyData(
            resourceId: $invoice->getId(),
            toEmail: $invoice->getCustomer()->getEmail(),
            subject: "Invoice #{$invoice->getId()}",
            message: "Hi, I’ve attached the invoice for benefits!",
        );

        try {
            $this->notificationFacade->notify($data);
        } catch (Throwable $e) {
            throw new DomainException("Failed to send invoice: " . $e->getMessage(), 0, $e);
        }

        $invoice->markAsSending();
    }
}
