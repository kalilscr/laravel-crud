<?php

namespace Tests\Feature\Listeners;

use App\Events\UserFollowed;
use App\Listeners\SendNewFollowerNotifications;
use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendNewFollowerNotificationsTest extends TestCase
{
    public function test_listener_is_attached_to_event(): void
    {
        Event::fake();

        Event::assertListening(
            UserFollowed::class,
            SendNewFollowerNotifications::class
        );
    }

    public function test_notification_is_sent()
    {
        Notification::fake();

        $user = User::factory()->create();
        $follower = User::factory()->create();

        $event = new UserFollowed($user, $follower);
        $listener = New SendNewFollowerNotifications();

        $listener->handle($event);

        Notification::assertSentTo($user, NewFollower::class);
        Notification::assertCount(1);
    }
}
