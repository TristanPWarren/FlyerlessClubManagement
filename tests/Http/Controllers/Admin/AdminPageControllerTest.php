<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Http\Controllers\Admin;

use Flyerless\Tests\FlyerlessClubManagement\TestCase;

class AdminPageControllerTest extends TestCase
{

    /** @test */
    public function it_returns_a_403_if_the_permission_is_not_given(){
        $this->revokePermissionTo('flyerless-club-management.admin.view-page');

        $response = $this->get($this->adminUrl('/'));
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_200_if_the_permission_is_given(){
        $this->givePermissionTo('flyerless-club-management.admin.view-page');

        $response = $this->get($this->adminUrl('/'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_the_corret_view(){
        $this->bypassAuthorization();

        $response = $this->get($this->adminUrl('/'));
        $response->assertStatus(200);
        $response->assertViewIs('flyerless-club-management::admin');
    }

}
