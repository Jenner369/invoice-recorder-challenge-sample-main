<?php

namespace App\Jobs;

use App\Events\Vouchers\VouchersProcessingResult;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVoucher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vouchersForProcessing;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(array $vouchersForProcessing, User $user)
    {
        $this->vouchersForProcessing = $vouchersForProcessing;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(VoucherService $voucherService): void
    {
        ['processed' => $vouchersProcessed, 'failed' => $vouchersFailed] = $voucherService->processVouchersFromFilesDataAndContent($this->vouchersForProcessing, $this->user);
        
        event(new VouchersProcessingResult($vouchersProcessed, $vouchersFailed, $this->user));
    }
}
