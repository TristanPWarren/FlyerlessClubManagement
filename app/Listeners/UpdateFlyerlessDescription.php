<?php

namespace Flyerless\FlyerlessClubManagement\Listeners;

use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionUpdated;
use Flyerless\FlyerlessClubManagement\Jobs\UpdateDescriptionOnFlyerless;

class UpdateFlyerlessDescription
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
     * @param DescriptionUpdated $event
     * @return void
     */
    public function handle(DescriptionUpdated $event)
    {
        dispatch(new UpdateDescriptionOnFlyerless($event->description));
    }

}
