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

/**
 * Marking contains the place of every tokens.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
/**
 * Class Marking
 * @package Symfony\Component\Workflow
 */
class Marking
{
    /**
     * @var string[]
     */
    private $places = array();

    /**
     * @param string[] $places keys are the place name and values should be 1
     */
    public function __construct(array $places = array())
    {
        foreach ($places as $place => $nbToken) {
            $this->markPlaceNamed($place);
        }
    }

    /**
     * @param Place[] $places
     * @return Marking
     */
    public static function fromPlaces(array $places = array())
    {
        $placeNames = [];
        foreach($places as $place) {
            $placeNames[$place->getName()] = 1;
        }
        return new self($placeNames);
    }

    /**
     * @param Place $place place
     */
    public function mark(Place $place)
    {
        $this->places[$place->getName()] = 1;
    }

    /**
     * @param Place $place place
     */
    public function unmark(Place $place)
    {
        unset($this->places[$place->getName()]);
    }

    /**
     * @param string $placename place name
     */
    public function markPlaceNamed($placeName)
    {
        $this->places[$placeName] = 1;
    }

    /**
     * @param string $placeName place name
     */
    public function unmarkPlaceNamed($placeName)
    {
        unset($this->places[$placeName]);
    }

    /**
     * @param Place $place
     * @return bool
     */
    public function has(Place $place)
    {
        return isset($this->places[$place->getName()]);
    }

    /**
     * @param string $place place name
     * @return bool
     */
    public function hasPlaceNamed($name)
    {
        return isset($this->places[$name]);
    }

    /**
     * @return Place[]
     */
    public function getPlaces()
    {
        return $this->places;
    }
}
