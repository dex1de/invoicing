<?php declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Invoices\Application\Dtos\CreateInvoiceData;
use Modules\Invoices\Application\UseCases\CreateInvoiceUseCase;
use Modules\Invoices\Application\UseCases\SendInvoiceUseCase;
use Modules\Invoices\Application\UseCases\ViewInvoiceUseCase;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Domain\ValueObjects\ProductLineData;
use Modules\Invoices\Presentation\Http\Requests\CreateInvoiceRequest;
use Modules\Invoices\Presentation\Http\Resources\InvoiceResource;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

final readonly class InvoiceController
{
    public function __construct(
        private UuidFactoryInterface $uuidFactory,
    ) {}

    public function create(
        CreateInvoiceRequest $request,
        CreateInvoiceUseCase $useCase
    ): JsonResponse {
        $productLines = collect($request['invoice_product_lines'] ?? [])
            ->map(fn($line) =>
            new ProductLineData(
                productName: $line['name'],
                quantity:    (int) $line['quantity'],
                unitPrice:   Money::fromFloat((float) $line['price']),
                uuid:        $this->uuidFactory->uuid4()->toString()
            ))
            ->toArray();

        $dto = new CreateInvoiceData(
            customerName:  $request['customer_name'],
            customerEmail: $request['customer_email'],
            productLines:  $productLines
        );

        try {
            $useCase->handle($dto);

            return response()->json(['message' => 'ok'])->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function send(string $reference, SendInvoiceUseCase $useCase): JsonResponse
    {
        try {
            $useCase->handle($reference);

            return response()->json(['message' => 'ok'])->setStatusCode(Response::HTTP_ACCEPTED);
        } catch (\Throwable $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function view(string $reference, ViewInvoiceUseCase $useCase): InvoiceResource|Response
    {
        try {
            $invoice = $useCase->handle($reference);

            return new InvoiceResource($invoice);
        } catch (\Throwable $exception) {
            return response()->json(['error' => $exception->getMessage()])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
