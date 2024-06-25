<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function sync(Request $request)
    {
        $user = auth()->user();
        $ids = $request->post('exists', []);

        if (gettype($ids) !== "array") {
            $ids = [$ids];
        }

        $transactions = Transaction::where('id_user', $user->id)->get();
        if ($transactions) {
            foreach ($transactions as $i => $t) {
                $transactions[$i]->items = $t->transactionItems;
                $transactions[$i]->server_id = $t->id;
                $transactions[$i]->sync_status = 3;

                unset($transactions[$i]->transactionItems);
                foreach ($transactions[$i]->items as $i2 => $item) {
                    $transactions[$i]->items[$i2]->product_name = $item->product->name;
                    $transactions[$i]->items[$i2]->id_transaction = $t->id;
                    unset($transactions[$i]->items[$i2]->product);
                    $transactions[$i]->items[$i2]->sync_status = 1;
                    $transactions[$i]->items[$i2]->total_price = $transactions[$i]->items[$i2]->current_price * $transactions[$i]->items[$i2]->quantity;
                }
            }
        }

        return response()->json([
            'message' => 'success',
            'data' => $transactions
        ]);
    }
    public function add(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(), [
            'notes' => 'required',
        ]);

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '1234567890';


        $idStore = $user->id;

        if ($user->id_role == 2) $idStore = $user->current_team_id;

        $transaction = new Transaction();
        $transaction->id_user = $user->id;
        $transaction->id_store = $idStore;
        $transaction->id_payment_type = NULL;
        $transaction->total = 0;
        $transaction->transaction_date = date('Y:m:d');
        $transaction->notes = $request->input('notes');
        $transaction->timestamp = date('H:i:s', time());
        $transaction->order_code = "#" . date('Y') . "-" . str_replace(' ', '', Str::upper(substr(str_shuffle($characters), 0, 3))) . "-" . str_replace(' ', '', Str::upper(substr(str_shuffle($numeric), 0, 3)));
        $transaction->source = 1;

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $result = $transaction->save();
        if ($result) {
            return response([
                'message' => "Transaction added",
                'data' => $transaction
            ], 201);
        } else {
            return response([
                'message' => "Failed to add transaction",
                'data' => null
            ], 400);
        }
    }

    public function show(Request $request)
    {
        $user = auth()->guard('api')->user();
        $transaction = Transaction::find($request->id);

        if (!$transaction) {
            return response([
                'message' => "There is no transaction with that id",
                'data' => null
            ], 400);
        }

        $transactionItem = TransactionItem::where('id_transaction', $transaction->id)->get();

        if ($transaction) {
            return response([
                'message' => "success",
                'data' => [
                    'transaction' => $transaction,
                    'product_list' => $transactionItem
                ]
            ], 200);
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

        $transaction = Transaction::where('id_user', $request->input('id_user'))->get();
        if ($transaction->isEmpty()) {
            return response([
                'message' => "There are no transactions",
                'data' => null
            ], 400);
        } else {
            return response([
                'message' => "success",
                'data' => $transaction
            ], 200);
        }
    }

    public function addProduct(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(), [
            'id_transaction' => 'required',
            'id_product' => 'required',
            'quantity' => 'required | numeric',
        ]);

        $transactionItem = new TransactionItem();
        $transactionItem->id_transaction = $request->input('id_transaction');
        $transactionItem->id_product = $request->input('id_product');
        $transactionItem->quantity = $request->input('quantity');

        $product = Product::where('id', $request->input('id_product'))->first();
        if (!$product) {
            return response([
                'message' => "Product is not exist",
                'data' => null
            ], 400);
        }

        $product->stock = $product->stock - $request->input('quantity');
        $product->save();

        $transactionItem->current_price = $product->price;
        $transactionItem->current_modal = $product->cost;

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $transaction = Transaction::where('id', $request->input('id_transaction'))->first();
        if (!$transaction) {
            return response([
                'message' => "Transaction is not exist",
                'data' => null
            ], 400);
        }
        $addPrice = $transactionItem->current_price;
        $quantity = $transactionItem->quantity;
        $transaction->total = $transaction->total + ($addPrice * $quantity);
        $transaction->save();

        $result = $transactionItem->save();
        if ($result) {
            return response([
                'message' => "Product added to transaction",
                'data' => $transactionItem
            ], 201);
        } else {
            return response([
                'message' => "Failed to add product to transaction",
                'data' => null
            ], 400);
        }
    }

    public function checkout(Request $request)
    {
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(), [
            'id_transaction' => 'required',
            'id_payment_type' => 'required',
            'transaction_date' => 'required | date',
            'notes' => 'required',
            'status' => 'required | numeric',
            'timestamp' => 'required',
        ]);

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '1234567890';

        $transaction = Transaction::all()->find($request->input('id_transaction'));
        $transaction->id_payment_type = $request->input('id_payment_type');
        $transaction->transaction_date = $request->input('transaction_date');
        $transaction->notes = $request->input('notes');
        $transaction->status = $request->input('status');
        $transaction->timestamp = date('H:i:s', time());
        $transaction->order_code = "#" . date('Y') . "-" . str_replace(' ', '', Str::upper(substr(str_shuffle($characters), 0, 3))) . "-" . str_replace(' ', '', Str::upper(substr(str_shuffle($numeric), 0, 3)));

        $transactionItem = TransactionItem::where('id_transaction', $transaction->id)->get();

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $result = $transaction->save();
        if ($result) {
            return response([
                'message' => "Transaction succesfully checked-out",
                'data' => [
                    $transaction, //untuk show item masih belum fix
                    // 'items' => [
                    //     'id_product' => $transactionItem->id_product,
                    //     'quantity' => $transactionItem->quantity
                    // ]
                ]
            ], 200);
        } else {
            return response([
                'message' => "Failed to checkout transaction",
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

        $transaction = Transaction::where('id_user', $user->id)->whereBetween('transaction_date', [$request->input('start_date'), $request->input('end_date')])->get();

        if ($transaction->isEmpty()) {
            return response([
                'message' => "There are no transaction",
                'data' => null
            ], 400);
        } else {
            return response([
                'message' => "success",
                'data' => $transaction
            ], 200);
        }
    }
}
