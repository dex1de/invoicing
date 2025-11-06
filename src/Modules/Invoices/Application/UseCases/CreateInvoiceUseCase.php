<?php declare(strict_types=1);

namespace Modules\Invoices\Application\UseCases;

use Modules\Invoices\Application\Contracts\InvoiceUseCaseInterface;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\Repositories\ProductLineRepositoryInterface;
use Ramsey\Uuid\UuidFactoryInterface;

final readonly class CreateInvoiceUseCase implements InvoiceUseCaseInterface
{
    public function __construct(
        private InvoiceRepositoryInterface     $invoiceRepository,
        private ProductLineRepositoryInterface $productLinesRepository,
        private UuidFactoryInterface           $uuidFactory
    ) {}

    /**
     * @param object $dto
     * @return void
     */
    public function handle(object $dto): void
    {
        $id = $this->uuidFactory->uuid4();

        $invoice = Invoice::create($id, $dto);

        $this->invoiceRepository->save($invoice);

        $this->productLinesRepository->save($invoice);

//        $invoice->recordEvent(new InvoiceCreated($id->toString(), $customer->getEmail()));
    }
}
