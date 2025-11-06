<?php declare(strict_types=1);

namespace Modules\Invoices\Application\Contracts;

interface InvoiceUseCaseInterface
{
    public function handle(object $dto);
}
