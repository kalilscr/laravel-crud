<?php

namespace Tests\Feature\Events;

use App\Events\ChirpCreated;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChirpCreatedtest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_chirp_created_event_is_dispatched()
    {
        Event::fake();

        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post(route('chirps.store'),['message' => 'test new message']);

        Event::assertDispatched(ChirpCreated::class);
    }
}
