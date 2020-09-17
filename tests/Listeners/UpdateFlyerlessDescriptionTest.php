<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Listeners;

use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionUpdated;
use Flyerless\FlyerlessClubManagement\Jobs\UpdateDescriptionOnFlyerless;
use Flyerless\FlyerlessClubManagement\Listeners\UpdateFlyerlessDescription;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;
use Illuminate\Support\Facades\Bus;

class UpdateFlyerlessDescriptionTest extends TestCase
{

    /** @test */
    public function it_dispatches_a_job(){
        Bus::fake(UpdateDescriptionOnFlyerless::class);

        $description = factory(Description::class)->create();

        $event = new DescriptionUpdated($description);
        $listener = new UpdateFlyerlessDescription();
        $listener->handle($event);

        Bus::assertDispatched(UpdateDescriptionOnFlyerless::class, function(UpdateDescriptionOnFlyerless $job) use ($description) {
            return $job->description->id === $description->id;
        });
    }

}
