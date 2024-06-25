<?php

namespace App\Http\Controllers\WebCommerce;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Dashboard\CityController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\WebCommerce\NewsController;
use App\Http\Controllers\WebCommerce\UmkmController;
use App\Http\Controllers\Dashboard\ChartJsController;

// Dashboard UMKM
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\WebCommerce\SearchController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\WebProfileController;
use App\Http\Controllers\WebCommerce\HighlightController;
use App\Http\Controllers\Bsi\UMKMController as BsiUMKMController;
use App\Http\Controllers\Bsi\ReportController as BsiReportController;
use App\Http\Controllers\LMS\ModuleController as LMSModuleController;
use App\Http\Controllers\Bsi\ProductController as BsiProductController;
use App\Http\Controllers\Bsi\UMKMCityController as BsiUMKMCityController;
use App\Http\Controllers\Bsi\DashboardController as BsiDashboardController;
use App\Http\Controllers\Bsi\HighlightController as BsiHighlightController;
use App\Http\Controllers\Bsi\UMKMGroupController as BsiUMKMGroupController;
use App\Http\Controllers\LMS\Student\ClassController as LMSClassController;
use App\Http\Controllers\LMS\Student\ForumController as LMSForumController;
use App\Http\Controllers\Bsi\UMKMSearchController as BsiUMKMSearchController;
use App\Http\Controllers\Dashboard\NewsController as DashboardNewsController;
use App\Http\Controllers\Bsi\ReportSearchController as BsiReportSearchController;

// Dashboard BSI
use App\Http\Controllers\Bsi\UMKMProvinceController as BsiUMKMProvinceController;
use App\Http\Controllers\Dashboard\IncomeController as DashboardIncomeController;
use App\Http\Controllers\Dashboard\ExpenseController as DashboardExpenseController;
use App\Http\Controllers\Dashboard\ProductController as DashboardProductController;
use App\Http\Controllers\Bsi\UMKMEvaluationController as BsiUMKMEvaluationController;
use App\Http\Controllers\Dashboard\NewsLabelController as DashboardNewsLabelController;
use App\Http\Controllers\Dashboard\IncomeTypeController as DashboardIncomeTypeController;
use App\Http\Controllers\Dashboard\PaymentTypeController as DashboardPaymentTypeController;
use App\Http\Controllers\Dashboard\TransactionController as DashboardTransactionController;
use App\Http\Controllers\Dashboard\NewsCategoryController as DashboardNewsCategoryController;
use App\Http\Controllers\Bsi\UMKMInfrastructuresController as BsiUMKMInfrastructuresController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\Dashboard\ExpenseSearchController as DashboardExpenseSearchController;

// LMS
use App\Http\Controllers\Dashboard\ProductSearchController as DashboardProductSearchController;
use App\Http\Controllers\Dashboard\ProductVariantController as DashboardProductVariantController;
use App\Http\Controllers\Dashboard\IncomeDashboardController as DashboardIncomeDashboardController;
use App\Http\Controllers\Dashboard\ProductCategoryController as DashboardProductCategoryController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//POS API

//RegisterAll
Route::post("register-all", [UserController::class, 'register']);
Route::post("register-android", [UserController::class, 'registerAndroid']);
//RegisterWithAuth
Route::get("register-admin", [UserController::class, 'register']);
Route::post("register-admin", [UserController::class, 'register'])->middleware('adminMiddleware'); //Auth
//login
Route::post("login", [UserController::class, 'login']);

//ChangePassword
Route::post('change-password', [UserController::class, 'changePassword']);
//ChangeEmail
Route::post('/change-email', [UserController::class, 'changeEmail']);
Route::post('/change-email/verify', [UserController::class, 'verifyToken']);

//ForgotPassword&Reset
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/forgot-password/verify', [UserController::class, 'forgotPasswordVerifyToken']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

