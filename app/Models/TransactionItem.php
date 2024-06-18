<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionItem extends Model
{
    use HasFactory;
    protected $table = 'transaction_item';

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction', 'id_transaction');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
