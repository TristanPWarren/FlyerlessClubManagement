<?php

namespace Flyerless\FlyerlessClubManagement\Http\Controllers\Participant;

use Flyerless\FlyerlessClubManagement\Http\Controllers\Controller;

class ParticipantPageController extends Controller
{

    public function index()
    {
        $this->authorize('view-page');

        return view('flyerless-club-management::participant');
    }

}
