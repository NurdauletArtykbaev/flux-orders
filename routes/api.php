<?php

use Illuminate\Support\Facades\Route;
use Nurdaulet\FluxOrders\Http\Controllers\OrderController;
use Nurdaulet\FluxOrders\Http\Controllers\PaymentMethodController;
use Nurdaulet\FluxOrders\Http\Controllers\OrderReviewController;
use Nurdaulet\FluxOrders\Http\Controllers\OrderLordController;
use Nurdaulet\FluxOrders\Http\Controllers\CancelReasonController;

/*   7445*/
Route::prefix('api')->group(function () {
    Route::group(['prefix' => 'methods'], function () {
        Route::get('payment', PaymentMethodController::class);
    });
    Route::get('cancel-reasons', CancelReasonController::class);
    Route::group(['prefix' => 'orders', 'middleware' => ['auth:sanctum']], function () {
        Route::post('', [OrderController::class, 'store']);
        Route::get('', [OrderController::class, 'index']);
        Route::get('not-reviewed', [OrderReviewController::class, 'userNotReviewedAd']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/{id}/review', [OrderReviewController::class, 'store']);
        Route::post('{id}/cancel', [OrderController::class, 'cancelByUser']);
        Route::post('/{id}/review-skip', [OrderReviewController::class, 'skipReview']);
//        Route::get('/{id}/contract', [OrderController::class, 'orderContract']);
//        Route::get('/{id}/contract-seller', [OrderController::class, 'orderContractSeller']);
    });

    Route::group(['prefix' => 'lord', 'middleware' => ['auth:sanctum']], function () {
        Route::get('orders', [OrderLordController::class, 'index']);
        Route::get('orders/{id}', [OrderLordController::class, 'show']);
        Route::post('orders/{id}', [OrderLordController::class, 'acceptOrder']);
        Route::post('orders/{id}/cancel', [OrderLordController::class, 'cancelOrder']);
        Route::post('orders/{id}/issue', [OrderLordController::class, 'issueOrder']);
        Route::post('orders/{id}/verify', [OrderLordController::class, 'issueVerify']);

        Route::post('orders/{id}/return', [OrderLordController::class, 'returnOrder']);
        Route::post('orders/{id}/verify-return', [OrderLordController::class, 'returnVerify']);
    });

});
