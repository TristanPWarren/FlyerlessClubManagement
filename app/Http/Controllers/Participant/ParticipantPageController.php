<?php

namespace Flyerless\FlyerlessClubManagement\Http\Controllers\Participant;

use BristolSU\Module\UploadFile\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ParticipantPageController extends Controller
{

    public function index()
    {
        $this->authorize('view-page');
        
        return view('flyerless-club-management::participant');
    }
    
}