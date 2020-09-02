<?php


namespace Flyerless\FlyerlessClubManagement\Http\Controllers\ParticipantApi;


use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\FlyerlessClubManagement\Http\Controllers\Controller;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceResolver;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DescriptionController extends Controller
{

    public function index(Request $request, Authentication $authentication) {
        //FOR TESTING
//        $controlGroup = $authentication->getRole();
//        $deleteRows = Description::forResource()->get()->delete();
//        $description = Description::forResource()->first();
//        dd($description);

        $this->authorize('club.index');

        //Check if club description exists
        $description = Description::forResource()->first();

        //If not create new one and populate with default values
        if ($description === null) {
            $this->createBlankDescription($authentication, $request);
        }

        //Return description
        $description = Description::forResource()->first();

        return $description;
    }




    public function store(Request $request, Authentication $authentication)
    {
        $this->authorize('club.update');

        //Check if club description exists
        $description = Description::forResource()->first();

        //If no create one with temp values
        if ($description === null ) {
            $this->createBlankDescription($authentication, $request);
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
        $description->mime = $newMime;
        $description->path_of_image = $newPath;
        $description->size = $newSize;
        $description->uploaded_by = $authentication->getUser()->id();

        $description->save();

        //Remove old image
        if ($oldPathToImage !== '') {
            Storage::delete($oldPathToImage);
        }

        return $description;
    }




    public function destroy(Request $request, Authentication $authentication)
    {
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

//TODO: Ask Toby when laravel can resolve dependencies?
//TODO: Ask Toby, in fileupload when delete is called are the files actually deleted
    private function createBlankDescription(Authentication $authentication, Request $request) {
        //Check user has a group
        if ($authentication->getGroup() !== null) {
            Description::create([
                'club' => $authentication->getGroup()->id(),
                'description' => "",
                'form_link' => "",
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