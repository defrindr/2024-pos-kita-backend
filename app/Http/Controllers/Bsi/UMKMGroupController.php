<?php

namespace App\Http\Controllers\Bsi;

use App\Models\User;
use App\Models\UMKMGroups;
use Illuminate\Http\Request;
use App\Models\UMKMGroupList;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Bsi\UMKMGroupStoreRequest;
use App\Http\Requests\Bsi\UMKMGroupUpdateRequest;

class UMKMGroupController extends Controller
{
    public function index()
    {
        $umkmGroups = UMKMGroups::orderBy('created_at', 'desc')->paginate(10);

        foreach ($umkmGroups as $umkmGroup) {
            $umkmGroup->umkmGroupLists;
        }

        return response([
            'message' => "success",
            'data' => $umkmGroups
        ], 200);
    }

    public function show(string $id)
    {
        $umkmGroup = UMKMGroups::where('id', $id)->first();

        if (!$umkmGroup) {
            return response([
                'status' => 404,
                'message' => "UMKM Group not found",
            ], 404);
        }

        $umkmGroup->umkmGroupLists;
        return response([
            'message' => "Success",
            'data' => $umkmGroup
        ], 200);
    }

    public function store(UMKMGroupStoreRequest $request)
    {
        $validated = $request->validated();

        $umkmGroup = new UMKMGroups();

        $umkmGroup->name = $validated['name'];
        $umkmGroup->id_province = $validated['id_province'];

        if (!$umkmGroup->save()) {
            return response([
                'message' => "Failed to create UMKM Group",
                'data' => null
            ], 400);
        }

        if (!empty($validated['umkms'])) {
            $umkms = $validated['umkms'];
            $umkms = array_unique($umkms);
            foreach ($umkms as $umkm) {
                if (!User::where('id_role', 2)->where('id', $umkm)->first()) {
                    continue;
                }
                $newUmkmGroupList = UMKMGroupList::create([
                    'id_umkm_group' => $umkmGroup->id,
                    'id_user' => $umkm,
                ]);
                $umkmGroupList[] =  $newUmkmGroupList;
            }
            $umkmGroup['umkm_group_list'] = $umkmGroupList;
        }

        return response([
            'message' => "UMKM Group created successfully",
            'data' => $umkmGroup
        ], 201);
    }

    public function update(UMKMGroupUpdateRequest $request, string $id)
    {
        $validated = $request->validated();

        $umkmGroup = UMKMGroups::where('id', $id)->first();

        if (!$umkmGroup) {
            return response([
                'status' => 404,
                'message' => "UMKM Group not found",
            ], 404);
        }

        $umkmGroup->name = $validated['name'];
        $umkmGroup->id_province = $validated['id_province'];

        if (!$umkmGroup->save()) {
            return response([
                'message' => "Failed to create UMKM Group",
                'data' => null
            ], 400);
        }

        if (!empty($validated['umkms'])) {
            $umkms = $validated['umkms'];
            $umkms = array_unique($umkms);

            UMKMGroupList::where('id_umkm_group', $id)->delete();

            foreach ($umkms as $umkm) {
                if (!User::where('id', $umkm)->first()) {
                    continue;
                }
                $newUmkmGroupList = UMKMGroupList::create([
                    'id_umkm_group' => $umkmGroup->id,
                    'id_user' => $umkm,
                ]);
                $umkmGroupList[] =  $newUmkmGroupList;
            }
            $umkmGroup['umkm_group_list'] = $umkmGroupList;
        }

        return response([
            'message' => "UMKM Group updated successfully",
            'data' => $umkmGroup
        ], 200);
    }

    public function destroy(string $id)
    {
        $umkmGroup = UMKMGroups::where('id', $id)->first();

        if (!$umkmGroup) {
            return response([
                'message' => "There is no UMKM Group with that id",
                'data' => null
            ], 400);
        }

        UMKMGroupList::where('id_umkm_group', $id)->delete();

        $result = UMKMGroups::destroy('id', $id);

        if (!$result) {
            return response([
                'message' => "Failed to delete UMKM Group",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "UMKM Group deleted",
            'data' => $umkmGroup
        ], 200);
    }

    public function searchByName(Request $request)
    {
        $umkmGroups = UMKMGroups::where('name', 'like', '%' . $request->input('query') . '%')->orderBy('created_at', 'desc')->paginate(10);

        foreach ($umkmGroups as $umkmGroup) {
            $umkmGroup->umkmGroupLists;
        }

        return response([
            'message' => "success",
            'query' => $request->input('query'),
            'data' => $umkmGroups
        ], 200);
    }
}
