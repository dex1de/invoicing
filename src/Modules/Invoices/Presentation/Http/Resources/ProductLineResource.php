<?php declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_name' => $this->resource->getProductName(),
            'quantity' => $this->resource->getQuantity(),
            'unit_price' => $this->resource->getUnitPrice()->toFloat(),
            'total_unit_price' => $this->resource->getTotalUnitPrice()->toFloat(),
        ];
    }
}
