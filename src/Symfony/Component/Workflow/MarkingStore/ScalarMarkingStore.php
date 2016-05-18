<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Workflow\MarkingStore;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Workflow\Marking;

/**
 * ScalarMarkingStore.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ScalarMarkingStore implements MarkingStoreInterface, UniqueTransitionOutputInterface
{
    private $property;
    private $propertyAccessor;

    public function __construct($property = 'marking', PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->property = $property;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function getMarking($subject)
    {
        $place = $this->propertyAccessor->getValue($subject, $this->property);

        if (!$place) {
            return new Marking();
        }

        return new Marking(array($place => true));
    }

    public function setMarking($subject, Marking $marking)
    {
        $this->propertyAccessor->setValue($subject, $this->property, key($marking->getPlaces()));
    }
}
