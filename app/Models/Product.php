<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function categoryProduct()
    {
        return $this->belongsTo('App\Models\CategoryProduct');
    }

    public function variantProducts()
    {
        return $this->hasMany(VariantProduct::class, 'id_product');
    }
}
