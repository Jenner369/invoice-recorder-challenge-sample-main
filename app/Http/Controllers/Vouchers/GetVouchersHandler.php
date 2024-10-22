<?php

namespace App\Http\Controllers\Vouchers;

use App\Contracts\Vouchers\IGetVoucherService;
use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Response;

class GetVouchersHandler
{
    public function __construct(private readonly IGetVoucherService $getVoucherService)
    {
    }

    public function __invoke(GetVouchersRequest $request): Response
    {
        $user = auth()->user();
        $vouchers = $this->getVoucherService->getVouchers(
            $request->query('page'),
            $request->query('paginate'),
            $user,
            $request->getFilters(),
        );

        return response([
            'data' => VoucherResource::collection($vouchers),
        ], 200);
    }
}
