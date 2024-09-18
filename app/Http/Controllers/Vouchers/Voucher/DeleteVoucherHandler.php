<?php

namespace App\Http\Controllers\Vouchers\Voucher;
use App\Contracts\Vouchers\IDeleteVoucherService;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class DeleteVoucherHandler
{
    public function __construct(private readonly IDeleteVoucherService $deleteVoucherService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $user = auth()->user();
        $this->deleteVoucherService->deleteVoucher($request->route('voucher'), $user);
        return response([
            'message' => 'Voucher eliminado correctamente',
        ], 200);
    }
}
