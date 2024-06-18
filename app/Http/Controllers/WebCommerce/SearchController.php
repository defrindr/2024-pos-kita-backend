<?php

namespace App\Http\Controllers\WebCommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function city(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'query' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'message' => "enter a keyword"
            ], 400);
        }

        $cityName = $request->input('query');
        $cityId = City::where('name', 'like', '%'.$cityName.'%')->first()->id;
        $city = User::where('id_city', $cityId)->where('id_role', 2)->get();


        if($city->isEmpty()){
            return response([
                'message' => "There are no UMKM in this city",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $city
            ], 200);
        }
    }

    public function province(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'query' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'message' => "enter a keyword"
            ], 400);
        }

        $provinceName = $request->input('query');
        $provinceId = Province::where('name', 'like', '%'.$provinceName.'%')->first()->id;
        $province = User::where('id_province', $provinceId)->where('id_role', 2)->get();

        if($province->isEmpty()){
            return response([
                'message' => "There are no UMKM in this province",
                'data' => null
            ], 400);
        }
        else{
        return response([
            'message' => "success",
            'data' => $province
        ], 200);
    }
    }


    public function name(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'query' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'message' => "enter a keyword"
            ], 400);
        }

        // $umkm = User::where('umkm_name', 'like', '%'.$request->input('query').'%')->where('id_role', 2)->paginate(20);
        $umkm = User::where('umkm_name', 'like', '%'.$request->input('query').'%')->where('id_role', 2)->get();
        $umkmDetails = $umkm->map(function ($umkms) {
            $city = City::where('id', $umkms->id_city)->first();
            $province = Province::where('id', $umkms->id_province)->first();
            return [
                'id' => $umkms->id,
                'owner_name' => $umkms->owner_name,
                'umkm_name' => $umkms->umkm_name,
                'umkm_description' => $umkms->umkm_description,
                'instagram' => $umkms->instagram,
                'whatsapp' => $umkms->whatsapp,
                'facebook' => $umkms->facebook,
                'umkm_email' => $umkms->umkm_email,
                'address' => $umkms->address,
                'kecamatan' => $umkms->kecamatan,
                'kode_pos' => $umkms->kode_pos,
                'pinpoint' => $umkms->pinpoint,
                'phone_number' => $umkms->phone_number,
                'city' => $city ? $city->name : null,
                'province' => $province ? $province->name : null,
                'umkm_image' => $umkms->umkm_image,
                'slug' => Str::slug($umkms->umkm_name)
            ];
        });
        if($umkm->isEmpty()){
            return response([
                'message' => "UMKM not found",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $umkmDetails,
                // 'pagination' => [
                //     'total' => $umkm->total(),
                //     'per_page' => $umkm->perPage(),
                //     'current_page' => $umkm->currentPage(),
                //     'last_page' => $umkm->lastPage(),
                //     'from' => $umkm->firstItem(),
                //     'to' => $umkm->lastItem(),
                // ],
                // 'full-data' => $umkm
            ], 200);
        }
    }

    public function filter(Request $request)
    {
        \Illuminate\Support\Facades\Log::debug('Request Input:', $request->all());

        $usersQuery = User::query(); // Initialize $usersQuery here

        if ($request->has('province')) {
            $provinceQuery = $request->input('province');

            if (strtolower($provinceQuery) === 'all') {
                $users = User::with('city', 'province')->where('id_role', 2)->get();

                $usersResult = $users->map(function ($user) {
                    $city = City::where('id', $user->id_city)->first();
                    $province = Province::where('id', $user->id_province)->first();

                    return [
                        'id' => $user->id,
                        'id_role' => $user->id_role,
                        'id_province' => $user->id_province,
                        'id_city' => $user->id_city,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                        'owner_name' => $user->owner_name,
                        'umkm_name' => $user->umkm_name,
                        'umkm_description' => $user->umkm_description,
                        'instagram' => $user->instagram,
                        'whatsapp' => $user->whatsapp,
                        'phone_number' => $user->phone_number,
                        'facebook' => $user->facebook,
                        'umkm_email' => $user->umkm_email,
                        'address' => $user->address,
                        'kecamatan' => $user->kecamatan,
                        'kelurahan' => $user->kelurahan,
                        'kode_pos' => $user->kode_pos,
                        'pinpoint' => $user->pinpoint,
                        'umkm_image' => $user->umkm_image,
                        'current_team_id' => $user->current_team_id,
                        'profile_photo_path' => $user->profile_photo_path,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                        'google_id' => $user->google_id,
                        'slug' => Str::slug($user->umkm_name),
                        'profile_photo_url' => $user->profile_photo_url,
                        'city' => $city ? $city->name : null,
                        'province' => $province ? $province->name : null
                    ];
                });

                if ($usersResult->isEmpty()) {
                    return response([
                        'message' => "No matching users found",
                        'data' => []
                    ], 200);
                } else {
                    return response([
                        'message' => "success",
                        'data' => $usersResult,
                    ], 200);
                }
            } else {

                if (strtolower($provinceQuery) !== 'all') {
                    // Process as before for non-'All' queries
                    $decodedProvinces = array_map('urldecode', explode(',', $provinceQuery));

                    // Replace hyphens with spaces in province names
                    $decodedProvinces = array_map(function ($province) {
                        return str_replace('-', ' ', $province);
                    }, $decodedProvinces);

                    $usersQuery->whereIn('id_province', function ($subquery) use ($decodedProvinces) {
                        $subquery->select('id')
                            ->from('user_province')
                            ->whereIn('name', $decodedProvinces);
                    });
                }
            }

        // Continue processing for non-'All' queries
        $users = $usersQuery->with('city', 'province')->where('id_role', 2)->get();

        $usersResult = $users->map(function ($user) {
            $city = City::where('id', $user->id_city)->first();
            $province = Province::where('id', $user->id_province)->first();

            return [
                'id' => $user->id,
                'id_role' => $user->id_role,
                'id_province' => $user->id_province,
                'id_city' => $user->id_city,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                'owner_name' => $user->owner_name,
                'umkm_name' => $user->umkm_name,
                'umkm_description' => $user->umkm_description,
                'instagram' => $user->instagram,
                'whatsapp' => $user->whatsapp,
                'phone_number' => $user->phone_number,
                'facebook' => $user->facebook,
                'umkm_email' => $user->umkm_email,
                'address' => $user->address,
                'kecamatan' => $user->kecamatan,
                'kelurahan' => $user->kelurahan,
                'kode_pos' => $user->kode_pos,
                'pinpoint' => $user->pinpoint,
                'umkm_image' => $user->umkm_image,
                'current_team_id' => $user->current_team_id,
                'profile_photo_path' => $user->profile_photo_path,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'google_id' => $user->google_id,
                'slug' => Str::slug($user->umkm_name),
                'profile_photo_url' => $user->profile_photo_url,
                'city' => $city ? $city->name : null,
                'province' => $province ? $province->name : null
            ];
        });

        if ($usersResult->isEmpty()) {
            return response([
                'message' => "No matching users found",
                'data' => []
            ], 200);
        } else {
            return response([
                'message' => "success",
                'data' => $usersResult,
            ], 200);
        }
    }
}
}
