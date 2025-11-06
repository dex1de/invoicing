<?php declare(strict_types=1);

namespace Modules\Invoices\Application\Dtos;

use Modules\Invoices\Domain\ValueObjects\ProductLineData;

readonly class CreateInvoiceData
{
    /**
     * @param string $customerName
     * @param string $customerEmail
     * @param ProductLineData[] $productLines
     */
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public array $productLines = [],
    ) {}

    public function totalAmount(): int
    {
        return array_sum(array_map(fn($line) => $line->total()->amount(), $this->productLines));
    }
}
