<?php

namespace Flyerless\FlyerlessClubManagement\Http\Controllers\Admin;

use Flyerless\FlyerlessClubManagement\Http\Controllers\Controller;

class AdminPageController extends Controller
{

    public function index()
    {
        $this->authorize('admin.view-page');

        return view('flyerless-club-management::admin');
    }

}
