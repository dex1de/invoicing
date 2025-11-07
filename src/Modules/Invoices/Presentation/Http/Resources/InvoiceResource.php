<?php declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'invoice_id' => $this->resource->getId()->toString(),
            'status' => $this->resource->getStatus()->value,
            'customer_name' => $this->resource->getCustomer()->getName(),
            'customer_email' => $this->resource->getCustomer()->getEmail(),

            'invoice_product_lines' => ProductLineResource::collection($this->resource->getProductLines()),

            'total_price' => $this->resource->getTotalPrice()->toFloat(),
        ];
    }
}
