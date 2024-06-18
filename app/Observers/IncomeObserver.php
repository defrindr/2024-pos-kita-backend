<?php

namespace App\Observers;

use App\Models\Income;
use Illuminate\Support\Facades\Cache;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        Cache::forget('incomes');
    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
        Cache::forget('incomes');
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        Cache::forget('incomes');
    }

    /**
     * Handle the Income "restored" event.
     */
    public function restored(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "force deleted" event.
     */
    public function forceDeleted(Income $income): void
    {
        //
    }
}
