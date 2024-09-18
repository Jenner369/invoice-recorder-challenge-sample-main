<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use App\Services\VoucherService;
use App\Services\XmlVoucherService;
use DB;
use Illuminate\Console\Command;
use Log;
use SimpleXMLElement;

class RegularizeExistingVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vouchers:regularize {--batch=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regularize existing vouchers adding missing fields like series, number, voucher_type and currency';

    public function __construct(private readonly XmlVoucherService $xmlService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = $this->option('batch');

        Voucher::query()
            ->whereNull('series')
            ->orWhereNull('number')
            ->orWhereNull('voucher_type')
            ->orWhereNull('currency')
            ->chunk($batchSize, function ($vouchers) {
                $this->processVoucherBatch($vouchers);
            });
        $this->info('Regularization completed.');
        
    }

    private function processVoucherBatch($vouchers)
    {
        DB::beginTransaction();
        try {
            foreach ($vouchers as $voucher) {
                $this->regularizeVoucher($voucher);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error regularizing batch: " . $e->getMessage());
            $this->error("Error processing batch. Check logs for details.");

        }
    }

    private function regularizeVoucher($voucher)
    {
        try {
            $xml = new SimpleXMLElement($voucher->xml_content);
            $xmlData = $this->xmlService->extractDetailsFromXmlContent($xml);
            $voucher->update($xmlData);
            $this->info("Regularized invoice ID: {$voucher->id}");

        } catch (\Exception $e) {
            Log::warning("Could not regularize invoice ID {$voucher->id}: " . $e->getMessage());
            $this->warn("Could not regularize invoice ID {$voucher->id}");

        }
    }
}
