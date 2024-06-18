<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
Use App\Http\Controllers\MailController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

//Logout
Route::get('logout', [UserController::class, 'logout']);

//Google Login
Route::get('authorized/google', [UserController::class, 'redirectToGoogle']);
Route::get('authorized/google/callback', [UserController::class, 'handleGoogleCallback']);


//Mail
Route::get('/email', function () {
    Mail::to('jovan3duardo@gmail.com')->send(new App\Mail\OTPMail());
    return new App\Mail\OTPMail();
});

Route::get('sendmail', [MailController::class, 'index']);
