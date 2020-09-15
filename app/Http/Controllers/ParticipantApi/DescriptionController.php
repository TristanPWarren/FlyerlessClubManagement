<?php


namespace Flyerless\FlyerlessClubManagement\Http\Controllers\ParticipantApi;


use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\FlyerlessClubManagement\Http\Controllers\Controller;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceResolver;
use BristolSU\ControlDB\Contracts\Repositories\User;
use BristolSU\ControlDB\Contracts\Repositories\Group;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DescriptionController extends Controller
{

    public function index(Request $request, Authentication $authentication, Group $groupRepository) {
        $this->authorize('club.index');

        //Check if club description exists
        $description = Description::forResource()->first();

        //If not create new one and populate with default values
        if ($description === null) {
            $this->createBlankDescription($authentication, $request, $groupRepository);
        }

        //Return description
        $description = Description::forResource()->first();

        return $description;
    }




    public function store(Activity $activity, ModuleInstance $moduleInstance, Request $request, Authentication $authentication, Group $groupRepository)
    {
        $this->authorize('club.update');

        //Check if club description exists
        $description = Description::forResource()->first();

        //If no create one with temp values
        if ($description === null ) {
            $this->createBlankDescription($authentication, $request, $groupRepository);
        }

        //Update values
        $description = Description::forResource()->first();

        //Description
        if ($request->input('description') === null) {
            $newDescription = '';
        } else {
            $newDescription = $request->input('description', $description->description);
        }

        //Form Link
        if ($request->input('link') === null) {
            $newLink = '';
        } else {
            $newLink = $request->input('link', $description->form_link);
        }

        //Instagram
        if ($request->input('instagram') === null) {
            $newInstagram = '';
        } else {
            $newInstagram = $request->input('instagram', $description->club_instagram);
        }

        //Facebook
        if ($request->input('facebook') === null) {
            $newFacebook = '';
        } else {
            $newFacebook = $request->input('facebook', $description->club_facebook);
        }

        //Website
        if ($request->input('website') === null) {
            $newWebsite = '';
        } else {
            $newWebsite = $request->input('website', $description->club_website);
        }

        //Mime, Image Link, Size
        if ($request->file('file') === null) {
            $newMime = $description->mime;
            $newPath = $description->path_of_image;
            $newSize = $description->size;
            $oldPathToImage = '';
        } else {
            $descriptionImage = $request->file('file')[0];
            $oldPathToImage = $description->path_of_image;
            $pathToImage = $descriptionImage->store('flyerlessclubmanagement');

            $newMime = $descriptionImage->getClientMimeType();
            $newPath = $pathToImage;
            $newSize = $descriptionImage->getSize();
        }

        $description->description = $newDescription;
        $description->form_link = $newLink;
        $description->club_instagram = $newInstagram;
        $description->club_facebook = $newFacebook;
        $description->club_website = $newWebsite;
        $description->mime = $newMime;
        $description->path_of_image = $newPath;
        $description->size = $newSize;
        $description->uploaded_by = $authentication->getUser()->id();

        $description->save();

        //Remove old image
        if ($oldPathToImage !== '') {
            Storage::delete($oldPathToImage);
        }

        try {
            $d = $description;
            $data = array('portalID' => $d->club_id, 'Name' => $d->club_name, 'Email' => $d->club_email, 'PicLink' => $d->path_of_image,
                     'Desc' => $d->description, 'Facebook' => $d->club_facebook, 'Instagram' => $d->club_instagram, 'Website' => $d->club_website);
            $options = array('Request_Type' => 0);
            $options = array_merge($options, array('Request_Type' => 4));
            $options = array_merge($options, array('clubData' => json_encode($data)));
            $connector = app(ModuleInstanceServiceRepository::class)->getConnectorForService('flyerless', $moduleInstance->id);
            $users = $connector->request('POST', '', $options);
            dd($users->getBody()->getContents());

        } catch (\BristolSU\Support\ModuleInstance\Connection\NoConnectionAvailable $e) {
            dd($e);
        }

        return $description;
    }




    public function destroy(Activity $activity, Request $request, Authentication $authentication, Group $groupRepository, ModuleInstance $moduleInstance)
    {

//        $group = $groupRepository->getById($authentication->getGroup()->id());
//
//        $data = $group->data();
//
//        $connector = app(ModuleInstanceServiceRepository::class)->getConnectorForService('flyerless', $moduleInstance->id);
//        $users = $connector->request('POST', '', []);
//
//        dd($users);
//
        dd("Function Not Implemented Yet");
        $this->authorize('club.delete');

        Description::forResource()->first()->delete();

        return "Description Removed";
    }


    public function club_logo() {
        $this->authorize('club.index');

         $description = Description::forResource()->first();

         if ($description->activity_instance_id !== app(ActivityInstanceResolver::class)->getActivityInstance()->id) {
             throw new AuthorizationException();
         }

         return Storage::download($description->path_of_image);
    }


//    Helpers:
    private function createBlankDescription(Authentication $authentication, Request $request, Group $groupRepository) {
        //Check user has a group
        if ($authentication->getGroup() !== null) {
            $group = $groupRepository->getById($authentication->getGroup()->id());
            $data = $group->data();

            Description::create([
                'club_id' => $authentication->getGroup()->id(),
                'club_name' => $data->name(),
                'description' => "",
                'form_link' => "",
                'club_email' => $data->email(),
                'club_facebook' => "",
                'club_instagram' => "",
                'club_website' => "",
                'mime' => "",
                'path_of_image' => "",
                'size' => 0,
                'uploaded_by' => $authentication->getUser()->id(),
                'activity_instance_id' => $request->input('activity_instance_id'),
            ]);

        //TODO: Ask Toby
        } else {
            dd("NO GROUP FOUND");
            //TODO: Throw some sort of error
        }
    }

}