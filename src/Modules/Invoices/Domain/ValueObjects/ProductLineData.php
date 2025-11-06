<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\ValueObjects;

use DomainException;

final readonly class ProductLineData
{
    private string $productName;
    private int $quantity;
    private Money $unitPrice;
    private Money $totalUnitPrice;

    /**
     * @param string $productName
     * @param int $quantity
     * @param Money $unitPrice
     */
    public function __construct(string $productName, int $quantity, Money $unitPrice)
    {
        //quantity must be positive
        if ($quantity <= 0) {
            throw new DomainException("Quantity must be positive");
        }

        //unit price must be positive
        if ($unitPrice->amount() <= 0) {
            throw new DomainException("Unit price must be positive");
        }

        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;

        //total is always calculated
        $this->totalUnitPrice = $unitPrice->multiply($quantity);
    }

    public function getProductName(): string { return $this->productName; }
    public function getQuantity(): int { return $this->quantity; }
    public function getUnitPrice(): Money { return $this->unitPrice; }
    public function getTotalUnitPrice(): Money { return $this->totalUnitPrice; }

    public function total(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}
