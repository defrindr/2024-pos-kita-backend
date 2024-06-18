<?php
namespace App\Http\Controllers\WebCommerce;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\AuthenticateProduct;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WebCommerce\NewsController;
use App\Http\Controllers\WebCommerce\UmkmController;
use App\Http\Controllers\WebCommerce\SearchController;
use App\Http\Controllers\WebCommerce\HighlightController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//POS API

//RegisterAll
Route::post("register-all", [UserController::class, 'register']);
//RegisterWithAuth
Route::get("register-admin", [UserController::class, 'register']);
Route::post("register-admin", [UserController::class, 'register'])->middleware('adminMiddleware'); //Auth
//login
Route::post("login", [UserController::class, 'login']);

Route::middleware(['jwt.auth'])->group(function () {
//Products
    Route::post("product", [ProductController::class, 'add']);
    Route::patch("product/{id}", [ProductController::class, 'edit']);
    Route::delete("product/{id}", [ProductController::class, 'delete']);
    Route::get("product/{id}", [ProductController::class, 'show']);
    Route::post("products", [ProductController::class, 'showAll']);
    Route::post("product/search", [ProductController::class, 'search']);
    Route::post("product/image", [ProductController::class, 'store']);
//Transaction
    Route::post("transaction", [TransactionController::class, 'add']);
    Route::post("transaction/checkout", [TransactionController::class, 'checkout']);
    Route::post("transaction/product", [TransactionController::class, 'addProduct']);
    Route::get("transaction/{id}", [TransactionController::class, 'show']);
    Route::post("transactions", [TransactionController::class, 'showAll']);
//Expense
    Route::post("reports/expense", [ExpenseController::class, 'add']);
    Route::get("reports/expense/{id}", [ExpenseController::class, 'show']);
    Route::post("reports/expenses", [ExpenseController::class, 'showAll']);
//Income
    Route::post("reports/income", [IncomeController::class, 'add']);
    Route::get("reports/income/{id}", [IncomeController::class, 'show']);
    Route::post("reports/incomes", [IncomeController::class, 'showAll']);
});



//WEBCOMMERCE API

//AUTH
Route::middleware(['jwt.auth'])->group(function () {
//Search
    Route::post("umkm/search/name", [SearchController::class, 'name']);
    Route::post("umkm/search/city", [SearchController::class, 'city']);
    Route::post("umkm/search/province", [SearchController::class, 'province']);
    Route::get('umkm/filter', [SearchController::class, 'filter']);

//News
    Route::get("news/{id}", [NewsController::class, 'show']);
    Route::post("all-news", [NewsController::class, 'showAll']);
    Route::get("latest-news", [NewsController::class, 'showLatestNews']);

//Highlight
    Route::get("highlight/{id}", [HighlightController::class, 'show']);
    Route::get("highlights", [HighlightController::class, 'showAll']);
    Route::post("highlight/image", [HighlightController::class, 'store']);

//UMKM
    Route::get("umkm/list", [UmkmController::class, 'listAll']);
    Route::get("umkm/{id}", [UmkmController::class, 'details']);
    Route::get("popular-umkm", [UmkmController::class, 'showPopularUMKM']);

});

//NO AUTH
//Search
Route::post("w/umkm/search/name", [SearchController::class, 'name']);
Route::post("w/umkm/search/city", [SearchController::class, 'city']);
Route::post("w/umkm/search/province", [SearchController::class, 'province']);
Route::get("w/umkm/filter", [SearchController::class, 'filter']);

//News
Route::get("w/news/{id}", [NewsController::class, 'show']);
Route::post("w/all-news", [NewsController::class, 'showAll']);
Route::get("w/latest-news", [NewsController::class, 'showLatestNews']);

//Highlight
Route::get("w/highlight/{id}", [HighlightController::class, 'show']);
Route::get("w/highlights", [HighlightController::class, 'showAll']);
Route::post("w/highlight/image", [HighlightController::class, 'store']);

//UMKM
Route::get("w/umkm/list", [UmkmController::class, 'listAll']);
Route::get("w/umkm/{id}", [UmkmController::class, 'details']);
Route::get("w/popular-umkm", [UmkmController::class, 'showPopularUMKM']);

//Product
Route::get("w/product/{id}", [ProductController::class, 'show']);
Route::post("w/products", [ProductController::class, 'showAll']);
Route::post("w/product/search", [ProductController::class, 'search']);

//untuk halaman detail umkm
Route::post("w/umkm-details", [UmkmController::class, 'fullDetail']);
//untuk halaman detail produk umkm
Route::post("w/product-details", [ProductController::class, 'details']);

//DASHBOARD
Route::middleware(['jwt.auth'])->group(function () {
//Hightlight
    Route::post("highlight", [HighlightController::class, 'add']);
    Route::patch("highlight/{id}", [HighlightController::class, 'edit']);
    Route::delete("highlight/{id}", [HighlightController::class, 'delete']);
//News
    Route::post("news", [NewsController::class, 'add']);
});
