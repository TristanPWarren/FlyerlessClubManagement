<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Http\Controllers\Participant;

use Flyerless\Tests\FlyerlessClubManagement\TestCase;

class ParticipantPageControllerTest extends TestCase
{

    /** @test */
    public function it_returns_a_403_if_the_permission_is_not_given(){
        $this->revokePermissionTo('flyerless-club-management.view-page');

        $response = $this->get($this->userUrl('/'));
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_200_if_the_permission_is_given(){
        $this->givePermissionTo('flyerless-club-management.view-page');

        $response = $this->get($this->userUrl('/'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_the_corret_view(){
        $this->bypassAuthorization();

        $response = $this->get($this->userUrl('/'));
        $response->assertStatus(200);
        $response->assertViewIs('flyerless-club-management::participant');
    }

}
