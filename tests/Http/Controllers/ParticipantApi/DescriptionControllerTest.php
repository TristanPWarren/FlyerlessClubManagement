<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Http\Controllers\ParticipantApi;

use BristolSU\Support\Authentication\Contracts\Authentication;
use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionCreated;
use Flyerless\FlyerlessClubManagement\Events\Description\DescriptionUpdated;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class DescriptionControllerTest extends TestCase
{

    /** @test */
    public function index_returns_a_description()
    {
        $this->bypassAuthorization();

        $description = factory(Description::class)->create([
          'activity_instance_id' => $this->getActivityInstance()->id,
          'module_instance_id' => $this->getModuleInstance()->id()
        ]);

        $response = $this->getJson($this->userApiUrl('/description'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
          'id' => $description->id,
          'description' => $description->description
        ]);
    }

    /** @test */
    public function index_creates_a_description_if_none_present()
    {
        $this->bypassAuthorization();

        $response = $this->getJson($this->userApiUrl('/description'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
          'activity_instance_id' => (string)$this->getActivityInstance()->id,
          'module_instance_id' => (string)$this->getModuleInstance()->id()
        ]);

        $this->assertDatabaseHas('flyerless_club_management_club_description', [
          'activity_instance_id' => $this->getActivityInstance()->id,
          'module_instance_id' => $this->getModuleInstance()->id()
        ]);
    }

    /** @test */
    public function index_returns_a_403_if_the_permission_is_not_given()
    {
        $this->revokePermissionTo('flyerless-club-management.club.index');

        $response = $this->getJson($this->userApiUrl('/description'));
        $response->assertStatus(403);
    }

    /** @test */
    public function index_returns_a_200_if_the_permission_is_given()
    {
        $this->givePermissionTo('flyerless-club-management.club.index');

        $response = $this->getJson($this->userApiUrl('/description'));
        $response->assertStatus(200);
    }

    /** @test */
    public function store_returns_a_403_if_the_permission_is_not_given()
    {
        $this->revokePermissionTo('flyerless-club-management.club.update');

        $response = $this->postJson($this->userApiUrl('/description'), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function store_returns_a_200_if_the_permission_is_given()
    {
        $this->givePermissionTo('flyerless-club-management.club.update');
        Event::fake([DescriptionCreated::class, DescriptionUpdated::class]);

        $response = $this->postJson($this->userApiUrl('/description'), []);
        $response->assertStatus(200);
    }

    /** @test */
    public function store_creates_a_new_description_with_the_given_parameters()
    {
        Event::fake([DescriptionCreated::class, DescriptionUpdated::class]);
        Storage::fake('test-disk');
        $this->app->config['filesystems.default'] = 'test-disk';
        $this->bypassAuthorization();

        $this->assertCount(0, Description::all());
        $file = UploadedFile::fake()->image('myfile.jpg');

        $response = $this->postJson($this->userApiUrl('/description'), [
          'description' => 'Some description',
          'link' => 'https://example2.com',
          'instagram' => '@insta',
          'facebook' => 'fb',
          'website' => 'https://example.com',
          'file' => [$file]
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Description::all());

        $description = Description::first();
        $this->assertInstanceOf(Description::class, $description);
        $this->assertTrue($description->exists);

        $data = [
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'description' => 'Some description',
          'form_link' => 'https://example2.com',
          'club_email' => $this->getControlGroup()->data()->email(),
          'club_facebook' => 'fb',
          'club_instagram' => '@insta',
          'club_website' => 'https://example.com',

          'mime' => 'image/jpeg',
          'size' => filesize($file),
          'uploaded_by' => $this->getControlUser()->id()
        ];

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $description->{$key});
        }

        $this->assertIsString($description->path_of_image);
        $this->assertStringStartsWith('flyerless-club-management/', $description->path_of_image);
        Storage::disk('test-disk')->assertExists($description->path_of_image);

    }

    /** @test */
    public function store_sets_an_existing_description_values_or_removes_them_if_null()
    {
        Event::fake([DescriptionCreated::class, DescriptionUpdated::class]);
        Storage::fake('test-disk');
        $this->app->config['filesystems.default'] = 'test-disk';
        $this->bypassAuthorization();

        $this->assertCount(0, Description::all());
        $file = UploadedFile::fake()->image('myfile2.jpg');
        $savedFile = $file->store('flyerless-club-management');

        Storage::disk('test-disk')->assertExists($savedFile);


        $description1 = factory(Description::class)->create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'description' => 'Some description',
          'form_link' => 'https://example2.com',
          'club_email' => $this->getControlGroup()->data()->email(),
          'club_facebook' => 'fb',
          'club_instagram' => '@insta',
          'club_website' => 'https://example.com',
          'activity_instance_id' => $this->getActivityInstance()->id,
          'module_instance_id' => $this->getModuleInstance()->id(),
          'mime' => 'image/jpeg',
          'size' => Storage::size($savedFile),
          'path_of_image' => $savedFile,
          'uploaded_by' => $this->getControlUser()->id()
        ]);


        $response = $this->postJson($this->userApiUrl('/description'), [
          'description' => 'Some description2',
          'link' => 'https://example22.com',
          'instagram' => '@insta2',
          'facebook' => null,
          'website' => 'https://example.com2',
          'file' => null
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Description::all());

        $description = Description::first();
        $this->assertInstanceOf(Description::class, $description);
        $this->assertTrue($description->exists);

        $data = [
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'description' => 'Some description2',
          'form_link' => 'https://example22.com',
          'club_email' => $this->getControlGroup()->data()->email(),
          'club_facebook' => '',
          'club_instagram' => '@insta2',
          'club_website' => 'https://example.com2',
          'mime' => 'image/jpeg',
          'size' => Storage::size($savedFile),
          'path_of_image' => $savedFile,
          'uploaded_by' => $this->getControlUser()->id()
        ];

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $description->{$key});
        }

        Storage::disk('test-disk')->assertExists($description1->path_of_image);
    }

    /** @test */
    public function store_updates_the_image_and_deletes_the_old_one()
    {
        Event::fake([DescriptionCreated::class, DescriptionUpdated::class]);
        Storage::fake('test-disk');
        $this->app->config['filesystems.default'] = 'test-disk';
        $this->bypassAuthorization();

        $this->assertCount(0, Description::all());

        $oldFile = UploadedFile::fake()->image('myfile2.jpg');
        $oldFilePath = $oldFile->store('flyerless-club-management');
        $newFile = UploadedFile::fake()->image('myfile.jpg');

        Storage::disk('test-disk')->assertExists($oldFilePath);

        $description1 = factory(Description::class)->create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'description' => 'Some description',
          'form_link' => 'https://example2.com',
          'club_email' => $this->getControlGroup()->data()->email(),
          'club_facebook' => 'fb',
          'club_instagram' => '@insta',
          'club_website' => 'https://example.com',
          'activity_instance_id' => $this->getActivityInstance()->id,
          'module_instance_id' => $this->getModuleInstance()->id(),
          'mime' => 'image/jpeg',
          'size' => Storage::size($oldFilePath),
          'path_of_image' => $oldFilePath,
          'uploaded_by' => $this->getControlUser()->id()
        ]);


        $response = $this->postJson($this->userApiUrl('/description'), [
          'description' => null,
          'link' => null,
          'instagram' => null,
          'facebook' => null,
          'website' => null,
          'file' => [$newFile]
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Description::all());

        $description = Description::first();
        $this->assertInstanceOf(Description::class, $description);
        $this->assertTrue($description->exists);

        $data = [
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'description' => '',
          'form_link' => '',
          'club_email' => $this->getControlGroup()->data()->email(),
          'club_facebook' => '',
          'club_instagram' => '',
          'club_website' => '',
          'mime' => 'image/jpeg',
          'size' => Storage::size($description->path_of_image),
          'uploaded_by' => $this->getControlUser()->id()
        ];

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $description->{$key});
        }

        Storage::disk('test-disk')->assertMissing($description1->path_of_image);
        Storage::disk('test-disk')->assertExists($description->path_of_image);
    }

    /** @test */
    public function store_fires_an_event_on_creation()
    {
        Event::fake(DescriptionCreated::class);
        $this->bypassAuthorization();
        Storage::fake('test-disk');
        $this->app->config['filesystems.default'] = 'test-disk';

        $oldFile = UploadedFile::fake()->image('myfile2.jpg');

        $this->assertCount(0, Description::all());

        $response = $this->postJson($this->userApiUrl('/description'), [
          'description' => 'Some description2',
          'link' => 'https://example22.com',
          'instagram' => '@insta2',
          'facebook' => null,
          'website' => 'https://example.com2',
          'file' => [$oldFile]
        ]);
        $response->assertStatus(200);

        Event::assertDispatched(DescriptionCreated::class, function(DescriptionCreated $event) {
            return $event->description->description === 'Some description2'
              && $event->description->form_link === 'https://example22.com'
              &&  $event->description->club_instagram === '@insta2'
              &&  $event->description->club_facebook === ''
              && $event->description->club_website === 'https://example.com2';
        });
    }

    /** @test */
    public function store_fires_an_event_on_update()
    {
        Event::fake(DescriptionUpdated::class);

        $this->bypassAuthorization();
        Storage::fake('test-disk');
        $this->app->config['filesystems.default'] = 'test-disk';

        $oldFile = UploadedFile::fake()->image('myfile2.jpg');
        $oldFilePath = $oldFile->store('flyerless-club-management');

        Storage::disk('test-disk')->assertExists($oldFilePath);
        $description1 = factory(Description::class)->create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'description' => 'Some description',
          'form_link' => 'https://example2.com',
          'club_email' => $this->getControlGroup()->data()->email(),
          'club_facebook' => 'fb',
          'club_instagram' => '@insta',
          'club_website' => 'https://example.com',
          'activity_instance_id' => $this->getActivityInstance()->id,
          'module_instance_id' => $this->getModuleInstance()->id(),
          'mime' => 'image/jpeg',
          'size' => Storage::size($oldFilePath),
          'path_of_image' => $oldFilePath,
          'uploaded_by' => $this->getControlUser()->id()
        ]);


        $response = $this->postJson($this->userApiUrl('/description'), [
          'description' => 'Some description2',
          'link' => 'https://example22.com',
          'instagram' => '@insta2',
          'facebook' => null,
          'website' => 'https://example.com2',
          'file' => null
        ]);
        $response->assertStatus(200);

        Event::assertDispatched(DescriptionUpdated::class, function(DescriptionUpdated $event) {
            return $event->description->description === 'Some description2'
              && $event->description->form_link === 'https://example22.com'
              &&  $event->description->club_instagram === '@insta2'
              &&  $event->description->club_facebook === ''
              && $event->description->club_website === 'https://example.com2';
        });

    }

}
