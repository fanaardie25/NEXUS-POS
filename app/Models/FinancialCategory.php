<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    protected $guarded = [];

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }
}
