<?php

namespace Tests\Feature\Controllers;

use App\Events\UserFollowed;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FollowerControllerTest extends TestCase
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

        $response = $this->actingAs($following)->post(route('follow.store'),['id' => $following->id]);

        $response->assertRedirect(session()->previousUrl());
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

        $response = $this->actingAs($following)->delete(route('follow.destroy', 2));

        $response->assertRedirect(session()->previousUrl());
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

    public function test_users_can_see_who_each_other_are_following()
    {
        $user = User::factory()
            ->has(User::factory(3),'following')
            ->create();

        $otherUser = User::factory()
            ->create();

        $this->actingAs($otherUser)
            ->get(route('follow.index', $user->id))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Follow/Index')
                ->has('user', fn (Assert $page) => $page
                    ->where('id', $user->id)
                    ->where('name', $user->name)
                    ->missing('password')
                    ->missing('email')
                )
                ->has('users', 3, fn (Assert $page) => $page
                    ->where('user_id', $user->following()->first()->id)
                    ->where('name', $user->following()->first()->name)
                    ->where('following', false)
                    )
            );
    }

    public function test_follow_property_depends_on_auth_user()
    {
        $following = User::factory();

        $user = User::factory()
            ->has($following,'following')
            ->create();

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->get(route('follow.index', $user->id))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Follow/Index')
                ->has('users', 1, fn (Assert $page) => $page
                    ->where('user_id', $user->following()->first()->id)
                    ->where('name', $user->following()->first()->name)
                    ->where('following', false)
                    )
            );

        $this->actingAs($user)
            ->get(route('follow.index', $user->id))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Follow/Index')
                ->has('users', 1, fn (Assert $page) => $page
                    ->where('user_id', $user->following()->first()->id)
                    ->where('name', $user->following()->first()->name)
                    ->where('following', true)
                    )
            );

    }

    public function test_must_be_logged_in_to_view_following_index()
    {
        $user = User::factory()
            ->create();

        $response = $this->actingAs($user)->get(route('follow.index', 1));

        $response->assertStatus(200);
    }

    public function test_must_be_verified_to_view_following_index()
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $response = $this->actingAs($user)
            ->get(route('follow.index', 1));

        $response->assertRedirect(route('verification.notice'));
    }
//////////////////////////////////////////////////////////////////////////////////////////
    public function test_users_can_see_who_is_following_a_particular_user()
    {
        $user = User::factory()
            ->has(User::factory(3),'followers')
            ->create();

        $otherUser = User::factory()
            ->create();

        $this->actingAs($otherUser)
            ->get(route('followers', $user->id))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Follow/Index')
                ->has('user', fn (Assert $page) => $page
                    ->where('id', $user->id)
                    ->where('name', $user->name)
                    ->missing('password')
                    ->missing('email')
                )
                ->has('users', 3, fn (Assert $page) => $page
                    ->where('id', $user->followers()->first()->id)
                    ->where('name', $user->followers()->first()->name)
                    ->where('following', null)
                    )
            );
    }

    public function test_follow_property_depends_on_user_authenticated()
    {
        $follower = User::factory()->create();

        $user = User::factory()->create();

        $user->following()->attach($follower);
        $user->followers()->attach($follower);

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->get(route('followers', $user->id))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Follow/Index')
                ->has('users', 1, fn (Assert $page) => $page
                    ->where('id', $user->followers()->first()->id)
                    ->where('name', $user->followers()->first()->name)
                    ->where('following', null)
                    )
            );

        $this->actingAs($user)
            ->get(route('followers', $user->id))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Follow/Index')
                ->has('users', 1, fn (Assert $page) => $page
                    ->where('id', $user->followers()->first()->id)
                    ->where('name', $user->followers()->first()->name)
                    ->whereNot('following', null)
                    )
            );

    }

    public function test_must_be_logged_in_to_view_following_index_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('follow.index', 1));

        $response->assertStatus(200);
    }

    public function test_must_be_verified_to_view_following_index_page()
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $response = $this->actingAs($user)
            ->get(route('follow.index', 1));

        $response->assertRedirect(route('verification.notice'));
    }
}
