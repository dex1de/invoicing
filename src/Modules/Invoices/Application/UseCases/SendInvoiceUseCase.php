<?php declare(strict_types=1);

namespace Modules\Invoices\Application\UseCases;

use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\Services\InvoiceSender;

final readonly class SendInvoiceUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceSender              $invoiceSender,
    ) {}

    public function handle(string $invoiceId): void
    {
        $invoice = $this->invoiceRepository->findById($invoiceId);

        if (!$invoice) {
            return;
        }

        $this->invoiceSender->send($invoice);

        $this->invoiceRepository->save($invoice);

    }
}