Route::middleware(['jwt.auth'])->group(function () {
    //Products
    Route::post("product", [ProductController::class, 'add']);
    Route::patch("product/{id}", [ProductController::class, 'edit']);
    Route::delete("product/{id}", [ProductController::class, 'delete']);
    Route::get("product/{id}", [ProductController::class, 'show']);
    Route::post("products", [ProductController::class, 'showAll']);
    Route::post("product/search", [ProductController::class, 'search']);
    Route::get("product/filter/latest", [ProductController::class, 'showLatestProducts']);
    Route::get("product/filter/oldest", [ProductController::class, 'showOldestProducts']);
    //Transaction
    Route::post("transaction", [TransactionController::class, 'add']);
    Route::post("transaction/checkout", [TransactionController::class, 'checkout']);
    Route::post("transaction/product", [TransactionController::class, 'addProduct']);
    Route::get("transaction/{id}", [TransactionController::class, 'show']);
    Route::post("transactions", [TransactionController::class, 'showAll']);
    Route::post("transactions/filter", [TransactionController::class, 'filter']);
    Route::post("transactions/sync", [TransactionController::class, 'sync']);
    //Expense
    Route::post("reports/expense", [ExpenseController::class, 'add']);
    Route::get("reports/expense/{id}", [ExpenseController::class, 'show']);
    Route::post("reports/expenses", [ExpenseController::class, 'showAll']);
    Route::patch("reports/expense/{id}", [ExpenseController::class, 'edit']);
    Route::delete("reports/expense/{id}", [ExpenseController::class, 'delete']);
    Route::post("reports/expenses/filter", [ExpenseController::class, 'filter']);
    //Income
    Route::post("reports/income", [IncomeController::class, 'add']);
    Route::get("reports/income/{id}", [IncomeController::class, 'show']);
    Route::post("reports/incomes", [IncomeController::class, 'showAll']);
    Route::patch("reports/income/{id}", [IncomeController::class, 'edit']);
    Route::delete("reports/income/{id}", [IncomeController::class, 'delete']);
    Route::post("reports/incomes/filter", [IncomeController::class, 'filter']);

    Route::resource("cashier", CashierController::class)->except('create', 'edit');
});

//LMS API
//AUTH
Route::middleware(['jwt.auth'])->group(function () {
    //Module
    Route::get('lms/module', [LMSModuleController::class, 'showAll']);
    Route::post('lms/topics', [LMSModuleController::class, 'showAllTopic']);
    Route::get('lms/class/active', [LMSClassController::class, 'activeClass']);
    Route::get('lms/class/done', [LMSClassController::class, 'completeClass']);
    Route::post('lms/class/detail', [LMSClassController::class, 'detailClass']);
    Route::get('lms/test/pretest/{id}', [LMSClassController::class, 'preTest']);
    Route::get('lms/test/posttest/{id}', [LMSClassController::class, 'postTest']);
    Route::post('lms/test/pretest', [LMSClassController::class, 'submitPreTest']);
    Route::post('lms/test/posttest', [LMSClassController::class, 'submitPostTest']);
    Route::post('lms/class/posttest', [LMSClassController::class, 'submitPostTest']);
    Route::post('lms/class/progress', [LMSClassController::class, 'updateComplete']);
    Route::post('lms/class/assignment', [LMSClassController::class, 'submitAssignment']);
    Route::get('lms/class/assignment/{id}', [LMSClassController::class, 'getAssignment']);
    Route::get('lms/class/forum/{id}', [LMSForumController::class, 'getForum']);
    Route::post('lms/class/forum', [LMSForumController::class, 'replyForum']);
    Route::delete('lms/class/forum/{id}', [LMSForumController::class, 'deleteForumReply']);
});


//WEBCOMMERCE API

