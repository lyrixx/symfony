<?php

namespace Symfony\Component\Workflow\Tests;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Place;
use Symfony\Component\Workflow\Transition;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{

    public function testAddPlaces()
    {
        $places = Place::fromNames(range('a', 'e'));
        $definition = new Definition($places, []);

        $this->assertCount(5, $definition->getPlaces());

        $this->assertEquals(new Place('a'), $definition->getInitialPlace());
    }

    public function testSetInitialPlace()
    {
        $places = Place::fromNames(range('a', 'e'));
        $definition = new Definition($places, []);

        $definition->setInitialPlace($places[3]);

        $this->assertEquals($places[3], $definition->getInitialPlace());
    }

    /**
     * @expectedException Symfony\Component\Workflow\Exception\LogicException
     * @expectedExceptionMessage Place "d" cannot be the initial place as it does not exist.
     */
    public function testSetInitialPlaceAndPlaceIsNotDefined()
    {
        $definition = new Definition([], []);

        $definition->setInitialPlace(new Place('d'));
    }

    public function testAddTransition()
    {
        $places = Place::fromNames(['a', 'b']);

        $transition = new Transition('name', $places[0], $places[1]);
        $definition = new Definition($places, [$transition]);

        $this->assertCount(1, $definition->getTransitions());
        $this->assertSame($transition, $definition->getTransitions()['name']);
    }

    /**
     * @expectedException Symfony\Component\Workflow\Exception\LogicException
     * @expectedExceptionMessage Place "c" referenced in transition "name" does not exist.
     */
    public function testAddTransitionAndFromPlaceIsNotDefined()
    {
        $places = Place::fromNames(['a', 'b']);

        new Definition($places, [new Transition('name', new Place('c'), $places[1])]);
    }

    /**
     * @expectedException Symfony\Component\Workflow\Exception\LogicException
     * @expectedExceptionMessage Place "c" referenced in transition "name" does not exist.
     */
    public function testAddTransitionAndToPlaceIsNotDefined()
    {
        $places = Place::fromNames(['a', 'b']);

        new Definition($places, [new Transition('name', $places[0], new Place('c'))]);
    }
}
