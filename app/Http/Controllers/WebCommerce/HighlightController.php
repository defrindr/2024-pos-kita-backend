<?php

namespace App\Http\Controllers\WebCommerce;

use App\Models\Highlight;
use App\Models\HighlightImage;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
class HighlightController extends Controller{

    public function add(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'id_product' => 'required',
            'description' => 'required',
            'image_title' => 'required',
            'image_promo' => 'required',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $product = Product::all()->find($request->input('id_product'));
        if(!$product){
            return response([
                'message' => "There is no product with that id",
                'data' => null
            ], 201);
        }
        // Image
        $file = $request->file('file');
        $imageName = "public/images/highlight/".str_replace(' ', '', $file->getClientOriginalName());
        $file->move(public_path('images/highlight'), $imageName);

        $highlight = new Highlight();
        $highlight->title = $request->input('title');
        $highlight->id_product = $request->input('id_product');
        $highlight->description = $request->input('description');
        $highlight->image_title = $request->input('image_title');
        $highlight->image_promo = $request->input('image_promo');
        $highlight->image = $imageName; // new column for image

        $result = $highlight->save();
        if($result){
            return response([
                'message' => "Promo added",
                'data' => $highlight
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to add promo",
                'data' => null
            ], 400);
        }
    }

    public function showImage($id){
        $highlight = Highlight::findOrFail($id);
        $path = storage_path('app/' . $highlight->image);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function showall(Request $request){
        $user = auth()->guard('api')->user();
        $highlight = Highlight::all();
        $highlightDetails = $highlight->map(function ($map) {
            $product = Product::where('id', $map->id_product)->first();
            $umkm = User::where('id', $product->id_user)->first();
            return [
                'id' => $map->id,
                'id_product' => $map->id_product,
                'title' => $map->title,
                'description' => $map->description,
                'image' => $map->image,
                'image_title' => $map->image_title,
                'image_promo' => $map->image_promo,
                'product_name' => $product ? $product->name : null,
                'umkm_name' => $umkm ? $umkm->umkm_name : null,
                'slug_product_name' => Str::slug($product ? $product->name : null),
                'slug' => Str::slug($umkm ? $umkm->umkm_name : null)
            ];
        });
        if($highlight->isEmpty()){
            return response([
                'message' => "There are no promo",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $highlightDetails
            ], 200);
        }
    }
    public function show(Request $request){
        $user = auth()->guard('api')->user();
        $highlight = Highlight::all()->find($request->id);

        $result = $highlight;
        if($result){
            return response([
                'message' => "success",
                'data' => $highlight
            ], 200);
        }
        else{
            return response([
                'message' => "There is no promo with that id",
                'data' => null
            ], 400);
        }
    }
    public function edit(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'title' => 'nullable',
            'id_product' => 'nullable',
            'description' => 'nullable',
            'image_title' => 'nullable',
            'image_promo' => 'nullable',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $highlight = Highlight::all()->find($request->id);

        if(!empty($request->file('file'))){
            $file = $request->file('file');
            $imageName = "public/images/highlight/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/highlight'), $imageName);
            $highlight->image = $imageName;
        }

        if(!empty($request->input('title'))){
            $highlight->title = $request->input('title');
        }

        if(!empty($request->input('id_product'))){
            $highlight->id_product = $request->input('id_product');
        }

        if(!empty($request->input('description'))){
            $highlight->description = $request->input('description');
        }

        if(!empty($request->input('image_title'))){
            $highlight->image_title = $request->input('image_title');
        }

        if(!empty($request->input('image_promo'))){
            $highlight->image_promo = $request->input('image_promo');
        }

        $result = $highlight->save();
        if($result){
            return response([
                'message' => "Promo edited",
                'data' => $highlight
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to edit promo",
                'data' => null
            ], 400);
        }
    }

    public function delete(Request $request){
        $user = auth()->guard('api')->user();
        $highlight = Highlight::all()->find($request->id);
        $result = $highlight->delete();
        if($result){
            return response([
                'message' => "Promo deleted",
                'data' => $highlight
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to delete promo",
                'data' => null
            ], 400);
        }
    }
}
