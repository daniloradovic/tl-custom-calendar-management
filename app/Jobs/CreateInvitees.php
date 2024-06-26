<?php

namespace App\Jobs;

use App\Mail\EventInvitation;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CreateInvitees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $invitees, private Event $event)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Add new invitees to the list of invitees for the event and send them an email
        foreach ($this->invitees as $invitee) {
            $this->event->invitees()->create(['email' => $invitee]);

            Mail::to($invitee)->send(new EventInvitation($this->event));
        }
    }
}
