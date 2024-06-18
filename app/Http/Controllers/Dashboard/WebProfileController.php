<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Dashboard\UpdateWebProfileRequest;

class WebProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response([
                'message' => "Umkm profile not found",
                'data' => null
            ], 404);
        }

        return response([
            'message' => "success",
            'data' => $user
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(UpdateWebProfileRequest $request)
    {
        $user = auth()->guard('api')->user();

        $validated = $request->validated();

        // return $validated;

        if (!empty($validated['file'])) {
            $file = $request->file('file');
            $imageName = "public/images/umkm/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/umkm'), $imageName);
            $user->umkm_image = $imageName;
        }

        $user->umkm_name = $validated['umkm_name'];
        $user->address = $validated['address'];
        $user->id_city = $validated['id_city'];
        $user->kecamatan = $validated['kecamatan'];
        $user->kelurahan = $validated['kelurahan'];
        $user->kode_pos = $validated['kode_pos'];
        $user->phone_number = $validated['phone_number'];
        $user->instagram =  $validated['instagram'];
        $user->whatsapp = $validated['whatsapp'];
        $user->facebook = $validated['facebook'];
        $user->umkm_description = $validated['umkm_description'];

        $result = $user->save();

        if ($result) {
            return response()->json([
                'message' => 'Umkm profile successfully edited',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to edit umkm profile',
                'data' => null
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
