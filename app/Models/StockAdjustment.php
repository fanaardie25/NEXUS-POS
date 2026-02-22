<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
{
    static::created(function ($adjustment) {
        $product = $adjustment->product;

        
        $qty = (int) $adjustment->qty; 

        
        $amount = ($adjustment->type === 'addition') ? $qty : -$qty;

        $product->increment('stock', $amount);
    });
}
}
