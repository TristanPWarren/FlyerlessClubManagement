<?php

namespace Flyerless\Tests\FlyerlessClubManagement\CompletionConditions;

use Flyerless\FlyerlessClubManagement\CompletionConditions\DescriptionCompletion;
use Flyerless\FlyerlessClubManagement\Models\Description;
use Flyerless\Tests\FlyerlessClubManagement\TestCase;
use FormSchema\Schema\Form;

class DescriptionCompletionTest extends TestCase
{

    /** @test */
    public function isComplete_returns_true_if_description_filled_in()
    {
        $completionCondition = new DescriptionCompletion('flyerless-club-management');

        factory(Description::class)->create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'module_instance_id' => $this->getModuleInstance()->id,
          'activity_instance_id' => $this->getActivityInstance()->id
        ]);

        $this->assertTrue(
          $completionCondition->isComplete([], $this->getActivityInstance(), $this->getModuleInstance())
        );

    }

    /** @test */
    public function isComplete_returns_false_if_description_not_filled_in()
    {
        $completionCondition = new DescriptionCompletion('flyerless-club-management');

        $this->assertFalse(
          $completionCondition->isComplete([], $this->getActivityInstance(), $this->getModuleInstance())
        );

    }

    /** @test */
    public function percentage_returns_100_if_description_filled_in()
    {
        $completionCondition = new DescriptionCompletion('flyerless-club-management');

        $description = factory(Description::class)->create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'module_instance_id' => $this->getModuleInstance()->id,
          'activity_instance_id' => $this->getActivityInstance()->id
        ]);

        $this->assertEquals(
          100,
          $completionCondition->percentage([], $this->getActivityInstance(), $this->getModuleInstance())
        );
    }

    /** @test */
    public function percentage_returns_100_if_two_descriptions_filled_in()
    {
        $completionCondition = new DescriptionCompletion('flyerless-club-management');

        $description = factory(Description::class)->create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'module_instance_id' => $this->getModuleInstance()->id,
          'activity_instance_id' => $this->getActivityInstance()->id
        ]);
        $description2 = factory(Description::class)->create([
          'club_id' => $this->getControlGroup()->id(),
          'club_name' => $this->getControlGroup()->data()->name(),
          'module_instance_id' => $this->getModuleInstance()->id,
          'activity_instance_id' => $this->getActivityInstance()->id
        ]);

        $this->assertEquals(
          100,
          $completionCondition->percentage([], $this->getActivityInstance(), $this->getModuleInstance())
        );
    }

    /** @test */
    public function percentage_returns_0_if_description_not_filled_in()
    {
        $completionCondition = new DescriptionCompletion('flyerless-club-management');

        $this->assertEquals(
          0,
          $completionCondition->percentage([], $this->getActivityInstance(), $this->getModuleInstance())
        );
    }

    /** @test */
    public function name_description_and_alias_return_values()
    {
        $completionCondition = new DescriptionCompletion('flyerless-club-management');

        $this->assertEquals('flyerless_club_management_description_updated', $completionCondition->alias());
        $this->assertIsString($completionCondition->name());
        $this->assertIsString($completionCondition->description());
    }

    /** @test */
    public function options_returns_a_form_schema()
    {
        $completionCondition = new DescriptionCompletion('flyerless-club-management');

        $this->assertInstanceOf(Form::class, $completionCondition->options());
        $this->assertIsString($completionCondition->name());
        $this->assertIsString($completionCondition->description());
    }


}
