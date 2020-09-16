<?php

namespace Flyerless\FlyerlessClubManagement\Http\Controllers\Participant;

use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceResolver;
use Flyerless\FlyerlessClubManagement\Http\Controllers\Controller;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClubLogoController extends Controller
{

    public function download()
    {
        $this->authorize('club.index');

        $description = Description::forResource()->firstOrFail();

        if(Storage::exists($description->path_of_image)) {
            return Storage::download($description->path_of_image);
        }

        throw new NotFoundHttpException('The image could not be found.');
    }

}
