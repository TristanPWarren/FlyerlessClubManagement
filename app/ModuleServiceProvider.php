<?php

namespace Flyerless\FlyerlessClubManagement;

use BristolSU\Support\Module\ModuleServiceProvider as ServiceProvider;
use BristolSU\Support\Completion\Contracts\CompletionConditionManager;
use BristolSU\Support\Connection\Contracts\ConnectorStore;
use Flyerless\FlyerlessClubManagement\Models\Description;
use BristolSU\Support\Connection\ServiceRequest;
use Flyerless\FlyerlessClubManagement\CompletionConditions\DescriptionCompletion;
use Flyerless\FlyerlessClubManagement\Connectors;
use FormSchema\Schema\Form;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{

    protected $permissions = [
        //Web
        'view-page' => [
            'name' => 'View Participant Page',
            'description' => 'View the main page of the module.',
            'admin' => false
        ],
        'admin.view-page' => [
            'name' => 'View Admin Page',
            'description' => 'View the administrator page of the module.',
            'admin' => true
        ],

        //API
        'club.index' => [
            'name' => 'View club details',
            'description' => 'Allows users to view current club details',
            'admin' => false,
        ],
        'club.update' => [
            'name' => 'Modify club details',
            'description' => 'Modify the details of the club or society',
            'admin' => false,
        ],
        'club.delete' => [
            'name' => 'Remove Club Details',
            'description' => 'Remove the details of the club',
            'admin' => false,
        ],
        'admin.club.index' => [
            'name' => 'View all club details',
            'description' => 'Fetch all club details for all clubs',
            'admin' => true,
        ]
    ];

    protected $events = [

    ];

    protected $completionConditions = [
        'flyerless_club_management_description_updated' => \Flyerless\FlyerlessClubManagement\CompletionConditions\DescriptionCompletion::class
    ];
    
    protected $commands = [
        
    ];

    protected $requiredServices = [
        'flyerless'
    ];
    
    public function alias(): string
    {
        return 'flyerless-club-management';
    }

    public function namespace()
    {
        return 'Flyerless\FlyerlessClubManagement\Http\Controllers';
    }
    
    public function baseDirectory()
    {
        return __DIR__ . '/..';
    }

    public function boot()
    {
        parent::boot();

        $connectorStore = $this->app->make(ConnectorStore::class);
        $connectorStore->register(
            'Flyerless Api (Required to use Flyerless Club Description Update)',
            'Connect to Flyerless',
            'flyerless-club-api',
            'flyerless',
            \Flyerless\FlyerlessClubManagement\Connectors\OAuth::class
        );

        $serviceRequest = $this->app->make(ServiceRequest::class);
        $serviceRequest->required('flyerless-club-management', $this->requiredServices);


        Route::bind('update_description', function($id) {
            $description = Description::findOrFail($id);

            if(request()->route('module_instance_slug') && (int) $description->module_instance_id === request()->route('module_instance_slug')->id()) {
                return $description;
            }
            throw (new ModelNotFoundException)->setModel(Description::class);
        });
    }

    public function register()
    {
        parent::register();
    }

    /**
     * @inheritDoc
     */
    public function settings(): Form
    {
        return \FormSchema\Generator\Form::make()->getSchema();
    }
}
