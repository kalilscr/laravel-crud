<?php

namespace Tests\Feature\Listeners;

use App\Events\ChirpCreated;
use App\Listeners\SendChirpCreatedNotifications;
use App\Models\Chirp;
use App\Models\User;
use App\Notifications\NewChirp;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendChirpCreatedNotificationsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_listining_for_chirp_created_event()
    {
        Event::fake();

        Event::assertListening(
            ChirpCreated::class,
            SendChirpCreatedNotifications::class
        );
    }

    public function test_notifications_are_only_sent_to_users_who_follow_author()
    {
        Notification::fake();

        $user = User::factory()->create();

        $followers = User::factory(2)->create();
        $nonFollower = User::factory()->create();
        $user->followers()->attach($followers->pluck('id'));
        Chirp::factory()
             ->for($user)
             ->create();

        Notification::assertSentTo(
            [$followers], NewChirp::class
        );

        Notification::assertNotSentTo(
            [$user, $nonFollower], NewChirp::class
        );
    }
}
