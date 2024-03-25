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

class UpdateInvitees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Event $event, private array $invitees)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invitees = $this->invitees;
        $existingInvitees = $this->event->invitees()->pluck('email', 'id')->toArray();
        // Remove invitees that are not in the new list
        if (! empty($existingInvitees)) {
            foreach ($existingInvitees as $id => $email) {
                if (! in_array($email, $invitees)) {
                    $this->event->invitees()->where('id', $id)->delete();
                }
            }
        }
        // Add new invitees to the list of invitees for the event and send them an email
        if (! empty($invitees)) {
            if (count($invitees) !== count(array_unique($invitees))) {
                // Duplicate values exist in the array
                $invitees = array_unique($this->invitees);
            }

            foreach ($invitees as $id => $email) {
                if (in_array($email, $existingInvitees)) {
                    continue;
                }

                $this->event->invitees()->updateOrCreate(['id' => $id], ['email' => $email]);

                Mail::to($email)->send(new EventInvitation($this->event));
            }
        }

    }
}
