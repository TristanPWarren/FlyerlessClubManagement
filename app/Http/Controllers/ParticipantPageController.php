<?php

namespace Flyerless\FlyerlessClubManagement\Http\Controllers;

class ParticipantPageController extends Controller
{

    public function index()
    {
        $this->authorize('view-page');
        
        return view('template::participant');
    }
    
}