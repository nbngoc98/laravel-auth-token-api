<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\EmailLogTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class JobSendEmailLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Số lần thử lại job
    public $retryAfter = 10; // Khoảng thời gian giữa các lần thử lại (giây)

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

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
        Mail::to($this->emailLog)->send(new EmailLogTemplate($this->emailLog));

        //đánh dấu công việc là "thất bại" theo cách thủ công
        // $this->fail();  

        // giải phóng thủ công một công việc trở lại hàng đợi để có thể thử lại công việc đó sau
        // $this->release();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed()
    {
        // Called when the job is failing...
    }
}
