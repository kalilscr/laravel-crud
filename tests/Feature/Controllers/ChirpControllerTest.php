<?php

namespace Tests\Feature\Controllers;

use App\Models\Chirp;
use App\Models\User;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ChirpControllerTest extends TestCase
{
    public function test_user_is_redirected_from_index_if_not_logged_in()
    {
        $response = $this->get(route('chirps.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_returns_200()
    {
        $user = User::factory()->makeOne();

        $this->actingAs($user);

        $response = $this->get(route('chirps.index'));

        $response->assertOk();
    }

    public function test_index_has_chirps()
    {
        $user = User::factory()->create();

        $chirp = Chirp::factory(1)->create();

        $this->actingAs($user);

        $this->get(route('chirps.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Chirps/Index')
                ->has('chirps', 1, fn (Assert $page) => $page
                    ->where('message', $chirp->first()->message)
                    ->etc()
                    ->has('user', fn (Assert $page) => $page
                        ->where('id', $chirp->first()->user_id)
                        ->where('name', $chirp->first()->user->name)
                        ->missing('password')
                    )
                )
            );
    }

    public function test_new_chirp_can_be_created()
    {
        $this->assertDatabaseEmpty('chirps');

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('chirps.store'),['message' => 'test new message']);

        $this->assertDatabaseHas('chirps', [
            'message' => 'test new message',
        ]);

        $response->assertRedirect(route('chirps.index'));
    }

    public function test_new_chirp_must_have_message()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('chirps.store'),[]);

        $response->assertSessionHasErrors([
            'message' => 'The message field is required.'
        ]);
    }

    public function test_new_chirp_message_must_be_string()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('chirps.store'),['message' => 123]);

        $response->assertSessionHasErrors([
            'message' => 'The message field must be a string.'
        ]);
    }

    public function test_new_chirp_message_must_be_less_than_255_characters()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('chirps.store'),['message' => str_repeat('a',256)]);

        $response->assertSessionHasErrors([
            'message' => 'The message field must not be greater than 255 characters.'
        ]);
    }

    public function test_chirp_can_be_updated()
    {
        $chirp = Chirp::factory()->create();

        $this->actingAs($chirp->user);

        $response = $this->patch(route('chirps.update', $chirp),['message' => 'test new message']);

        $this->assertDatabaseHas('chirps', [
            'message' => 'test new message',
        ]);

        $response->assertRedirect(route('chirps.index'));
    }

    public function test_updated_chirp_must_have_message()
    {
        $chirp = Chirp::factory()->create();

        $this->actingAs($chirp->user);

        $response = $this->patch(route('chirps.update',$chirp->id),[]);

        $response->assertSessionHasErrors([
            'message' => 'The message field is required.'
        ]);
    }

    public function test_updated_chirp_message_must_be_string()
    {
        $chirp = Chirp::factory()->create();

        $this->actingAs($chirp->user);

        $response = $this->patch(route('chirps.update',$chirp->id),['message' => 123]);

        $response->assertSessionHasErrors([
            'message' => 'The message field must be a string.'
        ]);
    }

    public function test_updated_chirp_message_must_be_less_than_255_characters()
    {
        $chirp = Chirp::factory()->create();

        $this->actingAs($chirp->user);

        $response = $this->patch(route('chirps.update',$chirp->id),['message' => str_repeat('a',256)]);

        $response->assertSessionHasErrors([
            'message' => 'The message field must not be greater than 255 characters.'
        ]);
    }

    public function test_chirp_cannot_be_updated_by_non_owner()
    {
        $chirp = Chirp::factory()->create();

        $wrongUser = User::factory()->create();

        $this->actingAs($wrongUser);

        $response = $this->patch(route('chirps.update', $chirp),['message' => 'test new message']);

        $response->assertForbidden();
    }

    public function test_chirp_can_be_deleted()
    {
        $chirp = Chirp::factory()->create();

        $this->assertDatabaseCount('Chirps',1);

        $this->actingAs($chirp->user);

        $response = $this->delete(route('chirps.destroy',$chirp));

        $response->assertRedirect(route('chirps.index'));

        $this->assertDatabaseEmpty('chirps');
    }

    public function test_chirp_cannot_be_delete_by_non_owner()
    {
        $chirp = Chirp::factory()->create();

        $this->assertDatabaseCount('Chirps',1);

        $wrongUser = User::factory()->create();

        $this->actingAs($wrongUser);

        $response = $this->delete(route('chirps.destroy',$chirp));

        $response->assertForbidden();

        $this->assertDatabaseCount('Chirps',1);
    }

    public function test_only_chirps_by_people_the_user_follows_are_returned_if_filter_is_true()
    {
        $following = User::factory()
            ->has(Chirp::factory())
            ->create();

        $user = User::factory()
                ->hasAttached($following,[],'following')
                ->create();

        $nonFollowedChirps = Chirp::factory(10)->create();

        $this->actingAs($user)
            ->get(route('chirps.index', ['filter' => 'true']))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Chirps/Index')
                ->has('chirps', 1, fn (Assert $page) => $page
                    ->where('message', $following->chirps->first()->message)
                    ->etc()
                    ->has('user', fn (Assert $page) => $page
                        ->where('id', $following->id)
                        ->where('name', $following->name)
                        ->missing('password')
                        ->missing('email')
                    )
                )
        );
    }

}
