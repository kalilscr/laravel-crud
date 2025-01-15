<?php

namespace Tests\Feature\Policies;

use App\Models\Chirp;
use App\Models\User;
use Tests\TestCase;

class ChirpPolicyTest extends TestCase
{
    public function test_policy_returns_true_if_chirp_to_be_updated_belongs_to_user()
    {
        $chirp = Chirp::factory()->create();

        $this->assertTrue($chirp->user->can('update', $chirp));
    }

    public function test_policy_returns_false_if_chirp_to_be_updated_does_not_belongs_to_user()
    {
        $user = User::factory()->create();

        $chirp = Chirp::factory()->create();

        $this->assertFalse($user->can('update', $chirp));
    }

    public function test_policy_returns_true_if_chirp_to_be_deleted_belongs_to_user()
    {
        $chirp = Chirp::factory()->create();

        $this->assertTrue($chirp->user->can('delete', $chirp));
    }

    public function test_policy_returns_false_if_chirp_to_be_deleted_does_not_belongs_to_user()
    {
        $user = User::factory()->create();

        $chirp = Chirp::factory()->create();

        $this->assertFalse($user->can('delete', $chirp));
    }
}
