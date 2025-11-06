<?php declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Invoices\Application\Contracts\InvoiceUseCaseFactory;
use Modules\Invoices\Application\Dtos\CreateInvoiceData;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use Modules\Invoices\Presentation\Http\Requests\CreateInvoiceRequest;
use Symfony\Component\HttpFoundation\Response;

final readonly class InvoiceController
{
    public function __construct(
        private InvoiceUseCaseFactory $factory
    ) {}

    public function create(CreateInvoiceRequest $request): JsonResponse
    {
        $productLines = collect($validated['invoice_product_lines'] ?? [])
            ->map(fn($line) => new ProductLineData(
                productName: $line['name'],
                quantity:    (int) $line['quantity'],
                unitPrice:   Money::fromFloat((float) $line['price']),
            ))
            ->toArray();

        $dto = new CreateInvoiceData(
            customerName:  $request['customer_name'],
            customerEmail: $request['customer_email'],
            productLines:  $productLines
        );
        $useCase = $this->factory->make('create');

        try {
            $result = $useCase->handle($dto);

            return response()->json([
                'result' => $result
            ])->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return response()->json([
                'result' => $exception->getMessage()
            ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function send(Request $request): JsonResponse
    {
        $useCase = $this->factory->make('send');
        return response()->json([
            'result' => $useCase->handle($request->all()),
        ]);
    }

    public function view(Request $request): JsonResponse
    {
        $useCase = $this->factory->make('view');
        return response()->json([
            'result' => $useCase->handle($request->all()),
        ]);
    }
}
