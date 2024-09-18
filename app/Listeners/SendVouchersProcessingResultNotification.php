<?php

namespace App\Listeners;

use App\Events\Vouchers\VouchersProcessingResult;
use App\Mail\VouchersProcessingResultMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendVouchersProcessingResultNotification implements ShouldQueue
{
    public function handle(VouchersProcessingResult $event): void
    {
        $mail = new VouchersProcessingResultMail($event->successfulVouchers, $event->failedVouchers, $event->user);
        Mail::to($event->user->email)->send($mail);
    }
}
