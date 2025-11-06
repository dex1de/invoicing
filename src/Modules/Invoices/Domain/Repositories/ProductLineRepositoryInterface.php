<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\Repositories;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Entities\Invoice;
use Ramsey\Uuid\UuidInterface;

interface ProductLineRepositoryInterface
{
    public function findByInvoiceId(UuidInterface $id): ?Collection;

    public function save(Invoice $invoice): void;
}
