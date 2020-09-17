<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Http\Controllers\Participant;

use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ClubLogoController extends TestCase
{

    /** @test */
    public function a_403_code_is_returned_if_the_permission_is_not_owned()
    {
        $this->revokePermissionTo('flyerless-club-management.club.index');

        $description = factory(Description::class)->create([
          'module_instance_id' => $this->getModuleInstance()->id(),
          'activity_instance_id' => $this->getActivityInstance()->id
        ]);

        $response = $this->get($this->userUrl('/club_logo'));
        $response->assertStatus(403);
    }

    /** @test */
    public function a_404_code_is_returned_if_the_file_is_not_found()
    {
        $this->bypassAuthorization();

        $response = $this->get($this->userUrl('/club_logo'));
        $response->assertStatus(404);
    }

    /** @test */
    public function a_404_code_is_returned_if_the_file_is_not_found_in_storage()
    {
        $this->bypassAuthorization();

        $description = factory(Description::class)->create([
          'module_instance_id' => $this->getModuleInstance()->id(),
          'activity_instance_id' => $this->getActivityInstance()->id
        ]);

        $response = $this->get($this->userUrl('/club_logo'));
        $response->assertStatus(404);
    }

    /** @test */
    public function a_download_response_is_returned_if_the_file_is_returned()
    {
        $this->bypassAuthorization();

        Storage::fake();
        $path = Storage::disk()->put('flyerless-club-management', UploadedFile::fake()->create('test.png'));

        $description = factory(Description::class)->create([
          'module_instance_id' => $this->getModuleInstance()->id(),
          'activity_instance_id' => $this->getActivityInstance()->id,
          'path_of_image' => $path
        ]);

        $response = $this->get($this->userUrl('/club_logo'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $response->assertHeader('Content-Disposition', 'attachment; filename=' . str_replace('flyerless-club-management/', '', $path));
    }

}
