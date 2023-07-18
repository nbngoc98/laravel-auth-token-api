<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Mail\EmailLogTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class JobSendEmailLogBatch implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Số lần thử lại job
    public $retryAfter = 10; // Khoảng thời gian giữa các lần thử lại (giây)

    protected $emailLog;
    /**
     * Create a new job instance.
     */
    public function __construct($emailLog)
    {
        $this->emailLog = $emailLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }

        Mail::to($this->emailLog)->send(new EmailLogTemplate($this->emailLog));
    }
}