//AUTH
Route::middleware(['jwt.auth'])->group(function () {
    //Search
    Route::post("umkm/search/name", [SearchController::class, 'name']);
    Route::post("umkm/search/city", [SearchController::class, 'city']);
    Route::post("umkm/search/province", [SearchController::class, 'province']);
    Route::get('umkm/filter', [SearchController::class, 'filter']);
    Route::get('umkm/search/cities', [SearchController::class, 'getAllCities']);
    //News
    Route::post("all-news", [NewsController::class, 'showAll']);
    Route::get("latest-news", [NewsController::class, 'showLatestNews']);
    Route::get("oldest-news", [NewsController::class, 'showOldestNews']);

    //Highlight
    Route::get("highlight/{id}", [HighlightController::class, 'show']);
    Route::get("highlights", [HighlightController::class, 'showAll']);
    //UMKM
    // Route::patch("umkm", [UmkmController::class, 'addProfile']);
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
Route::get('w/umkm/search/cities', [SearchController::class, 'getAllCities']);

//News
Route::post("w/new", [NewsController::class, 'add']);
Route::get("w/news/{id}", [NewsController::class, 'show']);
Route::patch("w/news/{id}", [NewsController::class, 'edit']);
Route::delete("w/news/{id}", [NewsController::class, 'delete']);
Route::post("w/news", [NewsController::class, 'showByName']);
Route::post("w/all-news", [NewsController::class, 'showAll']);
Route::get("w/latest-news", [NewsController::class, 'showLatestNews']);
Route::get("w/oldest-news", [NewsController::class, 'showOldestNews']);

//Highlight
Route::get("w/highlight/{id}", [HighlightController::class, 'show']);
Route::get("w/highlights", [HighlightController::class, 'showAll']);

//UMKM
Route::get("w/umkm/list", [UmkmController::class, 'listAll']);
Route::get("w/umkm/{id}", [UmkmController::class, 'details']);
Route::get("w/popular-umkm", [UmkmController::class, 'showPopularUMKM']);

//Checkout
Route::post("w/transaction", [CheckoutController::class, 'add']);
Route::post("w/transaction/checkout", [CheckoutController::class, 'checkout']);
Route::post("w/transaction/product", [CheckoutController::class, 'addProduct']);
Route::get("w/transaction/{id}", [CheckoutController::class, 'show']);
Route::post("w/transactions", [CheckoutController::class, 'showAll']);

//Product
Route::get("w/product/{id}", [ProductController::class, 'show']);
Route::post("w/products", [ProductController::class, 'showAll']);
Route::post("w/product/search", [ProductController::class, 'search']);
Route::get("w/product/filter/latest", [ProductController::class, 'showLatestProducts']);
Route::get("w/product/filter/oldest", [ProductController::class, 'showOldestProducts']);

//untuk halaman detail umkm
Route::post("w/umkm-details", [UmkmController::class, 'fullDetail']);
//untuk halaman detail produk umkm
Route::post("w/product-details", [ProductController::class, 'details']);

// Route::post("news", [NewsController::class, 'add']);
// Route::get("news/{id}", [NewsController::class, 'show']);
// Route::patch("news/{id}", [NewsController::class, 'edit']);
// Route::delete("news/{id}", [NewsController::class, 'delete']);



//DASHBOARD
Route::middleware(['jwt.auth'])->prefix('dashboard/umkm')->group(function () {

    // Chart data
    Route::prefix('chart')->group(function () {
        Route::get('/pendapatanPerBulanSatuTahun', [ChartJsController::class, 'pendapatanPerBulanSatuTahun']);
        Route::get('/pendapatanPerHariSatuMinggu', [ChartJsController::class, 'pendapatanPerHariSatuMinggu']);
        Route::get('/pengeluaranPerBulanSatuTahun', [ChartJsController::class, 'pengeluaranPerBulanSatuTahun']);
        Route::get('/peningkatanPesananPerBulanSatuTahun', [ChartJsController::class, 'peningkatanPesananPerBulanSatuTahun']);
    });

    // Dashboard
    Route::get('/pengeluaran', [DashboardController::class, 'pengeluaran']);
    Route::get('/labaBersih', [DashboardController::class, 'labaBersih']);
    Route::get('/pesananBaru', [DashboardController::class, 'pesananBaru']);
    Route::get('/barangTerjual', [DashboardController::class, 'barangTerjual']);
    Route::get('/itemTerpopuler', [DashboardController::class, 'itemTerpopuler']);
    Route::post('/sortProductByStock', [DashboardController::class, 'sortProductByStock']);
    Route::get('/dashboardMetrics', [DashboardController::class, 'dashboardMetrics']);

    //Web Profile
    Route::get("/profile", [WebProfileController::class, 'index']);
    Route::post("/profile", [WebProfileController::class, 'update']);

    //City
    Route::get("/profile/city/{id}", [CityController::class, 'show']);
    Route::get("/profileCities", [CityController::class, 'index']);

    //Transaction
    Route::get("/transactions", [DashboardTransactionController::class, 'index']);
    Route::get("/transaction/{id}", [DashboardTransactionController::class, 'show']);

    //Payment Types
    Route::get("/transactionPaymentList", [DashboardPaymentTypeController::class, 'index']);
    Route::get("/transactionPaymentList/{id}", [DashboardPaymentTypeController::class, 'show']);

    //Product
    Route::get("/products", [DashboardProductController::class, 'index']);
    Route::post("/product", [DashboardProductController::class, 'store']);
    Route::get("/product/{id}", [DashboardProductController::class, 'show']);
    Route::post("/product/{id}", [DashboardProductController::class, 'update']);
    Route::delete("/product/{id}", [DashboardProductController::class, 'destroy']);

    // Other
    Route::post("/searchProducts", [DashboardProductSearchController::class, 'show']);
    Route::post("/productVariant/{id}", [DashboardProductController::class, 'updateVariant']);
    Route::delete("/productImage/{id}/deleteImage/{image}", [DashboardProductController::class, 'deleteImage']);

    Route::post("/sortByProducts", [DashboardProductSearchController::class, 'sortBy']);


    Route::get("/reports", [ReportController::class, 'index']);

    // Reports
    Route::prefix('/report')->group(function () {
        //Incomes
        Route::get("/dashboardIncomesMetrics", [DashboardIncomeDashboardController::class, 'dashboardMetrics']);
        Route::get("/incomes", [DashboardIncomeController::class, 'index']);
        Route::post("/income", [DashboardIncomeController::class, 'store']);
        Route::get('/income/{id}', [DashboardIncomeController::class, 'show']);
        Route::post('/income/{id}', [DashboardIncomeController::class, 'update']);
        Route::delete('/income/{id}', [DashboardIncomeController::class, 'destroy']);

        // Type Payment Income
        Route::get('/paymentList', [DashboardIncomeTypeController::class, 'index']);

        //Expenses
        Route::get("/expenses/v1", [DashboardExpenseController::class, 'indexV1']);
        Route::get("/expenses", [DashboardExpenseController::class, 'index']);
        Route::post("/expense", [DashboardExpenseController::class, 'store']);
        Route::get('/expense/{id}', [DashboardExpenseController::class, 'show']);
        Route::post('/expense/{id}', [DashboardExpenseController::class, 'update']);
        Route::delete("/expense/{id}", [DashboardExpenseController::class, 'destroy']);

        Route::post("/expensesMonth", [DashboardExpenseSearchController::class, 'show']);
    });

    //News
    Route::get("/news", [DashboardNewsController::class, 'index']);
    Route::post("/news", [DashboardNewsController::class, 'store']);
    Route::post("/news/{id}", [DashboardNewsController::class, 'update']);
    Route::get("/news/{id}", [DashboardNewsController::class, 'show']);


    // Other
    Route::get("/productCategory", [DashboardProductCategoryController::class, 'index']);
    Route::get("/productVariant", [DashboardProductVariantController::class, 'index']);
    Route::get("/newsLabel", [DashboardNewsLabelController::class, 'index']);
    Route::get("/newsCategory", [DashboardNewsCategoryController::class, 'index']);

    //Category
    Route::post("category/product", [CategoryController::class, 'addProductCategory']);
    Route::get("category/products", [CategoryController::class, 'getAllProductCategory']);

    Route::post("category/news", [CategoryController::class, 'addNewsCategory']);
    Route::get("category/news", [CategoryController::class, 'getAllNewsCategory']);

    Route::post("label/news", [CategoryController::class, 'addNewsLabel']);
    Route::get("label/news", [CategoryController::class, 'getAllNewsLabel']);

    //Hightlight
    Route::post("highlight", [HighlightController::class, 'add']);
    Route::patch("highlight/{id}", [HighlightController::class, 'edit']);
    Route::delete("highlight/{id}", [HighlightController::class, 'delete']);
});
Route::middleware(['jwt.auth'])->prefix('dashboard/bsi')->group(function () {

    // Dashboard
    Route::get('/dashboardMetrics', [BsiDashboardController::class, 'dashboardMetrics']);

    // UMKM
    Route::get("/umkms", [BsiUMKMController::class, 'index']);
    Route::get("/umkmsV2", [BsiUMKMController::class, 'indexV2']);
    Route::get("/umkm/{id}", [BsiUMKMController::class, 'show']);
    Route::post("/umkm", [BsiUMKMController::class, 'store']);
    Route::post("/umkm/{id}", [BsiUMKMController::class, 'update']);
    Route::delete("/umkm/{id}", [BsiUMKMController::class, 'destroy']);

    // Other
    Route::post("/searchUmkms", [BsiUMKMSearchController::class, 'show']);
    Route::post("/searchUmkmsByProvince", [BsiUMKMSearchController::class, 'searchByProvince']);
    Route::get("/umkmCities", [BsiUMKMCityController::class, 'index']);
    Route::get("/umkmProvinces", [BsiUMKMProvinceController::class, 'index']);

    // Hightlight
    Route::get("/highlights", [BsiHighlightController::class, 'index']);
    Route::post("/highlight", [BsiHighlightController::class, 'store']);
    Route::get("/highlight/{id}", [BsiHighlightController::class, 'show']);
    Route::post("/highlight/{id}", [BsiHighlightController::class, 'update']);
    Route::delete("/highlight/{id}", [BsiHighlightController::class, 'destroy']);

    // Other
    Route::get("/highlightIncrement/{id}", [BsiHighlightController::class, 'incrementPosition']);
    Route::get("/highlightDecrement/{id}", [BsiHighlightController::class, 'decrementPosition']);
    Route::get("/highlights", [BsiHighlightController::class, 'index']);
    Route::get("/products", [BsiProductController::class, 'index']);

    // UMKM Group
    Route::get("/umkmGroups", [BsiUMKMGroupController::class, 'index']);
    Route::get("/umkmGroup/{id}", [BsiUMKMGroupController::class, 'show']);
    Route::post("/umkmGroup", [BsiUMKMGroupController::class, 'store']);
    Route::post("/umkmGroup/{id}", [BsiUMKMGroupController::class, 'update']);
    Route::delete("/umkmGroup/{id}", [BsiUMKMGroupController::class, 'destroy']);

    // Other
    Route::post("/searchUmkmGroups", [BsiUMKMGroupController::class, 'searchByName']);

    // Reports
    Route::get("/reports", [BsiReportController::class, 'index']);
    Route::get("/report/{id}", [BsiReportController::class, 'show']);

    // Other
    Route::post("/searchReports", [BsiReportSearchController::class, 'searchReports']);
    Route::post("/searchDetailReport/{id}", [BsiReportSearchController::class, 'searchDetailReport']);

    // UMKM Evaluation
    Route::get("/umkmEvaluations", [BsiUMKMEvaluationController::class, 'index']);
    Route::post("/umkmEvaluation", [BsiUMKMEvaluationController::class, 'store']);
    Route::get("/umkmEvaluation/{id}", [BsiUMKMEvaluationController::class, 'show']);
    Route::post("/umkmEvaluation/{id}", [BsiUMKMEvaluationController::class, 'update']);
    Route::delete("/umkmEvaluation/{id}", [BsiUMKMEvaluationController::class, 'destroy']);

    // Other
    Route::post("/searchEvaluation", [BsiUMKMEvaluationController::class, 'searchEvaluations']);
    Route::get("/searchEvaluationsByIdUser/{id}", [BsiUMKMEvaluationController::class, 'searchEvaluationsByIdUser']);
    Route::get("/umkmInfrastructures", [BsiUMKMInfrastructuresController::class, 'index']);
});
