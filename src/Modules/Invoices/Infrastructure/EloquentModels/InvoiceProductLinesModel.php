<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $invoice_id
 * @property string $name
 * @property int $price
 * @property int $quantity
 * @property string $created_at
 * @property string $updated_at
 */
class InvoiceProductLinesModel extends Model
{
    protected $table = 'invoice_product_lines';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'invoice_id',
        'name',
        'price',
        'quantity',
    ];
}
