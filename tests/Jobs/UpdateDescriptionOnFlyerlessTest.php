<?php

namespace Flyerless\Tests\FlyerlessClubManagement\Jobs;

use BristolSU\Support\Connection\Contracts\Connector;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use Flyerless\FlyerlessClubManagement\Jobs\UpdateDescriptionOnFlyerless;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;

class UpdateDescriptionOnFlyerlessTest extends TestCase
{

    /** @test */
    public function it_calls_the_connector_with_the_right_options(){
        $description = factory(Description::class)->create([
          'module_instance_id' => $this->getModuleInstance()->id(),
          'activity_instance_id' => $this->getActivityInstance()->id,
        ]);

        $connector = $this->prophesize(Connector::class);
        $connector->request('POST', '/', [
          'form_params' => [
            'Request_Type' => 0,
            'clubData' => json_encode([
              'portalID' => $description->club_id,
              'Name' => $description->club_name,
              'Email' => $description->club_email,
              'PicLink' => $description->path_of_image,
              'Desc' => $description->description,
              'Facebook' => $description->club_facebook,
              'Instagram' => $description->club_instagram,
              'Website' => $description->club_website
            ])
          ]
        ])->shouldBeCalled();

        $serviceRepository = $this->prophesize(ModuleInstanceServiceRepository::class);
        $serviceRepository->getConnectorForService('flyerless', $this->getModuleInstance()->id())
            ->shouldBeCalled()->willReturn($connector->reveal());

        $job = new UpdateDescriptionOnFlyerless($description);
        $job->handle($serviceRepository->reveal());
    }

}
