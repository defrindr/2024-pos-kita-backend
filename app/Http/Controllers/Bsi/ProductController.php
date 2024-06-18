<?php

namespace App\Http\Controllers\Bsi;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::All();

        return response([
            'message' => "success",
            'data' => $products
        ], 200);
    }
}
