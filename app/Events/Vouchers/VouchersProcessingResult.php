<?php
namespace App\Events\Vouchers;


use App\Models\User;
use App\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VouchersProcessingResult 

{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Event for the result of processing vouchers
     * @param array $successfulVouchers
     * @param array $failedVouchers
     */
    public function __construct(
        public readonly array $successfulVouchers,
        public readonly array $failedVouchers,
        public readonly User $user
    ) {
    }
}
