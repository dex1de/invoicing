<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Invoices\Domain\Entities\Invoice;

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

    protected $fillable = [
        'id',
        'invoice_id',
        'name',
        'price',
        'quantity',
    ];

    public function invoice(): HasOne
    {
        return $this->hasOne(
            Invoice::class,
            'id',
            'invoice_id'
            );
    }
}
