<?php

namespace Symfony\Component\Filesystem\Tests;

use Symfony\Component\Filesystem\LockHandler;

class LockHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The directory "/a/b/c/d/e" does not exist and could not be created.
     */
    public function testConstructWhenRepositoryDoesNotExist()
    {
        new LockHandler('/a/b/c/d/e/f');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The directory "/" is not writable.
     */
    public function testConstructWhenRepositoryIsNotWriteable()
    {
        new LockHandler('/');
    }

    public function testLockUnlock()
    {
        $file = sys_get_temp_dir().'/symfony-test-filesystem.lock';

        $l1 = new LockHandler($file);
        $l2 = new LockHandler($file);

        $this->assertTrue($l1->lock());
        $this->assertFalse($l2->lock());
        $l1->unlock();

        $this->assertTrue($l2->lock());
        $l2->unlock();
    }
}
