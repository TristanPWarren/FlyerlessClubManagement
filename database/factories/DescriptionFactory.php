<?php

use BristolSU\ControlDB\Models\Group;
use BristolSU\ControlDB\Models\User;
use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Faker\Generator as Faker;
use Flyerless\FlyerlessClubManagement\Models\Description;


$factory->define(Description::class, function (Faker $faker) {
    $group = factory(Group::class)->create();
    $imagePath = $faker->image();
    $user = factory(User::class)->create();

    return [
      'club_id' => $group->id(),
      'club_name' => $group->data()->name(),
      'description' => implode('', $faker->paragraphs(2)),
      'form_link' => $faker->url,
      'club_email' => $group->data()->email(),
      'club_facebook' => $faker->userName,
      'club_instagram' => '@' . $faker->userName,
      'club_website' => $faker->url,
      'mime' => mime_content_type($imagePath),
      'path_of_image' => $imagePath,
      'size' => filesize($imagePath),
      'module_instance_id' => function () {
          return factory(ModuleInstance::class)->create()->id;
      },
      'activity_instance_id' => function () {
          return factory(ActivityInstance::class)->create()->id;
      },
      'uploaded_by' => $user->id()
    ];
});
