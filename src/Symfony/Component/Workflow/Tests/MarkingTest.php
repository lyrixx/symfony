<?php

namespace Symfony\Component\Workflow\Tests;

use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\Place;

class MarkingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideMethodByType
     */
    public function testConstructor($methods, $places)
    {
        $marking = $this->{$methods['create']}();

        $this->assertTrue($marking->{$methods['has']}($places['a']));
        $this->assertFalse($marking->{$methods['has']}($places['b']));
        $this->assertSame(['a' => 1], $marking->getPlaces());

        $marking->{$methods['mark']}($places['b']);

        $this->assertTrue($marking->{$methods['has']}($places['a']));
        $this->assertTrue($marking->{$methods['has']}($places['b']));
        $this->assertSame(['a' => 1, 'b' => 1], $marking->getPlaces());

        $marking->{$methods['unmark']}($places['a']);

        $this->assertFalse($marking->{$methods['has']}($places['a']));
        $this->assertTrue($marking->{$methods['has']}($places['b']));
        $this->assertSame(['b' => 1], $marking->getPlaces());

        $marking->{$methods['unmark']}($places['b']);

        $this->assertFalse($marking->{$methods['has']}($places['a']));
        $this->assertFalse($marking->{$methods['has']}($places['b']));
        $this->assertSame([], $marking->getPlaces());
    }
    
    public function provideMethodByType()
    {
        return [
            [[
                'create' => 'provideMarkingByNew',
                'mark' => 'mark',
                'unmark' => 'unmark',
                'has' => 'has',
            ], [
                'a' => new Place('a'),
                'b' => new Place('b'),
            ]],
            [[
                'create' => 'provideMarkingStaticConstructor',
                'mark' => 'markPlaceNamed',
                'unmark' => 'unmarkPlaceNamed',
                'has' => 'hasPlaceNamed',
            ], [
                'a' => 'a',
                'b' => 'b',
            ]],
        ];
    }
    
    private function provideMarkingByNew()
    {
        return new Marking(['a' => 1]);
    }
    
    private function provideMarkingStaticConstructor()
    {
        return Marking::fromPlaces([new Place('a')]);
    }

}
