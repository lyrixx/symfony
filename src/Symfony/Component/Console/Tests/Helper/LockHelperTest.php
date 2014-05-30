<?php

namespace Symfony\Component\Console\Tests\Helper;

use Symfony\Component\Console\Helper\LockHelper;

class LockHelperTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once __DIR__.'/../Fixtures/TestBlockingLock.php';
    }

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

    public function testBlocking()
    {
        if (!class_exists('\Thread')) {
            $this->markTestSkipped('pthreads are not supported.');
        }

        $file = sys_get_temp_dir().'/symfony-test-filesystem-blocking.lock';

        $lock = new LockHelper($file);
        $lock->lock();

        $thread = new \TestBlockingLock($file);

        $thread->start();

        $hasLock = $thread->hasLock;
        $this->assertFalse($hasLock);
        sleep(1);

        $lock->unlock();

        $thread->join();
        $hasLock = $thread->hasLock;
        $this->assertTrue($hasLock);
    }
}
