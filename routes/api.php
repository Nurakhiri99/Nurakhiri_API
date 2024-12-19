<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PHPMailerController;
use App\Http\Controllers\UserController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//posts
Route::apiResource('/nurakhiri', App\Http\Controllers\Api\NurakhiriController::class);
Route::post("send-email", [PHPMailerController::class, "composeEmail"])->name("send-email");

Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index');
    Route::post('users', 'store'); //created data
    Route::get('users/{id}', 'show');
    Route::put('users/{id}', 'update');
    Route::delete('users/{id}', 'destroy');
});