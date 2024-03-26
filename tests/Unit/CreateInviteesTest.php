<?php

namespace Tests\Unit;

use App\Jobs\CreateInvitees;
use App\Mail\EventInvitation;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreateInviteesTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleAddsInviteesAndSendsEmails()
    {
        Mail::fake();

        $event = Event::factory()->create();
        $invitees = ['invitee1@example.com', 'invitee2@example.com'];

        $job = new CreateInvitees($invitees, $event);
        $job->handle();

        // Assert that invitees are added to the event
        $this->assertCount(2, $event->invitees);

        // Assert that emails are sent to the invitees
        Mail::assertSent(EventInvitation::class, function ($mail) use ($invitees) {
            return in_array($mail->to[0]['address'], $invitees);
        });
    }
}
