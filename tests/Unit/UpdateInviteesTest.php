<?php

namespace Tests\Unit;

use App\Jobs\UpdateInvitees;
use App\Mail\EventInvitation;

use App\Models\Event;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Mail;

use Tests\TestCase;

class UpdateInviteesTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleRemovesInviteesAndSendsEmails()
    {
        Mail::fake();

        $event = Event::factory()->create();
        $event->invitees()->create([
            'email' => 'invitee1@test.com'
        ]);

        $invitees = [
            'invitee2@test.com',
            'invitee3@test.com'
        ];

        $job = new UpdateInvitees($event, $invitees);
        $job->handle();

        // Assert that the removed invitee is deleted from the database
        $this->assertDatabaseMissing('event_invitees', [
            'email' => 'invitee1@test.com'
        ]);

        // Assert that the new invitees are added to the database
        $this->assertDatabaseHas('event_invitees', [
            'email' => 'invitee2@test.com'
        ]);
        $this->assertDatabaseHas('event_invitees', [
            'email' => 'invitee3@test.com'
        ]);

        // Assert that emails are sent to the new invitees
        Mail::assertSent(EventInvitation::class, function ($mail) use ($event) {
            return $mail->hasTo('invitee2@test.com') && $mail->event->id === $event->id;
        });
        Mail::assertSent(EventInvitation::class, function ($mail) use ($event) {
            return $mail->hasTo('invitee3@test.com') && $mail->event->id === $event->id;
        });
    }
}