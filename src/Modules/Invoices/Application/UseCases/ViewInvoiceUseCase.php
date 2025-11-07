<?php declare(strict_types=1);

namespace Modules\Invoices\Application\UseCases;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;

final readonly class ViewInvoiceUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function handle(string $invoiceId): ?Invoice
    {
        return $this->invoiceRepository->findById($invoiceId);
    }
}
