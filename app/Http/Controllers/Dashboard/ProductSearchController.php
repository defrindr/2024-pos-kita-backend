<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductSearchController extends Controller
{

    public function show(Request $request)
    {
        $user = auth()->guard('api')->user();

        $products = Product::where('id_user', $user->id)->where('name', 'like', '%' . $request->input('query') . '%')->orWhere('description', 'like', '%' . $request->input('query') . '%')->orderBy('updated_at', 'desc')->orderBy('updated_at', 'desc')->paginate(10);
        return response([
            'message' => "success",
            'query' => $request->input('query'),
            'data' => $products
        ], 200);
    }

    public function sortBy(Request $request)
    {
        $user = auth()->guard('api')->user();

        // Create a base query
        $products = Product::where('id_user', $user->id);

        if ($request->input('query')) {
            $products = $products->where('name', 'like', '%' . $request->input('query') . '%');
        }

        if ($request->input('sortByStock')) {
            $products = $products->orderBy('stock', $request->input('sortByStock'));
        } elseif ($request->input('sortByPrice')) {
            $products = $products->orderBy('price', $request->input('sortByPrice'));
        } elseif ($request->input('sortBySKU')) {
            $products = $products->orderBy('sku', $request->input('sortBySKU'));
        } elseif ($request->input('sortByName')) {
            $products = $products->orderBy('name', $request->input('sortByName'));
        } elseif ($request->input('sortByStatus')) {
            $products = $products->orderBy('status', $request->input('sortByStatus'));
        } else {
            $products = $products->orderBy('updated_at', 'desc');
        }

        $products = $products->paginate(10);

        return response([
            'message' => 'success',
            'query' => $request->input('query'),
            'data' => $products,
        ], 200);
    }
}
