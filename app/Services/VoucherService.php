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
        if (
            Voucher::duplicate(
                $data["issuer_document_number"],
                $data["issuer_document_type"],
                $data["total_amount"],
                $data["series"],
                $data["number"],
                $data["voucher_type"]
            )->exists()
        ) {
            throw new \Exception("Voucher " . $data["series"] . "-" . $data["number"] . " ya se ha registrado anteriormente");
        }

        $voucher = new Voucher([
            ...$data,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
        ]);
        if (!$voucher->save())
            throw new \Exception('No se pudo guardar el voucher ' . $voucher->series . '-' . $voucher->number);

        $this->xmlVoucherService->processVoucherLinesFromXmlContent($xml, function ($lineData) use ($voucher) {
            $voucherLine = new VoucherLine([
                ...$lineData,
                'voucher_id' => $voucher->id,
            ]);
            if (!$voucherLine->save())
                throw new \Exception('Ocurrio un error al intentar guardar las lineas/detalles del voucher ' . $voucher->series . '-' . $voucher->number);
        });

        return $voucher;
    }


    /**
     * Process vouchers from files data and content, and return the processed vouchers, specified by the user.
     * The returned array should contain the processed vouchers and failed vouchers (And the reason for failure).
     * @param array $vouchersForProcessing
     * @param \App\Models\User $user
     * @return array
     */
    public function processVouchersFromFilesDataAndContent(array $vouchersForProcessing, User $user): array
    {
        $processedVouchers = [];
        $failedVouchers = [];
        foreach ($vouchersForProcessing as $voucherForProcessing) {
            try {
                $voucher = $this->storeVoucherFromXmlContent($voucherForProcessing['content'], $user);
                $processedVouchers[] = $voucher;
            } catch (\Exception $e) {
                $failedVouchers[] = [
                    'filename' => $voucherForProcessing['filename'],
                    'reason' => $e->getMessage(),
                ];
            }
        }
        return [
            'processed' => $processedVouchers,
            'failed' => $failedVouchers,
        ];
    }

}
