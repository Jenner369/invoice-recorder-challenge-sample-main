<?php

namespace App\Http\Controllers\Vouchers\Voucher;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $user = auth()->user();
        $this->voucherService->deleteVoucher($request->route('voucher'), $user);
        return response([
            'message' => 'Voucher deleted',
        ], 200);
    }
}
