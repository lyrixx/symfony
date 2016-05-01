<?php

namespace Symfony\Component\Workflow\Tests;


use Symfony\Component\Workflow\Place;

class PlaceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Symfony\Component\Workflow\Exception\InvalidArgumentException
     * @expectedExceptionMessage The place "foo.bar" contains invalid characters.
     */
    public function testValidateName()
    {
        new Place('foo.bar');
    }
}
