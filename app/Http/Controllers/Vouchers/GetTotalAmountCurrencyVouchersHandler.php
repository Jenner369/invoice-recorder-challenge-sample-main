<?php

namespace App\Http\Controllers\Vouchers;

use App\Contracts\Vouchers\IGetVoucherService;
use App\Http\Requests\Vouchers\GetTotalAmountCurrencyVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Response;

class GetTotalAmountCurrencyVouchersHandler
{
    public function __construct(private readonly IGetVoucherService $getVoucherService)
    {
    }

    public function __invoke(GetTotalAmountCurrencyVouchersRequest $request): Response
    {
        $currency = $request->input('currency');
        $user = auth()->user();
        $amounts = $this->getVoucherService->getTotalAmountVouchers($currency, $user);
        return response([
            'data' => $amounts,
        ], 200);
    }
}
