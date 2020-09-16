<?php

namespace Flyerless\FlyerlessClubManagement\Models;

use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceRepository;
use BristolSU\Support\Authentication\HasResource;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstanceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

class Description extends Model
{
    use SoftDeletes, HasResource;

    protected $table = 'flyerless_club_management_club_description';

    protected $fillable = [
        'club_id',
        'club_name',
        'description',
        'form_link',
        'club_email',
        'club_facebook',
        'club_instagram',
        'club_website',
        'mime',
        'path_of_image',
        'size',
        'uploaded_by',
        'module_instance_id',
        'activity_instance_id',
    ];

    /**
     * @return ModuleInstance
     */
    public function moduleInstance()
    {
        return app(ModuleInstanceRepository::class)->getById($this->module_instance_id);
    }

    /**
     * @return ActivityInstance
     */
    public function activityInstance()
    {
        return app(ActivityInstanceRepository::class)->getById($this->activity_instance_id);
    }

}
