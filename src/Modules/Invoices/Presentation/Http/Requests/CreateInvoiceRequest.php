<?php declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'invoice_product_lines' => 'nullable|array',
            'invoice_product_lines.*.name' => 'required|string|',
            // Ensures it's not negative (gte = greater than or equal) and it has 0, 1, or 2 decimal places
            'invoice_product_lines.*.price' => 'required|numeric|gte:0|decimal:0,2',
            'invoice_product_lines.*.quantity' => 'required|integer',
        ];
    }
}
