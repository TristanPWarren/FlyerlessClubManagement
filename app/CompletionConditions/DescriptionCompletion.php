<?php

namespace Flyerless\FlyerlessClubManagement\CompletionConditions;

use Flyerless\FlyerlessClubManagement\Models\Description;
use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\Completion\Contracts\CompletionCondition;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstance;
use FormSchema\Schema\Form;


class DescriptionCompletion extends CompletionCondition
{

    public function isComplete($settings, ActivityInstance $activityInstance, ModuleInstance $moduleInstance): bool
    {
        //Check if club description exists
        $description = Description::forResource($activityInstance->id, $moduleInstance->id)->first();

        if ($description === null ) {
            return false;
        } else {
            return true;
        }
    }

    public function percentage($settings, ActivityInstance $activityInstance, ModuleInstance $moduleInstance): int
    {
        //Check if club description exists
        $description = Description::forResource($activityInstance->id, $moduleInstance->id)->first();

        if ($description === null ) {
            return 0;
        } else {
            return 1;
        }
    }


    public function options(): Form
    {
        return \FormSchema\Generator\Form::make()->getSchema();
    }

    public function name(): string
    {
        return 'Club Description has been updated';
    }

    public function description(): string
    {
        return 'Completed when the description of the club has any value assigned to it';
    }

    public function alias(): string
    {
        return 'flyerless_club_management_description_updated';
    }
}