<?php
namespace App\Contracts\Vouchers;
use App\Models\User;
use App\Models\Voucher;

interface IStoreVoucherService
{
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array;
    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher;
    public function storeVouchersFromFilesDataAndContent(array $vouchersForProcessing, User $user): array;

}