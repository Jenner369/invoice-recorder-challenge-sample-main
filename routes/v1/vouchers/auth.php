<?php

use App\Http\Controllers\Vouchers\GetTotalAmountCurrencyVouchersHandler;
use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use App\Http\Controllers\Vouchers\Voucher\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\Voucher\GetVoucherHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::post('/', StoreVouchersHandler::class);
        
        Route::get('/total-amount', GetTotalAmountCurrencyVouchersHandler::class);
        Route::get('/{voucher}', GetVoucherHandler::class);
        Route::delete('/{voucher}', DeleteVoucherHandler::class);
    }
);
