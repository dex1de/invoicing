<?php declare(strict_types=1);

namespace Modules\Invoices\Application\UseCases;

use Illuminate\Support\Facades\DB;
use Modules\Invoices\Application\Dtos\CreateInvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Events\InvoiceCreatedEvent;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\Repositories\ProductLineRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\Customer;
use Ramsey\Uuid\UuidFactoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class CreateInvoiceUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface     $invoiceRepository,
        private ProductLineRepositoryInterface $productLinesRepository,
        private UuidFactoryInterface           $uuidFactory,
        private Dispatcher                     $dispatcher,
    ) {}

    /**
     * @param object $dto
     * @return void
     */
    public function handle(CreateInvoiceData $dto): void
    {
        $id = $this->uuidFactory->uuid4();
        $customer = new Customer($dto->customerName, $dto->customerEmail);
        $productItems = $dto->productLines;

        DB::transaction(function () use ($id, $customer, $productItems) {
            $invoice = Invoice::create($id, $customer);

            foreach ($productItems as $productItem) {
                 $invoice->addLineItem($productItem);
            }

            $this->invoiceRepository->save($invoice);
            $this->productLinesRepository->save($invoice);

            $this->dispatcher->dispatch(new InvoiceCreatedEvent(
                $invoice->getId(),
                $invoice->getCustomer()->getEmail(),
            ));
        });
    }
}
