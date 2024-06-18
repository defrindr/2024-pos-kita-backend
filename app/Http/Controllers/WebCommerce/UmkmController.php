<?php

namespace App\Http\Controllers\WebCommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\City;
use App\Models\Province;
use App\Models\News;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class UmkmController extends Controller
{
    function addProfile(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'kota' => 'required',
            'umkm_name' => 'required',
            'umkm_description' => 'required',
            'instagram' => 'required',
            'phone_number' => 'required',
            'facebook' => 'required',
            'umkm_email' => 'required',
            'address' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'kode_pos' => 'required',
            'pinpoint' => 'nullable',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $file = $request->file('file');
        $imageName = "public/images/umkm/".str_replace(' ', '', $file->getClientOriginalName());
        $file->move(public_path('images/umkm'), $imageName);

        $user->id_city = $request->input('kota');
        $user->umkm_name = $request->input('umkm_name');
        $user->umkm_description = $request->input('umkm_description');
        $user->instagram = $request->input('instagram');
        $user->whatsapp = $request->input('phone_number');
        $user->phone_number = $request->input('phone_number');
        $user->facebook = $request->input('facebook');
        $user->umkm_email = $request->input('facebook');
        $user->address = $request->input('address');
        $user->kecamatan = $request->input('kecamatan');
        $user->kelurahan = $request->input('kelurahan');
        $user->kode_pos = $request->input('kode_pos');
        if(!empty($request->input('pinpoint'))){
            $user->pinpoint = $request->input('pinpoint');
        }
        $user->umkm_image = $imageName;

        $result = $user->save();

        if($result){
            return response()->json([
                'message' => 'Umkm profile added',
                'data' => $user,
            ], 201);
        }
        else{
            return response()->json([
                'message' => 'Failed to add umkm profile',
                'data' => null
            ], 400);
        }
    }

    function editProfile(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'kota' => 'nullable',
            'umkm_name' => 'nullable',
            'umkm_description' => 'nullable',
            'instagram' => 'nullable',
            'phone_number' => 'nullable',
            'facebook' => 'nullable',
            'umkm_email' => 'nullable',
            'address' => 'nullable',
            'kecamatan' => 'nullable',
            'kelurahan' => 'nullable',
            'kode_pos' => 'nullable',
            'pinpoint' => 'nullable',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if(!empty($request->file('file'))){
            $file = $request->file('file');
            $imageName = "public/images/umkm/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/umkm'), $imageName);
            $user->umkm_image = $imageName;
        }

        if(!empty($request->input('kota'))){
            $searchKota = City::where('name', $request->input('kota'))->first();
            $user->id_city = $searchKota->id;
        }

        if(!empty($request->input('umkm_name'))){
            $user->umkm_name = $request->input('umkm_name');
        }

        if(!empty($request->input('umkm_description'))){
            $user->umkm_description = $request->input('umkm_description');
        }

        if(!empty($request->input('instagram'))){
            $user->instagram = $request->input('instagram');
        }

        if(!empty($request->input('phone_number'))){
            $user->whatsapp = $request->input('phone_number');
        }

        if(!empty($request->input('phone_number'))){
            $user->phone_number = $request->input('phone_number');
        }

        if(!empty($request->input('facebook'))){
            $user->facebook = $request->input('facebook');
        }

        if(!empty($request->input('umkm_email'))){
            $user->umkm_email = $request->input('umkm_email');
        }

        if(!empty($request->input('address'))){
            $user->address = $request->input('address');
        }

        if(!empty($request->input('kecamatan'))){
            $user->kecamatan = $request->input('kecamatan');
        }

        if(!empty($request->input('kelurahan'))){
            $user->kelurahan = $request->input('kelurahan');
        }

        if(!empty($request->input('kode_pos'))){
            $user->kode_pos = $request->input('kode_pos');
        }

        if(!empty($request->input('pinpoint'))){
            $user->pinpoint = $request->input('pinpoint');
        }

        $result = $user->save();

        if($result){
            return response()->json([
                'message' => 'Umkm profile successfully edited',
                'data' => $user,
            ], 200);
        }
        else{
            return response()->json([
                'message' => 'Failed to edit umkm profile',
                'data' => null
            ], 400);
        }
    }

    public function getProfile(){
        $user = auth()->guard('api')->user();

        $findCity = $user->id_city;
        $findProvince = $user->id_province;

        $city = City::where('id', $findCity)->first();
        $province = Province::where('id', $findProvince)->first();

        $result = $user;

        if($result){
            return response([
                'message' => "success",
                'data' => $user,
                'user_city' => $city->name,
                'user_province' => $province->name
            ], 200);
        }
        else{
            return response([
                'message' => "Umkm profile not exist",
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

    public function details(Request $request){
        $user = auth()->guard('api')->user();

        $findUser = User::all()->find($request->id);

        $result = $findUser;

        if($result){
            return response([
                'message' => "success",
                'data' => $findUser,
                'slug' => Str::slug($findUser->umkm_name)
            ], 200);
        }
        else{
            return response([
                'message' => "Umkm not found",
                'data' => null
            ], 400);
        }
    }

    public function listAll() {
        $user = auth()->guard('api')->user();

        $users = User::where('id_role', 2)->paginate(20);
        // $users = User::where('id_role', 2)->paginate(request()->all());
        if ($users->isEmpty()) {
            return response([
                'message' => "No stores/users found",
                'data' => []
            ], 200);
        }

        $userDetails = $users->map(function ($user) {
            $city = City::where('id', $user->id_city)->first();
            $province = Province::where('id', $user->id_province)->first();
            return [
                'id' => $user->id,
                'umkm_name' => $user->umkm_name,
                'city' => $city ? $city->name : null,
                'province' => $province ? $province->name : null,
                'umkm_image' => $user->umkm_image,
                'slug' => Str::slug($user->umkm_name)
            ];
        });

        return response([
            'message' => "success",
            'data' => $userDetails,
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'full-data' => $users
        ], 200);
    }

    public function showPopularUMKM(Request $request){
        $user = auth()->guard('api')->user();

        $users = User::where('id_role', 2)->orderBy('created_at', 'DESC')->paginate(10);
        // dd($users);
        if($users->isEmpty()){
            return response([
                'message' => "There are no news",
                'data' => null
            ], 400);
        }

        $userDetails = $users->map(function ($user) {
            $city = City::where('id', $user->id_city)->first();
            $province = Province::where('id', $user->id_province)->first();
            return [
                'umkm_name' => $user->umkm_name,
                'city' => $city ? $city->name : null,
                'province' => $province ? $province->name : null,
                'umkm_image' => $user->umkm_image,
                'slug' => Str::slug($user->umkm_name)
            ];
        });

        return response([
            'message' => "success",
            'data' => $userDetails
        ], 200);
    }

    public function fullDetail(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'query' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'message' => "enter a keyword"
            ]);
        }

        $umkm = User::where('umkm_name',$request->input('query'))->first();
        if($umkm==null){
            return response([
                'message' => "UMKM not found",
                'data' => null
            ], 400);
        }
        $products = Product::where('id_user', $umkm->id)->get();
        $productDetails = $products->map(function ($product) {
            return [
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_stock' => $product->stock,
                'product_image' => $product->image,
                'slug' => Str::slug($product->name)
            ];
        });
        $news = News::where('id_user', $umkm->id)->get();
        $newsDetails = $news->map(function ($map) {
            return [
                'title' => $map->title,
                'content' => $map->content,
                'image' => $map->image,
                'date' => $map->date,
                'slug-title' => Str::slug($map->title),
            ];
        });
        if($umkm){
            return response([
                'message' => "success",
                'data' => [
                    'umkm' => $umkm,
                    'products' => $productDetails,
                    'news' => $newsDetails,
                    'slug' => Str::slug($umkm->umkm_name)
                ]
            ], 200);
        }
    }

}
