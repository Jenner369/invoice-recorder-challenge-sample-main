<?php

namespace App\Http\Controllers\Vouchers\Voucher;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $user = auth()->user();
        $voucher = $this->voucherService->getVoucher($request->route('voucher'), $user);
        return response([
            'data' => VoucherResource::make($voucher),
        ], 200);
    }
}
