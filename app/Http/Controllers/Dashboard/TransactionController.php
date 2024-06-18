<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->guard('api')->user();
        $transactions = Transaction::where('id_user', $user->id)->orderBy('transaction_date', 'desc')->paginate(10);

        return response([
            'message' => "success",
            'data' => $transactions
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
        $user = auth()->guard('api')->user();
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response([
                'message' => "There is no transaction with that id",
                'data' => null
            ], 400);
        }

        if ($transaction->id_user !== $user->id) {
            return response([
                'message' => "This is not your transaction!",
                'data' => null
            ], 400);
        }

        $transactionItem = TransactionItem::where('id_transaction', $transaction->id)->get();
        $transaction['product_list'] = $transactionItem;

        if ($transaction) {
            return response([
                'message' => "success",
                'data' => $transaction
            ], 200);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
