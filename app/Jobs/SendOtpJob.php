<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $identifier;
    protected $code;

    /**
     * Create a new job instance.
     */
    public function __construct(string $identifier, string $code)
    {
        $this->identifier = $identifier;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO: In production, integrate with Twilio (SMS), SendGrid (Email), or WhatsApp API.
        
        $channel = filter_var($this->identifier, FILTER_VALIDATE_EMAIL) ? 'Email' : 'SMS/WhatsApp';

        Log::info("PRODUCTION QUEUE: Sending {$channel} OTP to {$this->identifier}. Code: {$this->code}");
        
        // Simulating API delay
        sleep(1);
    }
}
