<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->guard('api')->user();

        $expenses = Expense::where('id_user', $user->id)->orderBy('date', 'desc')->paginate(10);

        return response([
            'message' => "success",
            'data' => $expenses
        ], 200);
    }

    public function indexV1()
    {
        $user = auth()->guard('api')->user();

        $expenses = Expense::where('id_user', $user->id)->orderBy('date', 'desc')->get();

        return response([
            'message' => "success",
            'data' => $expenses
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
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(), [
            'notes' => 'required',
            'nominal' => 'required|numeric',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $expense = new Expense();
        $expense->id_user = $user->id;
        $expense->id_fund_type = 1;
        $expense->notes = $request->input('notes');
        $expense->nominal = $request->input('nominal');
        $expense->date = $request->input('date');

        $result = $expense->save();

        if ($result) {
            return response([
                'message' => "Expense added",
                'data' => $expense
            ], 201);
        } else {
            return response([
                'message' => "Failed to add transaction",
                'data' => null
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->guard('api')->user();
        $expense = Expense::find($id);

        if (!$expense) {
            return response([
                'message' => "There is no expense with that id",
                'data' => null
            ], 400);
        }

        if ($expense->id_user !== $user->id) {
            return response([
                'message' => "This is not your expense!",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "success",
            'data' => $expense
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'notes' => 'required',
            'nominal' => 'required|numeric',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $expense = Expense::find($id);

        if (!$expense) {
            return response([
                'message' => "There is no expense with that id",
                'data' => null
            ], 400);
        }

        if ($expense->id_user !== $user->id) {
            return response([
                'message' => "This is not your expense!",
                'data' => null
            ], 400);
        }

        if ($request->has('nominal')) {
            $expense->nominal = $request->input('nominal');
        }
        if ($request->has('date')) {
            $expense->date = $request->input('date');
        }
        if ($request->has('notes')) {
            $expense->notes = $request->input('notes');
        }

        if ($expense->save()) {
            return response([
                'message' => "expense edited successfully",
                'data' => $expense
            ], 200);
        }

        return response([
            'message' => "failed to update expense",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->guard('api')->user();
        $expense = Expense::find($id);
        if (!$expense) {
            return response([
                'message' => "Expense not found",
            ], 400);
        }

        if ($expense->id_user !== $user->id) {
            return response([
                'message' => "Unauthorized",
            ], 400);
        }

        $result = Expense::destroy($id);

        if ($result) {
            return response([
                'message' => "Expense deleted",
                'data' => $expense
            ], 200);
        } else {
            return response([
                'message' => "Failed to delete expense",
                'data' => null
            ], 400);
        }
    }
}
