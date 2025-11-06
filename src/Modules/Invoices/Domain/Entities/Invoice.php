<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\Entities;

use App\Modules\Invoicing\Domain\Events\InvoiceCreated;
use App\Modules\Invoicing\Domain\Events\InvoiceSentToClient;
use App\Modules\Invoicing\Domain\Events\InvoiceStatusChanged;
use App\Modules\Invoicing\Domain\ValueObjects\Customer;
use DomainException;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatusEnum;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use Ramsey\Uuid\UuidInterface;

class Invoice
{
    private UuidInterface $id;
    private InvoiceStatusEnum $status;
    private string $customerName;
    private string $customerEmail;
    private Money $totalPrice;

    /** @var ProductLineData[] */
    private array $productLines = [];

    /** @var object[] */
    private array $domainEvents = [];

    private function __construct() {}

    public static function create(UuidInterface $id, object $dto): self
    {
        $invoice = new self();
        $invoice->id = $id;
        $invoice->customerName = $dto->customerName;
        $invoice->customerEmail = $dto->customerEmail;
        $invoice->status = InvoiceStatusEnum::DRAFT;
        $invoice->totalPrice = $dto->totalAmount();
        $invoice->productLines = [];

        return $invoice;
    }

    public function addLineItem(string $productName, int $quantity, Money $unitPrice): void
    {
        //cannot add items to a send invoice
        if (!$this->status->equals(InvoiceStatus::draft())) {
            throw new DomainException("Cannot add line items to an invoice that is not in draft status.");
        }

        //InvoiceLineItem VO validates its own data (qty/price > 0)
        $line = new InvoiceLineItem($productName, $quantity, $unitPrice);
        $this->productLines[] = $line;

        //the total price must always be correct
        $this->recalculateTotalPrice();
    }

    public function markAsSending(): void
    {
        if (!$this->status->equals(InvoiceStatus::draft())) {
            throw new DomainException("Only draft invoices can be marked as sending.");
        }

        //cannot send an empty invoice
        if (empty($this->productLines)) {
            throw new DomainException("Cannot send an invoice with no line items.");
        }

        $this->status = InvoiceStatus::sending();
//        $this->recordEvent(new InvoiceStatusChanged($this->id->toString(), $this->status->value));
    }

    public function markAsSentToClient(): void
    {
        if (!$this->status->equals(InvoiceStatus::sending())) {
            throw new DomainException("Invoice must be in 'sending' status to be marked as 'sent'.");
        }

        $this->status = InvoiceStatus::sentToClient();

        //fire two events: one generic, one specific
//        $this->recordEvent(new InvoiceStatusChanged($this->id->toString(), $this->status->value));
//        $this->recordEvent(new InvoiceSentToClient($this->id->toString(), $this->customer->getEmail()));
    }

    private function recalculateTotalPrice(): void
    {
        $total = 0;
        foreach ($this->productLines as $line) {
            $total += $line->getTotalUnitPrice()->amount();
        }
        $this->totalPrice = new Money($total);
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
    public function getStatus(): InvoiceStatusEnum
    {
        return $this->status;
    }

    #todo Customer ValueObjects
    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }

    /** @return ProductLineData[] */
    public function getProductLines(): array
    {
        return $this->productLines;
    }

    public static function hydrate(
        UuidInterface $id,
        InvoiceStatusEnum $status,
        string $customerName,
        string $customerEmail,
        Money $totalPrice,
        array $lineItems
    ): self {
        $invoice = new self();
        $invoice->id = $id;
        $invoice->status = $status;
        $invoice->customerName = $customerName;
        $invoice->customerEmail = $customerEmail;
        $invoice->totalPrice = $totalPrice;
        $invoice->productLines = $lineItems;

        return $invoice;
    }
}
