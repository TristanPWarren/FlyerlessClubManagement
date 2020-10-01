<?php


namespace Flyerless\FlyerlessClubManagement\Http\Controllers\ParticipantApi;


use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionCreated;
use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionUpdated;
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
        $updating = true;

        //If no create one with temp values
        if ($description === null ) {
            $this->createBlankDescription($authentication, $request, $groupRepository);
            $updating = false;
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

        //Website
        if ($request->input('tags') === null) {
            $newTags = '';
        } else {
            $newTags = $request->input('tags', $description->tags);
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
            $pathToImage = $descriptionImage->storePublicly('flyerless-club-management');

            $newMime = $descriptionImage->getClientMimeType();
            $newPath = $pathToImage;
            $newSize = $descriptionImage->getSize();
        }

        $description->description = $newDescription;
        $description->form_link = $newLink;
        $description->club_instagram = $newInstagram;
        $description->club_facebook = $newFacebook;
        $description->club_website = $newWebsite;
        $description->tags = $newTags;
        $description->mime = $newMime;
        $description->path_of_image = $newPath;
        $description->size = $newSize;
        $description->uploaded_by = $authentication->getUser()->id();

        $description->save();

//        $connector = app(ModuleInstanceServiceRepository::class)->getConnectorForService('flyerless', $moduleInstance->id);
//        $data = [
//          'portalID' => $description->club_id,
//          'Name' => $description->club_name,
//          'Email' => $description->club_email,
//          'PicLink' => $description->path_of_image,
//          'Desc' => $description->description,
//          'Facebook' => $description->club_facebook,
//          'Instagram' => $description->club_instagram,
//          'Website' => $description->club_website,
//          'FormLink' => $description->form_link,
//          'Keywords' => $description->tags,
//          'MembershipLink' => '',
//        ];
//
//        $body = [
//            'Request_Type' => 4,
//            'clubData' => json_encode($data),
//        ];
//
//        $response = $connector->request('POST', '', $body);
//        dd(json_decode((string) $response->getBody()->getContents(), false));


        //Remove old image
        if ($oldPathToImage !== '') {
            Storage::delete($oldPathToImage);
        }

        if($updating) {
            event(new DescriptionUpdated($description));
        } else {
            event(new DescriptionCreated($description));
        }

        return $description;
    }


//    Helpers:
    private function createBlankDescription(Authentication $authentication, Request $request, Group $groupRepository) {
        $group = $groupRepository->getById($authentication->getGroup()->id());
        $data = $group->data();

        Description::create([
            'club_id' => $authentication->getGroup()->id(),
            'club_name' => $data->name(),
            'description' => "",
            'form_link' => "",
            'club_email' => $data->email() ?? '',
            'club_facebook' => "",
            'club_instagram' => "",
            'club_website' => "",
            'tags' => "",
            'mime' => "",
            'path_of_image' => "",
            'size' => 0,
            'uploaded_by' => $authentication->getUser()->id(),
            'activity_instance_id' => $request->input('activity_instance_id'),
        ]);
    }

}
