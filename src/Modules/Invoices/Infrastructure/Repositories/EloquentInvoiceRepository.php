<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\Customer;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use Modules\Invoices\Infrastructure\EloquentModels\InvoiceProductLinesModel;
use Modules\Invoices\Infrastructure\EloquentModels\InvoicesModel;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Event;

class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(string $invoiceId): ?Invoice
    {
        $model = InvoicesModel::query()->with('productItems')->find($invoiceId);

        if (!$model) {
            return null;
        }

        $lineItems = $model->productItems->map(function (InvoiceProductLinesModel $item) {
            return new ProductLineData(
                $item->name,
                $item->quantity,
                new Money($item->price),
                $item->id
            );
        })->all();

        return Invoice::hydrate(
            Uuid::fromString($model->id),
            $model->status,
            new Customer($model->customer_name, $model->customer_email),
            $lineItems,
        );
    }

    public function save(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {

            $model = InvoicesModel::query()->findOrNew($invoice->getId()->toString());

            $model->id = $invoice->getId()->toString();
            $model->customer_name = $invoice->getCustomer()->getName();
            $model->customer_email = $invoice->getCustomer()->getEmail();
            $model->status = $invoice->getStatus()->value;

            $model->save();

            // get all IDs from the domain entities
            $domainLineItemIds = array_map(
                fn(ProductLineData $item) => $item->getUuid(),
                $invoice->getProductLines()
            );

            // delete any line items from DB that are no longer in the domain
            $model->productItems()->whereNotIn('id', $domainLineItemIds)->delete();

            // upsert all line items from the domain
            $lineItemData = [];
            foreach ($invoice->getProductLines() as $lineItem) {
                $lineItemData[] = [
                    'id' => $lineItem->getUuid(),
                    'invoice_id' => $invoice->getId()->toString(),
                    'name' => $lineItem->getProductName(),
                    'quantity' => $lineItem->getQuantity(),
                    'price' => $lineItem->getUnitPrice()->amount(),
                ];
            }

            InvoiceProductLinesModel::query()->upsert($lineItemData, ['id'], ['invoice_id', 'name', 'quantity', 'price']);

            // dispatch Domain Events

            foreach ($invoice->releaseEvents() as $event) {
                Event::dispatch($event);
            }
        });
    }
}
