<?php

namespace App\Models;

use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'id_transaction', 'id');
    }
}
