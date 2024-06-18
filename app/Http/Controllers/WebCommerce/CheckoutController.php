<?php

namespace App\Http\Controllers\WebCommerce;

use App\Models\Product;
use App\Models\Checkout;
use App\Models\PaymentType;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function add(Request $request){

        $transaction = new Transaction();
        $transaction->id_user = NULL;
        $transaction->id_payment_type = NULL;
        $transaction->total = 0;
        $transaction->transaction_date = NULL;
        $transaction->notes = NULL;
        $transaction->timestamp = NULL;
        $transaction->order_code = NULL;
        $transaction->source = 2;

        $result = $transaction->save();
        if($result){
            return response([
                'message' => "Transaction added",
                'data' => $transaction
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to add transaction",
                'data' => null
            ], 400);
        }
    }

    public function show(Request $request){
        $user = auth()->guard('api')->user();
        $transaction = Transaction::find($request->id);

        if(!$transaction){
            return response([
                'message' => "There is no transaction with that id",
                'data' => null
            ], 400);
        }

        $transactionItem = TransactionItem::where('id_transaction', $transaction->id)->get();

        if($transaction){
            return response([
                'message' => "success",
                'data' => [
                    'transaction' => $transaction,
                    'product_list' => $transactionItem
                ]
            ], 200);
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

        $transaction = Transaction::where('id_user', $request->input('id_user'))->get();
        if($transaction->isEmpty()){
            return response([
                'message' => "There are no transactions",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $transaction
            ], 200);
        }
    }

    public function addProduct(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'id_transaction' => 'required',
            'id_product' => 'required',
            'quantity' => 'required | numeric',
        ]);

        $transactionItem = new TransactionItem();
        $transactionItem->id_transaction = $request->input('id_transaction');
        $transactionItem->id_product = $request->input('id_product');
        $transactionItem->quantity = $request->input('quantity');

        $product = Product::where('id', $request->input('id_product'))->first();
        if(!$product){
            return response([
                'message' => "Product is not exist",
                'data' => null
            ], 400);
        }

        $product->stock = $product->stock - $request->input('quantity');
        $product->save();

        $transactionItem->current_price = $product->price;
        $transactionItem->current_modal = $product->cost;

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $transaction = Transaction::where('id', $request->input('id_transaction'))->first();
        if(!$transaction){
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
        if($result){
            return response([
                'message' => "Product added to transaction",
                'data' => $transactionItem
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to add product to transaction",
                'data' => null
            ], 400);
        }
    }

    public function delete(Request $request){
        $user = auth()->guard('api')->user();
        $product = Product::find($request->id);
        $result = Product::destroy($request->id);

        if($result){
            return response([
                'message' => "Product deleted",
                'data' => $product
            ], 200);
        }
        else{
            return response([
                'message' => "Failed to delete product",
                'data' => null
            ], 400);
        }
    }

    public function confirmation(Request $request, $id){
        $transaction = Transaction::find($id);

        if(!$transaction){
            return response()->json(['message' => "Transaction not found"], 404);
        }

        $transaction->status = 1;

        if($transaction->save()){
            return response()->json(['message' => "Transaction status updated successfully"], 200);
        }

        return response()->json(['message' => "Failed to update Transaction status"], 400);
    }

    public function checkout(Request $request){
        $validator = Validator::make($request->all(),[
            'id_transaction' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'pinpoint' => 'required',
            'provinsi' => 'required',
            'kota' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'kode_pos' => 'required',
            'jenis_pengiriman' => 'required',
            'vendor_pengiriman' => 'required',
            'metode_pembayaran' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '1234567890';

        $transaction = Transaction::all()->find($request->input('id_transaction'));
        $searchPaymentType = PaymentType::where('name', $request->input('metode_pembayaran'))->first();
        $transaction->id_payment_type = $searchPaymentType->id;
        $transaction->transaction_date = date('Y:m:d');
        // 0 = belum bayar, 1 = sudah lunas
        $transaction->status = 0;
        $transaction->timestamp = date('H:i:s', time());
        $transaction->order_code = "#".date('Y')."-".str_replace(' ', '', Str::upper(substr(str_shuffle($characters), 0, 3)))."-".str_replace(' ', '', Str::upper(substr(str_shuffle($numeric), 0, 3)));

        $checkout = new Checkout();
        // Assuming Checkout has similar fields to the request
        $checkout->id_transaction = $request->input('id_transaction');
        $checkout->first_name = $request->input('first_name');
        $checkout->last_name = $request->input('last_name');
        $checkout->email = $request->input('email');
        $checkout->phone_number = $request->input('phone_number');
        $checkout->address = $request->input('address');
        $checkout->pinpoint = $request->input('pinpoint');
        $checkout->provinsi = $request->input('provinsi');
        $checkout->kota = $request->input('kota');
        $checkout->kecamatan = $request->input('kecamatan');
        $checkout->kelurahan = $request->input('kelurahan');
        $checkout->kode_pos = $request->input('kode_pos');
        $checkout->jenis_pengiriman = $request->input('jenis_pengiriman');
        $checkout->vendor_pengiriman = $request->input('vendor_pengiriman');
        $checkout->metode_pembayaran = $request->input('metode_pembayaran');

        if($checkout->save()){
            if($transaction->save()){
                return response([
                    'message' => "Checkout and Transaction saved successfully",
                    'data' => $checkout
                ], 201);
            }

            return response([
                'message' => "Failed to save Transaction",
                'data' => null
            ], 400);

         }

         return response([
            'message' => "Failed to save Checkout",
            'data' => null
        ], 400);
    }
}
