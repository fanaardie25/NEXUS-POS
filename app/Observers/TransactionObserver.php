<?php

namespace App\Observers;

use App\Models\CashFlow;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        CashFlow::create([
            'date' => now(),
            'description' => "Order #" . $transaction->invoice_number,
            'type' => 'debit',
            'amount' => $transaction->total, 
            'financial_category_id' => 1, 
            'user_id' => $transaction->user_id,
            
            
            'trackable_id' => $transaction->id,
            'trackable_type' => Transaction::class,
        ]);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        CashFlow::where('trackable_id', $transaction->id)
            ->where('trackable_type', Transaction::class)
            ->delete();
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
