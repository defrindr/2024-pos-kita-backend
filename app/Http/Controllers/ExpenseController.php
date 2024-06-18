<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function add(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(), [
            'id_fund_type' => 'required',
            'notes' => 'required',
            'nominal' => 'required | numeric | between:1, 999999999999999',
            'date' => 'required | date',
        ]);

        $expense = new Expense();
        $expense->id_user = $user->id;
        $expense->id_fund_type = $request->input('id_fund_type');
        $expense->notes = $request->input('notes');
        $expense->nominal = $request->input('nominal');
        $expense->date = $request->input('date');


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $result = $expense->save();
        if ($result) {
            return response([
                'message' => "Expense added",
                'data' => $expense
            ], 201);
        } else {
            return response([
                'message' => "Failed to add expense",
                'data' => null
            ], 400);
        }
    }

    public function show(Request $request)
    {
        $user = auth()->guard('api')->user();
        $expense = Expense::all()->find($request->id);

        $result = $expense;

        if ($result) {
            return response([
                'message' => "success",
                'data' => $expense
            ], 200);
        } else {
            return response([
                'message' => "There is no expense with that id",
                'data' => null
            ], 400);
        }
    }

    public function showAll(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(), [
            'id_user' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $expense = Expense::where('id_user', $request->input('id_user'))->get();
        if ($expense->isEmpty()) {
            return response([
                'message' => "There are no expenses",
                'data' => null
            ], 400);
        } else {
            return response([
                'message' => "success",
                'data' => $expense
            ], 200);
        }
    }

    public function edit(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(), [
            'id_fund_type' => 'required',
            'notes' => 'required',
            'nominal' => 'required | numeric | between:1, 999999999999999',
            'date' => 'required | date',
        ]);
        $expense = Expense::all()->find($request->id);
        if ($expense == null) {
            return response([
                'message' => "There is no expense with that id",
                'data' => null
            ], 400);
        }
        $expense->id_fund_type = $request->input('id_fund_type');
        $expense->notes = $request->input('notes');
        $expense->nominal = $request->input('nominal');
        $expense->date = $request->input('date');

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $result = $expense->save();
        if ($result) {
            return response([
                'message' => "Expense edited",
                'data' => $expense
            ], 200);
        } else {
            return response([
                'message' => "Failed to edit expense",
                'data' => null
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        $user = auth()->guard('api')->user();
        $expense = Expense::find($request->id);
        $result = Expense::destroy($request->id);

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

    public function filter(Request $request)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'start_date' => 'required | date',
            'end_date' => 'required | date',
        ]);

        $expense = Expense::where('id_user', $user->id)->whereBetween('date', [$request->input('start_date'), $request->input('end_date')])->get();

        if ($expense->isEmpty()) {
            return response([
                'message' => "There are no expenses",
                'data' => null
            ], 400);
        } else {
            return response([
                'message' => "success",
                'data' => $expense
            ], 200);
        }
    }
}
