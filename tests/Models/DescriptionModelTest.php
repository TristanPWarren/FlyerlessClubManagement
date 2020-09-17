<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Models;

use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;

class DescriptionModelTest extends TestCase
{

    /** @test */
    public function a_description_model_can_be_created()
    {

        $description = Description::create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'description' => 'Test description of a club',
          'form_link' => 'https://example.com',
          'club_email' => $this->getControlGroup()->data()->email(),
          'club_facebook' => 'bsu-club',
          'club_instagram' => '@bsu-club',
          'club_website' => 'https://website.com',
          'mime' => 'image/png',
          'path_of_image' => 'somepath.png',
          'size' => 580009,
          'module_instance_id' => $this->getModuleInstance()->id(),
          'activity_instance_id' => $this->getActivityInstance()->id,
          'uploaded_by' => $this->getControlUser()->id()
        ]);

        $this->assertModelEquals(Description::findOrFail($description->id), $description);
        $this->assertEquals($this->getControlGroup()->id(), $description->club_id);
        $this->assertEquals($this->getControlGroup()->data()->name(), $description->club_name);
        $this->assertEquals('Test description of a club', $description->description);
        $this->assertEquals('https://example.com', $description->form_link);
        $this->assertEquals($this->getControlGroup()->data()->email(), $description->club_email);
        $this->assertEquals('bsu-club', $description->club_facebook);
        $this->assertEquals('@bsu-club', $description->club_instagram);
        $this->assertEquals('https://website.com', $description->club_website);
        $this->assertEquals('image/png', $description->mime);
        $this->assertEquals('somepath.png', $description->path_of_image);
        $this->assertEquals(580009, $description->size);
        $this->assertEquals($this->getModuleInstance()->id(), $description->module_instance_id);
        $this->assertEquals($this->getActivityInstance()->id, $description->activity_instance_id);
        $this->assertEquals($this->getControlUser()->id(), $description->uploaded_by);

    }

    /** @test */
    public function a_module_instance_can_be_retrieved()
    {
        $description = factory(Description::class)->create([
          'module_instance_id' => $this->getModuleInstance()->id(),
        ]);

        $this->assertModelEquals($this->getModuleInstance(), $description->moduleInstance());
    }

    /** @test */
    public function an_activity_instance_can_be_retrieved()
    {
        $description = factory(Description::class)->create([
          'activity_instance_id' => $this->getActivityInstance()->id,
        ]);

        $this->assertModelEquals($this->getActivityInstance(), $description->activityInstance());
    }

}
