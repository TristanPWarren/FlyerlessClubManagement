<?php

namespace Flyerless\FlyerlessClubManagement\Http\Controllers\Admin;

use BristolSU\Module\UploadFile\Http\Controllers\Controller;

class AdminPageController extends Controller
{
    
    public function index()
    {
        $this->authorize('admin.view-page');
        
        return view('flyerless-club-management::admin');
    }
    
}