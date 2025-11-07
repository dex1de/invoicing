<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\ProductLineRepositoryInterface;
use Modules\Invoices\Infrastructure\EloquentModels\InvoiceProductLinesModel;

class EloquentProductLineRepository implements ProductLineRepositoryInterface
{
    public function save(Invoice $invoice): void
    {
        foreach ($invoice->getProductLines() as $line) {
            $model = new InvoiceProductLinesModel;
            $model->id = $line->getUuid();
            $model->invoice_id = $invoice->getId()->toString();
            $model->name = $line->getProductName();
            $model->price = $line->getUnitPrice()->amount();
            $model->quantity = $line->getQuantity();

            $model->save();
        }
    }
}
