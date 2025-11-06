<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\EloquentModels\InvoicesModel;
use Ramsey\Uuid\UuidInterface;

class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(UuidInterface $id): ?InvoicesModel
    {
        return InvoicesModel::query()->find($id);
    }

    public function save(Invoice $invoice): void
    {
        $entity = new InvoicesModel;
        $entity->id = $invoice->getId();
        $entity->customer_name = $invoice->getCustomerName();
        $entity->customer_email = $invoice->getCustomerEmail();
        $entity->status = $invoice->getStatus();

        $entity->save();
    }
}
