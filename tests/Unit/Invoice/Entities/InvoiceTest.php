<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice\Entities;

use DomainException;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Events\InvoiceSendingEvent;
use Modules\Invoices\Domain\Events\InvoiceSendToClientEvent;
use Modules\Invoices\Domain\ValueObjects\Customer;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatusEnum;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class InvoiceTest extends TestCase
{
    private Customer $customer;
    private UuidInterface $invoiceId;

    protected function setUp(): void
    {
        parent::setUp();
        // Create common, reusable test data
        $this->customer = new Customer('Test Customer', 'test@customer.com');
        $this->invoiceId = Uuid::uuid4();
    }

    /**
     * Helper method to create a clean, draft invoice for tests.
     */
    private function createDraftInvoice(): Invoice
    {
        return Invoice::create($this->invoiceId, $this->customer);
    }

    /** @test */
    public function itCanBeCreatedInADraftState(): void
    {
        $invoice = $this->createDraftInvoice();

        $this->assertEquals(InvoiceStatusEnum::DRAFT, $invoice->getStatus());
        $this->assertEquals($this->customer, $invoice->getCustomer());
        $this->assertEquals($this->invoiceId, $invoice->getId());
        $this->assertEquals(new Money(0), $invoice->getTotalPrice());
        $this->assertEmpty($invoice->releaseEvents(), "No events should be fired on creation.");
    }

/** @test */
public function itCorrectlyAddsLineItemsAndRecalculatesTotal(): void
{
    $invoice = $this->createDraftInvoice();

    // Act: Add two line items
    $invoice->addLineItem(new ProductLineData("Product A", 2, new Money(1000), Uuid::uuid4()->toString()));
    $invoice->addLineItem(new ProductLineData("Product B", 10, new Money(50), Uuid::uuid4()->toString()));

    // Assert
    $this->assertEquals(new Money(2500), $invoice->getTotalPrice());
}

    /** @test */
    public function it_can_be_marked_as_sending_and_fires_event(): void
    {
    // Arrange
    $invoice = $this->createDraftInvoice();

    // Act
    $invoice->markAsSending();

    // Assert: Status is correct
    $this->assertEquals(InvoiceStatusEnum::Sending, $invoice->getStatus());

    // Assert: Event was recorded
    $events = $invoice->releaseEvents();
    $this->assertCount(1, $events);
    $this->assertInstanceOf(InvoiceSendingEvent::class, $events[0]);
    $this->assertEquals($this->invoiceId->toString(), $events[0]->invoiceId);
}

    /** @test */
    public function it_can_be_marked_as_sent_to_client_and_fires_event(): void
    {
    // Arrange: Create an invoice and put it in the 'sending' state
    $invoice = $this->createDraftInvoice();
    $invoice->markAsSending();
    $invoice->releaseEvents(); // Clear events from the previous step

    // Act
    $invoice->markAsSentToClient();

    // Assert: Status is correct
    $this->assertEquals(InvoiceStatusEnum::SentToClient, $invoice->getStatus());

    // Assert: Event was recorded
    $events = $invoice->releaseEvents();
    $this->assertCount(1, $events);
    $this->assertInstanceOf(InvoiceSendToClientEvent::class, $events[0]);
    $this->assertEquals($this->invoiceId->toString(), $events[0]->invoiceId);
}

    /**
     * @test
     * This is the test for YOUR specific requirement.
     */
    public function it_cannot_be_marked_as_sent_to_client_from_draft_status(): void
    {
    // Arrange
    $invoice = $this->createDraftInvoice();
    $this->assertEquals(InvoiceStatusEnum::DRAFT, $invoice->getStatus());

    // Assert: We expect a specific exception
    $this->expectException(DomainException::class);
    $this->expectExceptionMessage("Invoice must be in 'sending' status to be marked 'sent-to-client'.");

    // Act: This line should throw the exception
    $invoice->markAsSentToClient();
}

    /** @test */
    public function it_cannot_be_marked_as_sending_if_already_sent(): void
    {
    // Arrange: Create an invoice and mark it as sent
    $invoice = $this->createDraftInvoice();
    $invoice->markAsSending();
    $invoice->markAsSentToClient();
    $this->assertEquals(InvoiceStatusEnum::SentToClient, $invoice->getStatus());

    // Assert
    $this->expectException(DomainException::class);
    $this->expectExceptionMessage("Invoice can only be marked as 'sending' from 'draft' status.");

    // Act
    $invoice->markAsSending();
}

    /** @test */
    public function it_cannot_add_line_items_when_sent_to_client(): void
    {
        // Arrange: Create an invoice and mark it as sent
        $invoice = $this->createDraftInvoice();
        $invoice->markAsSending();
        $invoice->markAsSentToClient();

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Cannot add line items to an invoice that is not in 'draft' status.");

        // Act
        $invoice->addLineItem(new ProductLineData("Product C", 1, new Money(100), Uuid::uuid4()->toString()));
    }
}
