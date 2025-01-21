<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewFollowerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_mail_contents(): void
    {
       Notification::fake();

       $user = User::factory()->create();
       $follower = User::factory()->create();

        $user->notify(new NewFollower($follower));

        Notification::assertSentTo($user, NewFollower::class, function ($notification) use ($user, $follower){
            $mailNotification = $notification->toMail($user);

            $this->assertEquals("{$follower->name} started following you", $mailNotification->greeting);
            $this->assertEquals('View their profile', $mailNotification->actionText);
            $this->assertEquals(route('profile.show', $follower->id),$mailNotification->actionUrl);

            return true;
        });
    }
}
