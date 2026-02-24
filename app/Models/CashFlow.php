<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $guarded = [];

    public function financialCategory()
    {
        return $this->belongsTo(FinancialCategory::class);
    }

    public function trackable()
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cashFlow) {
            if (auth()->check()) {
                $cashFlow->user_id = auth()->id();
            }
        });
    }
}
