<?php declare(strict_types=1);

namespace Modules\Invoices\Domain\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;

interface ProductLineRepositoryInterface
{
    public function save(Invoice $invoice): void;
}
