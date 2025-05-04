<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {
    Route::post('/user/register', [ApiController::class, 'Userregister'])->name('user.register');
    Route::post('/user/login', [ApiController::class, 'login'])->name('user.login');
    Route::get('/all/user', [ApiController::class, 'allUser'])->name('all.user');
    Route::put('/edit/user/{userId}', [ApiController::class, 'editUser'])->name('edit.user');
    Route::delete('/delete/user/{userId}', [ApiController::class, 'deleteUser'])->name('delete.user');

    Route::post('/create/category', [ApiController::class, 'createCategory'])->name('create.category');
    Route::get('/all/category', [ApiController::class, 'allCategory'])->name('all.category');
    Route::post('/edit/category/{id}', [ApiController::class, 'editCategory'])->name('edit.category');
    Route::delete('/delete/category/{id}', [ApiController::class, 'deleteCategory'])->name('delete.category');

    //product route
    Route::post('/create/product', [ApiController::class, 'createProduct'])->name('create.product');


    //shipping method 
    Route::post('/create/shipping', [ApiController::class, 'createShipping'])->name('create.shipping');

    //payment start here 
    Route::post('/create/payment', [ApiController::class, 'createPayment'])->name('create.payment');

    //order route 
    Route::post('/create/order', [ApiController::class, 'createOrder'])->name('create.order');

    //address route 
    Route::post('/create/address', [ApiController::class, 'createAddress'])->name('create.address');

    //review start 
    Route::post('/create/review', [ApiController::class, 'createReview'])->name('create.review');
});
