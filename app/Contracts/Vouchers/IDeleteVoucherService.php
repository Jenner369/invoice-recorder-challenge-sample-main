<?php
namespace App\Contracts\Vouchers;
use App\Models\User;

interface IDeleteVoucherService
{
    public function deleteVoucher(string $voucherId, User $user): void;
}