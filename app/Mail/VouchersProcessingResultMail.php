<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VouchersProcessingResultMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $vouchersSuccess;
    public array $vouchersFailed;
    public User $user;

    public function __construct(array $vouchersSuccess, array $vouchersFailed, User $user)
    {
        $this->vouchersSuccess = $vouchersSuccess;
        $this->vouchersFailed = $vouchersFailed;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->view('emails.vouchers-processing-result')
            ->with(['vouchersSuccess' => $this->vouchersSuccess, 'vouchersFailed' => $this->vouchersFailed, 'user' => $this->user]);
    }
}
