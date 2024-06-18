<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CategoryProduct;
use App\Models\CategoryNews;
use App\Models\LabelNews;

class CategoryController extends Controller
{
    function addProductCategory(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'name' => 'required | unique:m_category_product,name'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $categoryProduct = new CategoryProduct();
        $categoryProduct->name = $request->input('name');

        if(!$categoryProduct->save()){
            return response([
                'message' => "Failed to add product category",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Category product added",
            'data' => [
                'product' => $categoryProduct,
            ]
        ], 201);

    }

    function getAllProductCategory(Request $request){
        $user = auth()->guard('api')->user();

        $categoryProduct = CategoryProduct::all();

        if($categoryProduct->isEmpty()){
            return response([
                'message' => "There are no product category",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $categoryProduct
            ], 200);
        }

    }

    function addNewsCategory(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'name' => 'required | unique:m_category_news,name'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $categoryNews = new CategoryNews();
        $categoryNews->name = $request->input('name');

        if(!$categoryNews->save()){
            return response([
                'message' => "Failed to add news category",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Category news added",
            'data' => [
                'product' => $categoryNews,
            ]
        ], 201);

    }

    function getAllNewsCategory(Request $request){
        $user = auth()->guard('api')->user();

        $categoryNews = CategoryNews::all();

        if($categoryNews->isEmpty()){
            return response([
                'message' => "There are no news category",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $categoryNews
            ], 200);
        }

    }

    function addNewsLabel(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'name' => 'required | unique:m_label_news,name'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $labelNews = new LabelNews();
        $labelNews->name = $request->input('name');

        if(!$labelNews->save()){
            return response([
                'message' => "Failed to add news label",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Label news added",
            'data' => [
                'product' => $labelNews,
            ]
        ], 201);

    }

    function getAllNewsLabel(Request $request){
        $user = auth()->guard('api')->user();

        $labelNews = LabelNews::all();

        if($labelNews->isEmpty()){
            return response([
                'message' => "There are no news label",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $labelNews
            ], 200);
        }

    }
}
