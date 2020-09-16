<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Listeners;

use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionCreated;
use Flyerless\FlyerlessClubManagement\Jobs\UpdateDescriptionOnFlyerless;
use Flyerless\FlyerlessClubManagement\Listeners\CreateFlyerlessDescription;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;
use Illuminate\Support\Facades\Bus;

class CreateFlyerlessDescriptionTest extends TestCase
{

    /** @test */
    public function it_dispatches_a_job(){
        Bus::fake(UpdateDescriptionOnFlyerless::class);

        $description = factory(Description::class)->create();

        $event = new DescriptionCreated($description);
        $listener = new CreateFlyerlessDescription();
        $listener->handle($event);

        Bus::assertDispatched(UpdateDescriptionOnFlyerless::class, function(UpdateDescriptionOnFlyerless $job) use ($description) {
            return $job->description->id === $description->id;
        });
    }

}
