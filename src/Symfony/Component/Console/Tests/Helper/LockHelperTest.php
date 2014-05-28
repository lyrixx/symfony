<?php

namespace Symfony\Component\Console\Tests\Helper;

use Symfony\Component\Console\Helper\LockHelper;

class LockHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The directory "/a/b/c/d/e" does not exist and could not be created.
     */
    public function testConstructWhenRepositoryDoesNotExist()
    {
        new LockHelper('/a/b/c/d/e/f');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The directory "/" is not writable.
     */
    public function testConstructWhenRepositoryIsNotWriteable()
    {
        new LockHelper('/');
    }

    public function testLockUnlock()
    {
        $file = sys_get_temp_dir().'/symfony-test-filesystem.lock';

        $l1 = new LockHelper($file);
        $l2 = new LockHelper($file);

        $this->assertTrue($l1->lock());
        $this->assertFalse($l2->lock());
        $l1->unlock();

        $this->assertTrue($l2->lock());
        $l2->unlock();
    }
}
