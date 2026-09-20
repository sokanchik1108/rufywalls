<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLayer extends Model
{
    protected $fillable = [
        'warehouse_id',
        'variant_id',
        'batch_id',
        'source_type',
        'source_id',
        'quantity',
        'unit_cost',
        'layer_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'layer_date' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function receiptItem()
    {
        return $this->belongsTo(
            ReceiptItem::class,
            'source_id'
        )->where('source_type', 'receipt');
    }

    /*
     * Получаем актуальную себестоимость слоя.
     *
     * initial:
     * берём текущий purchase_price товара.
     *
     * receipt:
     * берём сохранённую цену приёмки.
     */
    public function getCurrentUnitCostAttribute()
    {
        if ($this->source_type === 'initial') {
            return (float) (
                $this->variant
                    ->product
                    ->purchase_price ?? 0
            );
        }

        return (float) ($this->unit_cost ?? 0);
    }
}