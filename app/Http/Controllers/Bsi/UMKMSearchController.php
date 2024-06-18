<?php

namespace App\Http\Controllers\Bsi;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UMKMSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function searchByProvince(Request $request)
    {
        $users = User::where('id_role', 2)->where('id_province', $request->input('id_province'))->orderBy('created_at', 'desc')->get();
        return response([
            'message' => "success",
            'id_province' => $request->input('id_province'),
            'data' => $users
        ], 200);
    }

    public function show(Request $request)
    {
        $users = User::where('id_role', 2)->where('umkm_name', 'like', '%' . $request->input('query') . '%')->orWhere('owner_name', 'like', '%' . $request->input('query') . '%')->orderBy('created_at', 'desc')->paginate(10);
        return response([
            'message' => "success",
            'query' => $request->input('query'),
            'data' => $users
        ], 200);
    }
}
