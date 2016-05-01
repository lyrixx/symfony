<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Workflow;

use Symfony\Component\Workflow\Exception\LogicException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Definition
{
    /**
     * @var Place[]
     */
    private $places = array();

    /**
     * @var Transition[]
     */
    private $transitions = array();

    /**
     * @var Place
     */
    private $initialPlace;


    /**
     * Definition constructor.
     * @param Place[] $places
     * @param Transition[] $transitions
     */
    public function __construct(array $places, array $transitions)
    {
        $this->addPlaces($places);
        $this->addTransitions($transitions);
    }

    /**
     * @return Place|null
     */
    public function getInitialPlace()
    {
        return $this->initialPlace;
    }

    /**
     * @return Place[]|null
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @return Transition[]|null
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * @param Place $place
     */
    public function setInitialPlace(Place $place)
    {
        if (!isset($this->places[$place->getName()])) {
            throw new LogicException(sprintf('Place "%s" cannot be the initial place as it does not exist.', $place->getName()));
        }

        $this->initialPlace = $place;
    }

    /**
     * @param Place $place
     */
    public function addPlace(Place $place)
    {
        if (!count($this->places)) {
            $this->initialPlace = $place;
        }

        $this->places[$place->getName()] = $place;
    }

    /**
     * @param Place[] $places
     */
    public function addPlaces(array $places)
    {
        foreach ($places as $place) {
            $this->addPlace($place);
        }
    }

    /**
     * @param Transition[] $transitions
     * @throws LogicException
     */
    public function addTransitions(array $transitions)
    {
        foreach ($transitions as $transition) {
            $this->addTransition($transition);
        }
    }

    /**
     * @param Transition $transition
     * @throws LogicException
     */
    public function addTransition(Transition $transition)
    {
        $name = $transition->getName();

        foreach ($transition->getFroms() as $from) {
            if (!isset($this->places[$from->getName()])) {
                throw new LogicException(sprintf('Place "%s" referenced in transition "%s" does not exist.', $from, $name));
            }
        }

        foreach ($transition->getTos() as $to) {
            if (!isset($this->places[$to->getName()])) {
                throw new LogicException(sprintf('Place "%s" referenced in transition "%s" does not exist.', $to, $name));
            }
        }

        $this->transitions[$name] = $transition;
    }
}
