<?php declare(strict_types=1);

namespace Modules\Invoices\Application\Contracts;

use Modules\Invoices\Application\UseCases\CreateInvoiceUseCase;
use Modules\Invoices\Application\UseCases\SendInvoiceUseCase;
use Modules\Invoices\Application\UseCases\ViewInvoiceUseCase;

final readonly class InvoiceUseCaseFactory
{
    public function __construct(
        private CreateInvoiceUseCase $create,
        private SendInvoiceUseCase $send,
        private ViewInvoiceUseCase $view
    ) {}

    public function make(string $type): InvoiceUseCaseInterface
    {
        return match ($type) {
            'create' => $this->create,
            'send'   => $this->send,
            'view'   => $this->view,
            default  => throw new \InvalidArgumentException("Unknown invoice use case: {$type}")
        };
    }
}
