<?php

namespace App\Jobs;

use App\Models\ApplicationInvitation;
use App\Notifications\ApplicationInviteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendApplicantInvitation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public ApplicationInvitation $applicationInvitation ,public string $token )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
            $this->applicationInvitation->notify(new ApplicationInviteNotification($this->applicationInvitation,$this->token));
    }
}
