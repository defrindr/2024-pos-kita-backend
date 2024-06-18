<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Dashboard\StoreIncomeRequest;
use App\Http\Requests\Dashboard\UpdateIncomeRequest;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $datas = Cache::remember('incomes', 60, function () {
                $user = auth()->guard('api')->user();

                $incomes = Income::where('id_user', $user->id)->orderBy('date', 'desc')->paginate(10);

                return $incomes;
            });

            return response()->json([
                'message' => "success",
                'data' => $datas
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => "Internal Server Error : " . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncomeRequest $request)
    {
        try {
            $user = auth()->guard('api')->user();

            $validated = $request->validated();

            $income = new Income();
            $income->id_user = $user->id;
            $income->id_income_type = $validated['id_income_type'];
            $income->notes = $validated['notes'];
            $income->nominal = $validated['nominal'];
            $income->date = $validated['date'];

            $income->save();

            return response([
                'status' => "success",
                'message' => "data inserted successfuly",
                'data' => $income
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => "Internal Server Error : " . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = auth()->guard('api')->user();

            $income = Income::where('id_user', $user->id)->where('id', $id)->first();

            if (!$income) {
                return response()->json([
                    'status' => "fail",
                    'message' => "Data not found",
                    'data' => null
                ], 404);
            }

            return response([
                'message' => "success",
                'data' => $income
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => "Internal Server Error : " . $th->getMessage(),
                'data' => null
            ], 500);
        }

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
    public function update(UpdateIncomeRequest $request, string $id)
    {
        try {
            $user = auth()->guard('api')->user();

            $validated = $request->validated();

            $income = Income::where('id_user', $user->id)->where('id', $id)->first();

            if (!$income) {
                return response()->json([
                    'status' => "fail",
                    'message' => "Data not found",
                    'data' => null
                ], 404);
            }

            $income->id_income_type = $validated['id_income_type'];
            $income->notes = $validated['notes'];
            $income->nominal = $validated['nominal'];
            $income->date = $validated['date'];

            $income->save();

            return response()->json([
                'status' => "success",
                'data' => $income
            ], 200);


        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => "Internal Server Error : " . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = auth()->guard('api')->user();

            $result = Income::where('id_user', $user->id)->where('id', $id)->delete();

            if (!$result) {
                return response()->json([
                    'status' => "fail",
                    'message' => "Data not found",
                ], 404);
            }

            return response()->json([
                'status' => "success",
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => "Internal Server Error : " . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
