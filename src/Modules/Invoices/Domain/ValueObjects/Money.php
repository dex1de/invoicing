<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private int $amount, // stored in cents
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }
    }

    public static function fromFloat(float $value): self
    {
        $decimalString = number_format($value, 2, '.', '');

        return new self((int)bcmul($decimalString, '100', 0));
    }

    public function add(Money $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function multiply(int $quantity): self
    {
        return new self($this->amount * $quantity);
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function __toString(): string
    {
        return number_format($this->toFloat(), 2);
    }
}
