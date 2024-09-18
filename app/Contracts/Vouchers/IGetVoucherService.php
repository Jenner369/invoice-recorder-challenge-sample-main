<?php
namespace App\Contracts\Vouchers;
use App\DTO\CurrencyTotalAmountDTO;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IGetVoucherService
{
    public function getVouchers(int $page, int $paginate, User $user, array $filters): LengthAwarePaginator;
    public function getVoucher(string $voucherId, User $user): Voucher;
    public function getTotalAmountVouchers(string $currency, User $user): CurrencyTotalAmountDTO;
}