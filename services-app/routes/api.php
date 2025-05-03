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
});
