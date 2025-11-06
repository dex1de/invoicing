<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\Customer;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatusEnum;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use Modules\Invoices\Infrastructure\EloquentModels\InvoiceProductLinesModel;
use Modules\Invoices\Infrastructure\EloquentModels\InvoicesModel;
use Ramsey\Uuid\UuidInterface;

class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(UuidInterface $id): ?Invoice
    {
        $model = InvoicesModel::query()->with('productItems')->find($id->toString());

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
            $model->id,
            InvoiceStatusEnum::fromString($model->status),
            new Customer($model->customer_name, $model->customer_email),
            $lineItems,
        );
    }

    public function save(Invoice $invoice): void
    {
        $model = new InvoicesModel;
        $model->id = $invoice->getId()->toString();
        $model->customer_name = $invoice->getCustomer()->getName();
        $model->customer_email = $invoice->getCustomer()->getEmail();
        $model->status = $invoice->getStatus();

        $model->save();
    }
}
