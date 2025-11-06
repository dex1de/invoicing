<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Infrastructure\EloquentModels\InvoicesModel;
use Ramsey\Uuid\UuidInterface;

interface InvoiceRepositoryInterface
{
    public function findById(UuidInterface $id): ?InvoicesModel;

    public function save(Invoice $invoice): void;
}
