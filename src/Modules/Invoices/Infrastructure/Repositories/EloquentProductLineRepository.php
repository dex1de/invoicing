<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\ProductLineRepositoryInterface;
use Modules\Invoices\Infrastructure\EloquentModels\InvoiceProductLinesModel;
use Ramsey\Uuid\UuidInterface;

class EloquentProductLineRepository implements ProductLineRepositoryInterface
{

    public function findByInvoiceId(UuidInterface $id): ?Collection
    {
        return InvoiceProductLinesModel::query()->where("invoice_id", $id->getHex())->get();
    }

    public function save(Invoice $invoice): void
    {
        $productList = $invoice->getProductLines();

        foreach ($productList as $productLine) {
            $entity = new InvoiceProductLinesModel;
            $entity->id = $this->uuidFactory->uuid4();
            $entity->invoice_id = $invoice->getId();
        }
    }
}
