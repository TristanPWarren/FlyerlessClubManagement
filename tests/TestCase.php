<?php

namespace Flyerless\Tests\FlyerlessClubManagement;

use Prophecy\PhpUnit\ProphecyTrait;
use BristolSU\ControlDB\Repositories\Pivots\UserGroup;
use Flyerless\FlyerlessClubManagement\ModuleServiceProvider;
use BristolSU\Support\Testing\AssertsEloquentModels;
use BristolSU\Support\Testing\CreatesModuleEnvironment;
use BristolSU\Support\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use AssertsEloquentModels, CreatesModuleEnvironment, ProphecyTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setModuleIsFor('group');
        $this->createModuleEnvironment('flyerless-club-management');
        app(UserGroup::class)->addUserToGroup($this->getControlUser(), $this->getControlGroup());
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            ModuleServiceProvider::class
        ]);
    }

}
