<?php

namespace App\Http\Controllers\Bsi;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bsi\UMKMStoreRequest;
use App\Http\Requests\Bsi\UMKMUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UMKMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $umkms = User::where('id_role', 2)->orderBy('updated_at', 'desc')->with('group.umkmGroup')->paginate(10);

        return response([
            'message' => "success",
            'data' => $umkms
        ], 200);
    }

    public function indexV2()
    {
        $umkms = User::where('id_role', 2)->orderBy('updated_at', 'desc')->with('group.umkmGroup')->get();

        return response([
            'message' => "success",
            'data' => $umkms
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UMKMStoreRequest $request)
    {
        $validated = $request->validated();

        $umkm = new User();
        $umkm->email = $validated['email'];
        $umkm->id_role = 2;
        $umkm->umkm_name = $validated['umkm_name'];
        $umkm->id_province = $validated['id_province'];
        $umkm->id_city = $validated['id_city'];
        $umkm->kecamatan = $validated['kecamatan'];
        $umkm->kelurahan = $validated['kelurahan'];
        $umkm->kode_pos = $validated['kode_pos'];
        $umkm->instagram = $validated['instagram'];
        $umkm->facebook = $validated['facebook'];
        $umkm->address = $validated['address'];
        $umkm->owner_name = $validated['owner_name'];
        $umkm->nik = $validated['nik'];
        $umkm->phone_number = $validated['phone_number'];
        $umkm->whatsapp = $validated['whatsapp'];
        $umkm->password = Hash::make($validated['password']);

        if ($validated['umkm_image']) {
            $file = $validated['umkm_image'];
            $imageName = "public/images/umkm/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/umkm'), $imageName);
            $umkm->umkm_image = $imageName;
        }

        $umkm->umkm_email = $validated['umkm_email'];

        if (!$umkm->save()) {
            return response([
                'message' => "Failed to create UMKM",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "UMKM created successfully",
            'data' => $umkm
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $umkm = User::where('id_role', 2)->where('id', $id)
            ->with('city')->with('province', 'group.umkmGroup')
            ->first();


        if (!$umkm) {
            return response([
                'status' => 404,
                'message' => "UMKM not found",
            ], 404);
        }

        return response([
            'message' => "success",
            'data' => $umkm,
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
    public function update(UMKMUpdateRequest $request, string $id)
    {
        $umkm = User::where('id_role', 2)->where('id', $id)->first();

        if (!$umkm) {
            return response([
                'status' => 404,
                'message' => "UMKM not found",
            ], 404);
        }

        $validated = $request->validated();

        $umkm->umkm_name = $validated['umkm_name'];
        $umkm->id_province = $validated['id_province'];
        $umkm->id_city = $validated['id_city'];
        $umkm->kecamatan = $validated['kecamatan'];
        $umkm->kelurahan = $validated['kelurahan'];
        $umkm->kode_pos = $validated['kode_pos'];
        $umkm->instagram = $validated['instagram'];
        $umkm->facebook = $validated['facebook'];
        $umkm->address = $validated['address'];
        $umkm->owner_name = $validated['owner_name'];
        $umkm->nik = $validated['nik'];
        $umkm->phone_number = $validated['phone_number'];
        $umkm->whatsapp = $validated['whatsapp'];
        $umkm->umkm_email = $validated['umkm_email'];

        if ($validated['password']) {
            $umkm->password = Hash::make($validated['password']);
        }

        if (!empty($validated['umkm_image'])) {
            $file = $validated['umkm_image'];
            $imageName = "public/images/umkm/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/umkm'), $imageName);
            $umkm->umkm_image = $imageName;
        }

        if (!$umkm->save()) {
            return response([
                'message' => "Failed to edit product",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Product edited successfully",
            'data' => $umkm
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $umkm = User::where('id', $id)->first();

        if (!$umkm) {
            return response([
                'status' => 404,
                'message' => "UMKM not found",
            ], 404);
        }

        $result = User::destroy($id);

        if (!$result) {
            return response([
                'message' => "Failed to delete UMKM",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "UMKM deleted successfully",
            'data' => $umkm
        ], 200);
    }
}
