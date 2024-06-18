<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\CategoryProduct;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function add(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file5' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'id_category_product' => 'required | numeric',
            'name' => 'required',
            'description' => 'required',
            'cost' => 'nullable | numeric | between:1, 999999999999',
            'price' => 'required | numeric | between:1, 999999999999',
            'stock' => 'required | numeric',
            'status' => 'nullable | numeric',
            'variant' => 'nullable',
            'weight' => 'nullable | numeric',
            'length' => 'nullable | numeric',
            'width' => 'nullable | numeric',
            'height' => 'nullable | numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '1234567890';

        // Product
        $product = new Product();
        $product->id_user = $user->id;
        $product->id_category_product = $request->input('id_category_product');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->sku = str_replace(' ', '', Str::upper(substr(str_shuffle($characters), 0, 3))).str_replace(' ', '', Str::upper(substr(str_shuffle($numeric), 0, 2)));

        if(!empty($request->input('cost'))){
            $product->cost = $request->input('cost');
        }

        if(!empty($request->input('status'))){
            $product->status = $request->input('status');
        }

        if(!empty($request->input('variant'))){
            $product->variant = $request->input('variant');
        }
        if(!empty($request->input('weight'))){
            $product->weight = $request->input('weight');
        }
        if(!empty($request->input('length'))){
            $product->length = $request->input('length');
        }
        if(!empty($request->input('width'))){
            $product->width = $request->input('width');
        }
        if(!empty($request->input('height'))){
            $product->height = $request->input('height');
        }

        if(!empty($request->file('file'))){
            $file = $request->file('file');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image = $imageName;
        }
        if(!empty($request->file('file2'))){
            $file = $request->file('file2');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_2 = $imageName;
        }
        if(!empty($request->file('file3'))){
            $file = $request->file('file3');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_3 = $imageName;
        }
        if(!empty($request->file('file4'))){
            $file = $request->file('file4');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_4 = $imageName;
        }
        if(!empty($request->file('file5'))){
            $file = $request->file('file5');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_5 = $imageName;
        }
        // Image

        if(!$product->save()){
            return response([
                'message' => "Failed to add product",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Product added",
            'data' => [
                'product' => $product,
            ]
        ], 201);
    }

    public function show(Request $request){
        $user = auth()->guard('api')->user();

        $product = Product::all()->find($request->id);
        if($product==null){
            return response([
                'message' => "There is no product with that id",
                'data' => null
            ], 400);
        }
        $findUser = $product->id_user;
        $findCategory = $product->id_category_product;
        $umkm = User::where('id', $findUser)->first();
        $category = CategoryProduct::where('id', $findCategory)->first();

        $result = $product;

        if($result){
            return response([
                'message' => "success",
                'data' => $product,
                'product_category' => $category->name,
                'umkm' => $umkm->umkm_name,
                'slug' => Str::slug($product->name),
            ], 200);
        }
    }

    public function showAll(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'id_user' => 'required'
        ]);

        $products = Product::where('id_user', $request->input('id_user'))->get();
        if($products->isEmpty()){
            return response([
                'message' => "There are no product",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $products
            ], 200);
        }
    }

    public function edit(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'file5' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
            'name' => 'nullable',
            'description' => 'nullable',
            'cost' => 'nullable | numeric | between:1, 999999999999',
            'price' => 'nullable | numeric | between:1, 999999999999',
            'stock' => 'nullable | numeric',
            'status' => 'nullable | numeric',
            'variant' => 'nullable',
            'weight' => 'nullable | numeric',
            'length' => 'nullable | numeric',
            'width' => 'nullable | numeric',
            'height' => 'nullable | numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $product = Product::all()->find($request->id);
        if($product==null){
            return response([
                'message' => "There is no product with that id",
                'data' => null
            ], 400);
        }

        if(!empty($request->input('name'))){
            $product->name = $request->input('name');
        }

        if(!empty($request->input('description'))){
            $product->description = $request->input('description');
        }

        if(!empty($request->input('price'))){
            $product->price = $request->input('price');
        }

        if(!empty($request->input('stock'))){
            $product->stock = $request->input('stock');
        }

        if(!empty($request->input('cost'))){
            $product->cost = $request->input('cost');
        }

        if(!empty($request->input('status'))){
            $product->status = $request->input('status');
        }

        if(!empty($request->input('variant'))){
            $product->variant = $request->input('variant');
        }
        if(!empty($request->input('weight'))){
            $product->weight = $request->input('weight');
        }
        if(!empty($request->input('length'))){
            $product->length = $request->input('length');
        }
        if(!empty($request->input('width'))){
            $product->width = $request->input('width');
        }
        if(!empty($request->input('height'))){
            $product->height = $request->input('height');
        }

        if(!empty($request->file('file'))){
            $file = $request->file('file');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image = $imageName;
        }
        if(!empty($request->file('file2'))){
            $file = $request->file('file2');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_2 = $imageName;
        }
        if(!empty($request->file('file3'))){
            $file = $request->file('file3');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_3 = $imageName;
        }
        if(!empty($request->file('file4'))){
            $file = $request->file('file4');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_4 = $imageName;
        }
        if(!empty($request->file('file5'))){
            $file = $request->file('file5');
            $imageName = "public/images/product/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image_5 = $imageName;
        }

        $result = $product->save();
        if($result){
            return response([
                'message' => "Product edited",
                'data' => $product
            ], 200);
        }
        else{
            return response([
                'message' => "Failed to edit product",
                'data' => null
            ], 400);
        }

    }

    public function delete(Request $request){
        $user = auth()->guard('api')->user();
        $product = Product::find($request->id);
        $result = Product::destroy($request->id);

        if($result){
            return response([
                'message' => "Product deleted",
                'data' => $product
            ], 200);
        }
        else{
            return response([
                'message' => "Failed to delete product",
                'data' => null
            ], 400);
        }
    }

    public function search(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'query' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'message' => "enter a keyword"
            ], 400);
        }

        $products = Product::where('name', 'like', '%'.$request->input('query').'%')->orWhere('description', 'like', '%'.$request->input('query').'%')->get();
        if($products->isEmpty()){
            return response([
                'message' => "product not found",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $products
            ], 200);
        }
    }

    public function details(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'query' => 'required'
        ]);

        $product = Product::where('name',$request->input('query'))->first();
        $umkm = User::where('id', $product->id_user)->first();
        $otherProduct = Product::where('id_user', $umkm->id)->get();
        $otherProductDetails = $otherProduct->map(function ($other) {
            return [
                'id' => $other->id,
                'name' => $other->name,
                'description' => $other->description,
                'price' => $other->price,
                'stock' => $other->stock,
                'image' => $other->image,
                'image2' => $other->image_2,
                'image3' => $other->image_3,
                'image4' => $other->image_4,
                'image5' => $other->image_5,
                'slug-product' => Str::slug($other->name),
            ];
        });
        if($umkm==null){
            return response([
                'message' => "UMKM not found",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => [
                    'product' => $product,
                    'umkm' => $umkm,
                    'otherProducts' => $otherProductDetails,
                    'slug-product' => Str::slug($product->name),
                    'slug-umkm' => Str::slug($umkm->umkm_name)
                ]
            ], 200);
        }
    }

    public function showLatestProducts(Request $request){
        $user = auth()->guard('api')->user();

        $products = Product::orderBy('created_at', 'DESC')->get();

        $productDetails = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'slug-name' => Str::slug($product->name),
            ];
        });

        return response([
            'message' => "success",
            'data' => $productDetails
        ], 200);
    }
    public function showOldestProducts(Request $request){
        $user = auth()->guard('api')->user();

        $products = Product::orderBy('created_at', 'ASC')->get();

        $productDetails = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'slug-name' => Str::slug($product->name),
            ];
        });

        return response([
            'message' => "success",
            'data' => $productDetails
        ], 200);
    }

}
