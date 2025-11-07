<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\Events;

use Modules\Invoices\Domain\ValueObjects\InvoiceStatusEnum;
use Ramsey\Uuid\UuidInterface;

final readonly class InvoiceSendingEvent {
    public function __construct(
        public UuidInterface $invoiceId,
        public InvoiceStatusEnum $status
    ) {}
}
