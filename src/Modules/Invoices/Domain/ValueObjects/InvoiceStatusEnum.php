<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\ValueObjects;

use InvalidArgumentException;

enum InvoiceStatusEnum: string
{
    case DRAFT = 'draft';
    case Sending = 'sending';
    case SentToClient = 'sent-to-client';

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public static function fromString(string $status): self
    {
        return self::tryFrom($status)
            ?? throw new InvalidArgumentException("Invalid invoice status: {$status}");
    }
}
