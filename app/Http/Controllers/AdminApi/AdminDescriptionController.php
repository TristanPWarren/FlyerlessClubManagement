<?php


namespace Flyerless\FlyerlessClubManagement\Http\Controllers\AdminApi;


use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\FlyerlessClubManagement\Http\Controllers\Controller;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceResolver;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AdminDescriptionController extends Controller
{
    public function index(Request $request, Authentication $authentication, ModuleInstance $moduleInstance) {
        $this->authorize('admin.club.index');

        return forResource()->first();
    }

    public function description_all(Request $request, Authentication $authentication, ModuleInstance $moduleInstance) {
        $this->authorize('admin.club.index');

        return forModuleInstance();
    }

}