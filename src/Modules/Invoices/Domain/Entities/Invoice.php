<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\Entities;

use DomainException;
use Modules\Invoices\Domain\ValueObjects\Customer;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatusEnum;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use Ramsey\Uuid\UuidInterface;

class Invoice
{
    private UuidInterface $id;
    private InvoiceStatusEnum $status;
    private Customer $customer;
    private Money $totalPrice;

    /** @var ProductLineData[] */
    private array $productLines = [];

    /** @var object[] */
    private array $domainEvents = [];

    private function __construct() {}

    public static function create(UuidInterface $id, Customer $customer): self
    {
        $invoice = new self();
        $invoice->id = $id;
        $invoice->customer = $customer;
        $invoice->status = InvoiceStatusEnum::DRAFT;

        // Record a domain event

        return $invoice;
    }

    public function addLineItem(ProductLineData $line): void
    {
        //cannot add item to a non drafted invoice
        if (!$this->status->equals(InvoiceStatusEnum::DRAFT)) {
            throw new DomainException("Cannot add line items to an invoice that is not in draft status");
        }

        $this->productLines[] = $line;

        //the total price must always be correct
        $this->recalculateTotalPrice();
    }

    public function markAsSending(): void
    {
        if (!$this->status->equals(InvoiceStatusEnum::DRAFT)) {
            throw new DomainException("Only draft invoices can be marked as sending");
        }

        //cannot send an empty invoice
        if (empty($this->productLines)) {
            throw new DomainException("Cannot send an invoice with no line items");
        }

        $this->status = InvoiceStatusEnum::Sending;
    }

    public function markAsSentToClient(): void
    {
        if (!$this->status->equals(InvoiceStatusEnum::Sending)) {
            throw new DomainException("Invoice must be in 'sending' status to be marked as 'sent-to-client''");
        }

        $this->status = InvoiceStatusEnum::SentToClient;
    }

    private function recalculateTotalPrice(): void
    {
        $total = 0;
        foreach ($this->productLines as $line) {
            $total += $line->getTotalUnitPrice()->amount();
        }
        $this->totalPrice = new Money($total);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
    public function getStatus(): InvoiceStatusEnum
    {
        return $this->status;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
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
        Customer $customer,
        array $productLines,

    ): self {
        $invoice = new self();
        $invoice->id = $id;
        $invoice->status = $status;
        $invoice->customer = $customer;
        $invoice->productLines = $productLines;

        $invoice->recalculateTotalPrice();

        return $invoice;
    }
}
