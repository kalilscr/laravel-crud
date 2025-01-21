<?php

namespace Tests\Feature\Controllers;

use App\Events\UserFollowed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FollowControllerTest extends TestCase
{
    public function test_users_can_follow_each_other()
    {
        $user = User::factory()->create();

        $following = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('follow.store'),['id' => $following->id]);

        $response->assertRedirect();

        $this->assertContains($following->id, $user->following->pluck('id'));
    }

    public function test_user_followed_event_is_dispatched()
    {
        Event::fake();

        $user = User::factory()->create();

        $following = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(route('follow.store'),['id' => $following->id]);

        Event::assertDispatched(function (UserFollowed $event) use ($user) {
            return $event->follower->id === $user->id;
        });
    }

    public function test_user_must_be_logged_in_before_following()
    {
        $following = User::factory()->create();

        $response = $this->post(route('follow.store'),['id' => $following->id]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_must_be_verified_before_following()
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $following = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('follow.store'),['id' => $following->id]);

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_id_required()
    {
        $response = $this
            ->actingAs(User::factory()->make())
            ->post(route('follow.store'));

        $response->assertSessionHasErrors([
            'id' => 'The id field is required.'
        ]);
    }

    public function test_id_must_be_a_number()
    {
        $response = $this
            ->actingAs(User::factory()->make())
            ->post(route('follow.store'),['id' => 'a']);

        $response->assertSessionHasErrors([
            'id' => 'The id field must be an integer.',
            'id' => 'The id field must be a number.'
        ]);
    }

    public function test_id_must_not_be_request_user()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('follow.store'),['id' => $user->id]);

        $response->assertSessionHasErrors([
            'id' => 'The selected id is invalid.',
        ]);
    }

    public function test_id_must_belong_to_existing_user()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('follow.store'),['id' => 999]);

        $response->assertSessionHasErrors([
            'id' => 'The selected id is invalid.',
        ]);
    }

    public function test_users_can_unfollow_each_other()
    {
        $user = User::factory()->create();

        $following = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('follow.destroy',$following->id));

        $response->assertRedirect();

        $this->assertNotContains($following->id, $user->following->pluck('id'));
    }

    public function test_user_must_be_logged_in_to_unfollow()
    {
        $following = User::factory()->create();

        $response = $this->delete(route('follow.destroy', 2));

        $response->assertRedirect(route('login'));
    }

    public function test_user_must_be_verified_to_unfollow()
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('follow.destroy', 2));

        $response->assertRedirect(route('verification.notice'));
    }
}
