<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $guarded = [];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function adjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }
}
