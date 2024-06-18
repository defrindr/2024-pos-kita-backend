<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Str;
use App\Models\VariantProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreProductRequest;
use App\Http\Requests\Dashboard\UpdateProductRequest;
use App\Http\Requests\Dashboard\UpdateVariantsProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->guard('api')->user();

        $products = Product::where('id_user', $user->id)->orderBy('updated_at', 'desc')->paginate(10);

        return response([
            'message' => "success",
            'data' => $products
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $user = auth()->guard('api')->user();

        $validated = $request->validated();

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '1234567890';

        // Product
        $product = new Product();

        if (!empty($request->file('file'))) {
            $file = $request->file('file');
            $imageName = "public/images/product/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image = $imageName;
        }
        if (!empty($request->file('file2'))) {
            $file2 = $request->file('file2');
            $imageName2 = "public/images/product/" . str_replace(' ', '', $file2->getClientOriginalName());
            $file2->move(public_path('images/product'), $imageName2);
            $product->image_2 = $imageName2;
        }
        if (!empty($request->file('file3'))) {
            $file3 = $request->file('file3');
            $imageName3 = "public/images/product/" . str_replace(' ', '', $file3->getClientOriginalName());
            $file3->move(public_path('images/product'), $imageName3);
            $product->image_3 = $imageName3;
        }
        if (!empty($request->file('file4'))) {
            $file4 = $request->file('file4');
            $imageName4 = "public/images/product/" . str_replace(' ', '', $file4->getClientOriginalName());
            $file4->move(public_path('images/product'), $imageName4);
            $product->image_4 = $imageName4;
        }
        if (!empty($request->file('file5'))) {
            $file5 = $request->file('file5');
            $imageName5 = "public/images/product/" . str_replace(' ', '', $file5->getClientOriginalName());
            $file5->move(public_path('images/product'), $imageName5);
            $product->image_5 = $imageName5;
        }

        $product->id_user = $user->id;
        $product->name = $validated['name'];
        $product->id_category_product = $validated['id_category_product'];
        $product->sku = str_replace(' ', '', Str::upper(substr(str_shuffle($characters), 0, 3))) . str_replace(' ', '', Str::upper(substr(str_shuffle($numeric), 0, 2)));
        $product->status = $validated['status'];
        $product->price = $validated['price'];
        $product->description = $validated['description'];
        $product->stock = $validated['stock'];
        $product->weight = $validated['weight'];
        $product->length = $validated['length'];
        $product->width = $validated['width'];
        $product->height = $validated['height'];

        if (!$product->save()) {
            return response([
                'message' => "Failed to add product",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Product created successfully",
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->guard('api')->user();

        $product = Product::where('id', $id)
            ->with('variantProducts.variant') // Eager load the related VariantProducts and their Variants
            ->first();

        if (!$product) {
            return response([
                'status' => 404,
                'message' => "Product not found",
            ], 404);
        }

        if ($product->id_user !== $user->id) {
            return response([
                'status' => 403,
                'message' => "Forbidden",
            ], 403);
        }

        $variantData = $product->variantProducts->map(function ($variantProduct) {
            return [
                'id_variant' => $variantProduct->variant->id,
                'name' => $variantProduct->variant->name,
            ];
        })->toArray();

        $product->variants = $variantData;

        unset($product->variantProducts);

        return response([
            'message' => "Product found!",
            'data' => $product,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $user = auth()->guard('api')->user();

        $validated = $request->validated();

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response([
                'status' => 404,
                'message' => "Product not found",
            ], 404);
        }

        if ($product->id_user !== $user->id) {
            return response([
                'status' => 403,
                'message' => "Forbidden",
            ], 403);
        }

        if (!empty($request->file('file'))) {
            $file = $request->file('file');
            $imageName = "public/images/product/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/product'), $imageName);
            $product->image = $imageName;
        }
        if (!empty($request->file('file2'))) {
            $file2 = $request->file('file2');
            $imageName2 = "public/images/product/" . str_replace(' ', '', $file2->getClientOriginalName());
            $file2->move(public_path('images/product'), $imageName2);
            $product->image_2 = $imageName2;
        }
        if (!empty($request->file('file3'))) {
            $file3 = $request->file('file3');
            $imageName3 = "public/images/product/" . str_replace(' ', '', $file3->getClientOriginalName());
            $file3->move(public_path('images/product'), $imageName3);
            $product->image_3 = $imageName3;
        }
        if (!empty($request->file('file4'))) {
            $file4 = $request->file('file4');
            $imageName4 = "public/images/product/" . str_replace(' ', '', $file4->getClientOriginalName());
            $file4->move(public_path('images/product'), $imageName4);
            $product->image_4 = $imageName4;
        }
        if (!empty($request->file('file5'))) {
            $file5 = $request->file('file5');
            $imageName5 = "public/images/product/" . str_replace(' ', '', $file5->getClientOriginalName());
            $file5->move(public_path('images/product'), $imageName5);
            $product->image_5 = $imageName5;
        }

        $product->name = $validated['name'];
        $product->id_category_product = $validated['id_category_product'];
        $product->status = $validated['status'];
        $product->price = $validated['price'];
        $product->description = $validated['description'];
        $product->stock = $validated['stock'];
        $product->weight = $validated['weight'];
        $product->length = $validated['length'];
        $product->width = $validated['width'];
        $product->height = $validated['height'];

        if (!$product->save()) {
            return response([
                'message' => "Failed to edit product",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Product successfully edited",
            'data' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->guard('api')->user();

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response([
                'status' => 404,
                'message' => "Product not found",
            ], 404);
        }

        if ($product->id_user !== $user->id) {
            return response([
                'status' => 403,
                'message' => "Forbidden",
            ], 403);
        }

        VariantProduct::where('id_product', $product->id)->delete();
        $result = Product::destroy($id);

        if (!$result) {
            return response([
                'message' => "Failed to delete product",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Product deleted",
            'data' => $product
        ], 204);
    }

    public function updateVariant(UpdateVariantsProductRequest $request, string $id)
    {
        $user = auth()->guard('api')->user();

        $validated = $request->validated();

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response([
                'status' => 404,
                'message' => "Product not found",
            ], 404);
        }

        if ($product->id_user !== $user->id) {
            return response([
                'status' => 403,
                'message' => "Forbidden",
            ], 403);
        }

        if ($validated['variants']) {
            $variants = $validated['variants'];

            // Load the existing VariantProducts
            $product->load('variantProducts');

            // Delete the existing VariantProducts for the product
            VariantProduct::where('id_product', $product->id)->delete();

            foreach ($variants as $variantName) {
                $searchVariant = Variant::where('name', $variantName)->first();
                if (!$searchVariant) {
                    $newVariant = Variant::create([
                        'name' => $variantName,
                    ]);

                    // Create a new VariantProduct record
                    VariantProduct::create([
                        'id_product' => $product->id,
                        'id_variant' => $newVariant->id,
                    ]);
                } else {
                    // Create a new VariantProduct record with an existing variant
                    VariantProduct::create([
                        'id_product' => $product->id,
                        'id_variant' => $searchVariant->id,
                    ]);
                }
            }
        } else {
            VariantProduct::where('id_product', $product->id)->delete();
        }

        return response([
            'message' => "Product variants successfully edited",
        ], 200);
    }


    public function deleteImage(string $id, string $image)
    {
        $user = auth()->guard('api')->user();

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response([
                'status' => 404,
                'message' => "Product not found",
            ], 404);
        }

        if ($product->id_user !== $user->id) {
            return response([
                'status' => 403,
                'message' => "Forbidden",
            ], 403);
        }

        if ($image == "image_2") {
            $product->image_2 = null;
        } else if ($image == "image_3") {
            $product->image_3 = null;
        } else if ($image == "image_4") {
            $product->image_4 = null;
        } else if ($image == "image_5") {
            $product->image_5 = null;
        } else {
            return response([
                'status' => 404,
                'message' => "Column Image Not Found",
            ], 404);
        }

        if (!$product->save()) {
            return response([
                'status' => 404,
                'message' => "Bad Request",
            ], 400);
        } else {
            return response([
                'status' => 200,
                'message' => "Product image deleted successfully",
            ], 400);
        }
    }
}
