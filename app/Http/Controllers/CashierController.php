<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashierResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CashierController extends Controller
{
  public function store(Request $req)
  {
    $validator = Validator::make($req->all(), [
      'email' => 'required',
      'password' => 'required',
      'owner_name' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    $creator = auth()->user();

    $payload = $validator->validated();

    $this->bindPayload($payload, $creator);

    $payload['password'] = bcrypt($payload['password']);

    try {
      $cashier = new User();
      $this->bindAttribute($cashier, $payload);
      if ($cashier->save()) {
        return response([
          'message' => "Berhasil menambahkan kasir",
          'data' => null
        ], 201);
      }

      return response([
        'message' => "Gagal menambahkan kasir",
        'data' => null
      ], 400);
    } catch (\Throwable $th) {
      return response([
        'message' => "Terjadi kesalahan saat menambahkan kasir" . $th->getMessage(),
        'data' => null
      ], 500);
    }
  }

  public function update(User $cashier, Request $req)
  {
    $validator = Validator::make($req->all(), [
      'email' => 'required',
      'password' => 'min:6|nullable',
      'owner_name' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => $validator->errors()->first(),
        'data' => null
      ], 400);
    }

    $creator = auth()->user();

    $payload = $validator->validated();

    $this->bindPayload($payload, $creator);

    if ($req->has('password'))
      $payload['password'] = bcrypt($payload['password']);

    try {
      $this->bindAttribute($cashier, $payload);
      if ($cashier->update($payload)) {
        return response([
          'message' => "Berhasil mengubah kasir",
          'data' => null
        ], 201);
      }

      return response([
        'message' => "Gagal menambahkan kasir",
        'data' => null
      ], 400);
    } catch (\Throwable $th) {
      return response([
        'message' => "Terjadi kesalahan saat mengubah kasir",
        'data' => null
      ], 500);
    }
  }

  public function index(Request $req)
  {
    $user = auth()->user();
    $builder = User::where('id_role', 2)
      ->where('current_team_id', $user->id)
      ->orderBy('owner_name', 'asc');

    if ($req->has('keyword')) {
      $keyword = $req->get('keyword');
      $builder->where(function (Builder $query) use ($keyword) {
        $query->where('owner_name', 'like', "%$keyword%");
      });
    }

    $cashiers = $builder->get();

    return response([
      'message' => "success",
      'data' => CashierResource::collection($cashiers)
    ], 200);
  }

  public function show(User $cashier)
  {
    return response([
      'message' => "success",
      'data' => new CashierResource($cashier)
    ], 200);
  }

  public function destroy(User $cashier)
  {
    try {
      $cashier->delete();

      return response([
        'message' => "Berhasil di hapus",
        'data' => null
      ], 200);
    } catch (\Throwable $th) {
      return response([
        'message' => "Terjadi kesalahan saat menghapus kasir",
        'data' => null
      ], 500);
    }
  }

  private function bindPayload(&$payload, $creator)
  {
    $payload = array_merge($payload, [
      "id_role" => 2, // ROLE KASIR
      "id_province" => $creator->id_province,
      "id_province" => $creator->id_province,
      "id_city" => $creator->id_city,
      "current_team_id" => $creator->id,
      // "email" => $creator->email,
      // "password" => $creator->password,
      // "owner_name" => $creator->owner_name,
      "umkm_name" => $creator->umkm_name,
      "umkm_description" => $creator->umkm_description,
      "instagram" => $creator->instagram,
      "whatsapp" => $creator->whatsapp,
      "facebook" => $creator->facebook,
      "address" => $creator->address,
      "phone_number" => $creator->phone_number,
      "umkm_image" => $creator->umkm_image,
    ]);
  }

  private function bindAttribute(&$instance, $payload)
  {
    foreach ($payload as $key => $value) {
      $instance->$key = $value;
    }
  }
}
