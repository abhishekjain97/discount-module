<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\BookingController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// User Routes
Route::post('/user/create-user', [UserController::class, 'store']);
Route::get('/user/view', [UserController::class, 'index']);

// Members Route
Route::post('/member/create-member', [MemberController::class, 'store']);
Route::get('/member/view', [MemberController::class, 'index']);
Route::get('/member/view/{id}', [MemberController::class, 'show']);

// Schedules Route
Route::post('/schedule/create-schedule', [ScheduleController::class, 'store']);
Route::get('/schedule/view', [ScheduleController::class, 'index']);

// Discount Route
Route::post('/discount/create-discount', [DiscountController::class, 'store']);

Route::middleware('throttle:100,1')->group(function () {
    Route::get('/discount/apply-discount', [DiscountController::class, 'apply']);
});

// Booking Route
Route::post('/booking/confirm', [BookingController::class, 'store']);


Route::get('/customers', [CustomerController::class, 'index']);
Route::get('/customers/{id}', [CustomerController::class, 'show']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::put('/customers/{id}', [CustomerController::class, 'update']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);