<?php

namespace App\Services;

use App\DTO\CurrencyTotalAmountDTO;
use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{

    public function __construct(
        private readonly XmlVoucherService $xmlVoucherService
    ) {
    }

    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }
        VouchersCreated::dispatch($vouchers, $user);
        
        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $data = $this->xmlVoucherService->getVoucherDataFromXml($xml);
        $voucher = new Voucher([
            ...$data,
            'xml_content' => $xmlContent,
            'user_id' => $user->id, 
        ]);
        $voucher->save();

        $this->xmlVoucherService->processVoucherLinesFromXmlContent($xml, function ($lineData) use ($voucher) {
            $voucherLine = new VoucherLine([
                ...$lineData,
                'voucher_id' => $voucher->id,
            ]);
            $voucherLine->save();
        });

        return $voucher;
    }

}
