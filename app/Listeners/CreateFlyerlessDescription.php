<?php

namespace Flyerless\FlyerlessClubManagement\Listeners;

use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionCreated;
use Flyerless\FlyerlessClubManagement\Jobs\UpdateDescriptionOnFlyerless;

class CreateFlyerlessDescription
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DescriptionCreated  $event
     * @return void
     */
    public function handle(DescriptionCreated $event)
    {
        dispatch(new UpdateDescriptionOnFlyerless($event->description));
    }

}
