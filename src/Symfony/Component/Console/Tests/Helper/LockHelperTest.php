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

        $lh = new LockHelper($file);
        $lh->lock();

        $this->assertFileExists($file);

        $lh->unlock();

        $this->assertFileNotExists($file);
    }

    public function testIsLockWhenNotLocked()
    {
        $file = sys_get_temp_dir().'/symfony-test-filesystem.lock';

        $lh = new LockHelper($file);

        $this->assertFalse($lh->isLocked());
    }

    public function testIsLockWhenLockedButPidIsDead()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('This feature does not work on windows.');
        }

        $file = sys_get_temp_dir().'/symfony-test-filesystem.lock';
        // Fake a lock file, with a "dead" pid
        file_put_contents($file, 999999999);

        $lh = new LockHelper($file);

        $this->assertFalse($lh->isLocked());
    }

    public function testIsLockWhenLockedButPidIsAlive()
    {
        $file = sys_get_temp_dir().'/symfony-test-filesystem.lock';
        // Fake a lock file, with a "dead" pid
        file_put_contents($file, getmypid());

        $lh = new LockHelper($file);

        $this->assertTrue($lh->isLocked());
    }
}
