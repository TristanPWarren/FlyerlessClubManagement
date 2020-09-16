<?php

namespace Flyerless\FlyerlessClubManagement\Events\Description;

use BristolSU\ControlDB\Contracts\Repositories\User;
use BristolSU\Support\Action\Contracts\TriggerableEvent;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DescriptionCreated implements TriggerableEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var Description
     */
    public $description;

    public function __construct(Description $description)
    {
        $this->description = $description;
    }

    public static function getFieldMetaData(): array
    {
        return [
          'user_id' => [
            'label' => 'User ID',
            'helptext' => 'The ID of the user who uploaded the description'
          ],
          'user_email' => [
            'label' => 'Email Address',
            'helptext' => 'Email Address of the user. May be empty.'
          ],
          'user_first_name' => [
            'label' => 'First Name',
            'helptext' => 'First Name of the user. May be empty.'
          ],
          'user_last_name' => [
            'label' => 'Last Name',
            'helptext' => 'Last Name of the user. May be empty.'
          ],
          'user_preferred_name' => [
            'label' => 'Preferred Name',
            'helptext' => 'Preferred Name of the user. May be empty.'
          ],
          'club_id' => [
            'label' => 'Group ID',
            'helptext' => 'ID of the group the description belongs to'
          ],
          'club_name' => [
            'label' => 'Group Name',
            'helptext' => 'Name of the group the description belongs to'
          ],
          'description' => [
            'label' => 'Description',
            'helptext' => 'The main description for the group'
          ],
          'form_link' => [
            'label' => 'Form Link',
            'helptext' => 'A link to the group form'
          ],
          'club_email' => [
            'label' => 'Group Email',
            'helptext' => 'Email address of the group the description belongs to'
          ],
          'club_facebook' => [
            'label' => 'Facebook',
            'helptext' => 'A link to the group facebook page'
          ],
          'club_instagram' => [
            'label' => 'Instagram',
            'helptext' => 'A link to the group instagram'
          ],
          'club_website' => [
            'label' => 'Website',
            'helptext' => 'Website of the club'
          ],
          'mime' => [
            'label' => 'Image Mimetype',
            'helptext' => 'The mimetime of the image accompanying the description'
          ],
          'path_of_image' => [
            'label' => 'Image Path',
            'helptext' => 'The path at which the image is saved'
          ],
          'size' => [
            'label' => 'Image Size',
            'helptext' => 'The size of the image in bytes'
          ],
          'uploaded_by' => [
            'label' => 'Uploaded By',
            'helptext' => 'ID of the user who uploaded the description'
          ],
          'activity_instance_id' => [
            'label' => 'Activity Instance ID',
            'helptext' => 'The id of the activity instance viewed.'
          ],
          'module_instance_id' => [
            'label' => 'Module Instance ID',
            'helptext' => 'ID of the module instance viewed'
          ],
          'created_at' => [
            'label' => 'Created At',
            'helptext' => 'The date and time at which the description was created'
          ]
        ];
    }

    public function getFields(): array
    {
        $user = app(User::class)->getById($this->description->uploaded_by);
        $dataUser = $user->data();

        return [
          'user_id' => $user->id(),
          'user_email' => $dataUser->email(),
          'user_first_name' => $dataUser->firstName(),
          'user_last_name' => $dataUser->lastName(),
          'user_preferred_name' => $dataUser->preferredName(),
          'club_id' => $this->description->club_id,
          'club_name' => $this->description->club_name,
          'description' => $this->description->description,
          'form_link' => $this->description->form_link,
          'club_email' => $this->description->club_email,
          'club_facebook' => $this->description->club_facebook,
          'club_instagram' => $this->description->club_instagram,
          'club_website' => $this->description->club_website,
          'mime' => $this->description->mime,
          'path_of_image' => $this->description->path_of_image,
          'size' => $this->description->size,
          'uploaded_by' => $this->description->uploaded_by,
          'activity_instance_id' => $this->description->activity_instance_id,
          'module_instance_id' => $this->description->module_instance_id,
          'created_at' => $this->description->created_at->format('Y-m-d H:i:s')
        ];
    }
}
