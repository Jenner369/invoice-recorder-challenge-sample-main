<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\StoreVoucherRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Jobs\ProcessVoucher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreVouchersHandler
{
    public function __construct()
    {
    }

    public function __invoke(StoreVoucherRequest $request): Response
    {
        $vouchersForProcessing = $request->getFilesForProcessing();

        $user = auth()->user();
        $vouchers = ProcessVoucher::dispatch($vouchersForProcessing, $user);

        return response([
            'message' => "Los vouchers se están procesando. Recibirás una notificación cuando el procesamiento haya finalizado."
        ], 201);
    }
}
