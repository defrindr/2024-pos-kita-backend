<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantProduct extends Model
{
    use HasFactory;
    protected $table = 'm_variant_product';
    protected $fillable = ['id_product', 'id_variant'];

    public function variant()
    {
        return $this->belongsTo('App\Models\Variant', 'id_variant');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\User', 'id_product');
    }
}
