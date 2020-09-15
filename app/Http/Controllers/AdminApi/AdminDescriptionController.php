<?php


namespace Flyerless\FlyerlessClubManagement\Http\Controllers\AdminApi;


use BristolSU\Support\ModuleInstance\Contracts\ModuleInstance;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\FlyerlessClubManagement\Http\Controllers\Controller;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceResolver;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AdminDescriptionController extends Controller
{
    public function index() {
        $this->authorize('admin.club.index');

        return forResource()->first();
    }

    public function description_all() {
        $this->authorize('admin.club.index');
        dd("HELLO");

        return forModuleInstance();
    }

}