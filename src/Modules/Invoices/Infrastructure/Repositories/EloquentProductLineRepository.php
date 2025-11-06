<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\ProductLineRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use Modules\Invoices\Infrastructure\EloquentModels\InvoiceProductLinesModel;
use Ramsey\Uuid\UuidInterface;

class EloquentProductLineRepository implements ProductLineRepositoryInterface
{
    public function findByInvoiceId(UuidInterface $id): ?Collection
    {
//        return InvoiceProductLinesModel::query()->where("invoice_id", $id->toString())->get();
    }

    public function save(Invoice $invoice): void
    {
        foreach ($invoice->getProductLines() as $line) {
            $model = new InvoiceProductLinesModel;
            $model->id = $line->getUuid();
            $model->invoice_id = $invoice->getId()->toString();
            $model->name = $line->getProductName();
            $model->price = $line->getUnitPrice();
            $model->quantity = $line->getQuantity();

            $model->save();
        }
    }
}
