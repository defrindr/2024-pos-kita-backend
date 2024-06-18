<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function add(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'id_income_type' => 'required',
            'notes' => 'required',
            'nominal' => 'required | numeric | between:1, 999999999999999',
            'date' => 'required | date',
        ]);

        $income = new Income();
        $income->id_user = $user->id;
        $income->id_income_type = $request->input('id_income_type');
        $income->notes = $request->input('notes');
        $income->nominal = $request->input('nominal');
        $income->date = $request->input('date');


        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $result = $income->save();
        if($result){
            return response([
                'message' => "Income added",
                'data' => $income
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to add income",
                'data' => null
            ], 400);
        }
    }

    public function show(Request $request){
        $user = auth()->guard('api')->user();
        $income = Income::all()->find($request->id);

        $result = $income;

        if($result){
            return response([
                'message' => "success",
                'data' => $income
            ], 200);
        }
        else{
            return response([
                'message' => "There is no income with that id",
                'data' => null
            ], 400);
        }

    }

    public function showAll(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'id_user' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $income = Income::where('id_user', $request->input('id_user'))->get();
        if($income->isEmpty()){
            return response([
                'message' => "There are no incomes",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $income
            ], 200);
        }
    }

    public function edit(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'id_income_type' => 'required',
            'notes' => 'required',
            'nominal' => 'required | numeric | between:1, 999999999999999',
            'date' => 'required | date',
        ]);
        $income = Income::all()->find($request->id);
        if($income==null){
            return response([
                'message' => "There is no income with that id",
                'data' => null
            ], 400);
        }
        $income->id_income_type = $request->input('id_income_type');
        $income->notes = $request->input('notes');
        $income->nominal = $request->input('nominal');
        $income->date = $request->input('date');

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $result = $income->save();
        if($result){
            return response([
                'message' => "Income edited",
                'data' => $income
            ], 200);
        }
        else{
            return response([
                'message' => "Failed to edit income",
                'data' => null
            ], 400);
        }

    }

    public function delete(Request $request){
        $user = auth()->guard('api')->user();
        $income = Income::find($request->id);
        $result = Income::destroy($request->id);

        if($result){
            return response([
                'message' => "Income deleted",
                'data' => $income
            ], 200);
        }
        else{
            return response([
                'message' => "Failed to delete income",
                'data' => null
            ], 400);
        }
    }

    public function filter(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'start_date' => 'required | date',
            'end_date' => 'required | date',
        ]);

        $income = Income::where('id_user', $user->id)->whereBetween('date', [$request->input('start_date'), $request->input('end_date')])->get();

        if($income->isEmpty()){
            return response([
                'message' => "There are no incomes",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $income
            ], 200);
        }
    }

}
