<?php

namespace Flyerless\FlyerlessClubManagement\Jobs;

use BristolSU\Support\Connection\Contracts\Connector;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository as ServiceRepository;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateDescriptionOnFlyerless implements ShouldQueue
{

    use Dispatchable, Queueable, SerializesModels;

    /**
     * @var Description
     */
    public $description;

    public function __construct(Description $description)
    {
        $this->description = $description;
    }

    public function handle(ServiceRepository $serviceRepository)
    {
        $body = [
          'Request_Type' => 0,
          'clubData' => json_encode($this->getData())
        ];
        $users = $this->connector($serviceRepository)->request('POST', '/', ['form_params' => $body]);
    }

    private function getData(): array
    {
        return [
          'portalID' => $this->description->club_id,
          'Name' => $this->description->club_name,
          'Email' => $this->description->club_email,
          'PicLink' => $this->description->path_of_image,
          'Desc' => $this->description->description,
          'Facebook' => $this->description->club_facebook,
          'Instagram' => $this->description->club_instagram,
          'Website' => $this->description->club_website
        ];
    }

    private function connector(ServiceRepository $serviceRepository): Connector
    {
        return $serviceRepository->getConnectorForService('flyerless', $this->description->module_instance_id);
    }

}
