<?php declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Domain\ValueObjects\InvoiceStatusEnum;

/**
 * @property string $id
 * @property string $customer_name
 * @property string $customer_email
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class InvoicesModel extends Model
{
    protected $table = 'invoices';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'status' => InvoiceStatusEnum::class,
    ];

    protected $fillable = [
        'id',
        'customer_name',
        'customer_email',
        'status',
    ];

    public function productItems(): HasMany
    {
        return $this->hasMany(
            InvoiceProductLinesModel::class,
            'invoice_id',
            'id'
        );
    }
}
