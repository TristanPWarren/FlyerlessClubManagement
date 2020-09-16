<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Events;

use BristolSU\ControlDB\Models\DataGroup;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\Group;
use BristolSU\ControlDB\Models\User;
use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Carbon\Carbon;
use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionUpdated;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;

class DescriptionUpdatedTest extends TestCase
{

    /** @test */
    public function getFields_returns_the_fields_from_the_pageview()
    {
        $dataUser = factory(DataUser::class)->create([
          'email' => 'myemail@email.com',
          'first_name' => 'Toby',
          'last_name' => 'Twigger',
          'preferred_name' => 'Toby Twigger2'
        ]);

        $dataGroup = factory(DataGroup::class)->create([
          'email' => 'myemail@email.com',
          'name' => 'CHAOS'
        ]);

        $user = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $group = factory(Group::class)->create(['data_provider_id' => $dataGroup->id()]);

        $moduleInstance = factory(ModuleInstance::class)->create(['name' => 'ModInst1']);
        $activityInstance = factory(ActivityInstance::class)->create(['name' => 'ActInst1']);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $description = factory(Description::class)->create([
          'club_id' => $group->id(),
          'club_name' => $group->data()->name(),
          'description' => 'A Description Here',
          'form_link' => 'Form Link',
          'club_email' => $group->data()->email(),
          'club_facebook' => 'fb',
          'club_instagram' => 'fb',
          'club_website' => 'https://example.com',
          'mime' => 'image/jpeg',
          'path_of_image' => 'flyerless-club-management/test123.jpg',
          'size' => 7888,
          'uploaded_by' => $user->id(),
          'activity_instance_id' => $activityInstance->id,
          'module_instance_id' => $moduleInstance->id,
        ]);

        $event = new DescriptionUpdated($description);

        $this->assertEquals([
          'user_id' => $user->id(),
          'user_email' => 'myemail@email.com',
          'user_first_name' => 'Toby',
          'user_last_name' => 'Twigger',
          'user_preferred_name' => 'Toby Twigger2',
          'club_id' => $group->id(),
          'club_name' => $group->data()->name(),
          'description' => 'A Description Here',
          'form_link' => 'Form Link',
          'club_email' => $group->data()->email(),
          'club_facebook' => 'fb',
          'club_instagram' => 'fb',
          'club_website' => 'https://example.com',
          'mime' => 'image/jpeg',
          'path_of_image' => 'flyerless-club-management/test123.jpg',
          'size' => 7888,
          'uploaded_by' => $user->id(),
          'activity_instance_id' => $activityInstance->id,
          'module_instance_id' => $moduleInstance->id,
          'created_at' => $now->format('Y-m-d H:i:s'),
          'updated_at' => $now->format('Y-m-d H:i:s'),
        ], $event->getFields());

    }

    /** @test */
    public function getFieldMetaData_returns_an_array_with_the_required_keys()
    {
        $buttonClick = factory(Description::class)->create();
        $event = new DescriptionUpdated($buttonClick);

        $requiredFields = array_keys($event->getFields());
        $actualFields = DescriptionUpdated::getFieldMetaData();

        foreach ($requiredFields as $requiredField) {
            $this->assertArrayHasKey($requiredField, $actualFields);
        }
    }

}
