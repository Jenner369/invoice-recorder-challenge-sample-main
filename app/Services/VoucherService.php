<?php

namespace App\Services;

use App\Contracts\ICurrencyService;
use App\Contracts\IXmlVoucherService;
use App\Contracts\Vouchers\IDeleteVoucherService;
use App\Contracts\Vouchers\IGetVoucherService;
use App\Contracts\Vouchers\IStoreVoucherService;
use App\DTO\CurrencyTotalAmountDTO;
use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VoucherService implements IGetVoucherService, IDeleteVoucherService, IStoreVoucherService
{

    public function __construct(
        private readonly IXmlVoucherService $xmlVoucherService,
        private readonly ICurrencyService $currencyService
    ) {
    }

    public function getVouchers(int $page, int $paginate, User $user, array $filters): LengthAwarePaginator
    {
        $query = Voucher::query();

        if ($filters['serie']) $query->bySeries($filters['serie']);
        if ($filters['number']) $query->byNumber($filters['number']);
        if ($filters['from']) $query->fromDate($filters['from']);
        if ($filters['to']) $query->toDate($filters['to']);
        $query->byUserId($user->id);

        return $query->paginate(perPage: $paginate, page: $page);
    }

    public function getTotalAmountVouchers(string $currency, User $user): CurrencyTotalAmountDTO
    {
        $totalAmountInBase = 0;
        $totalAmountInCurrency = 0;

        Voucher::query()->selectRaw('sum(total_amount) as total_amount, currency')
            ->byUserId($user->id)
            ->groupBy('currency', 'id')
            ->each(function ($voucher) use (&$totalAmountInCurrency, &$totalAmountInBase, $currency) {
                $totalAmountInCurrency += $this->currencyService->convertToCurrency($voucher->total_amount, $voucher->currency, $currency);
                $totalAmountInBase += $this->currencyService->convertToBaseCurrency($voucher->total_amount, $voucher->currency);
            });

        return new CurrencyTotalAmountDTO($totalAmountInBase, $this->currencyService->getCurrencyBase(), $totalAmountInCurrency, $currency);
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
        $xml = $this->xmlVoucherService->createXmlFromString($xmlContent);
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

    public function deleteVoucher(string $voucherId, User $user): void
    {
        $voucher = Voucher::byUserId($user->id)->findOrFail($voucherId);
        VoucherLine::where('voucher_id', $voucher->id)->delete();
        $voucher->delete();
    }

    public function getVoucher(string $voucherId, User $user): Voucher
    {
        return Voucher::with(['lines', 'user'])->byUserId($user->id)->findOrFail($voucherId);
    }

    /**
     * Process vouchers from files data and content, and return the processed vouchers, specified by the user.
     * The returned array should contain the processed vouchers and failed vouchers (And the reason for failure).
     * @param array $vouchersForProcessing
     * @param \App\Models\User $user
     * @return array
     */
    public function storeVouchersFromFilesDataAndContent(array $vouchersForProcessing, User $user): array
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
