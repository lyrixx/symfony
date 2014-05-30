<?php

use Symfony\Component\Console\Helper\LockHelper;

/**
 * Tests blocking lock using pthreads
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class TestBlockingLock extends \Thread
{
    private $lockFile;
    public $hasLock;

    public function __construct($lockFile)
    {
       $this->lockFile = $lockFile;
       $this->hasLock = false;
    }

    public function run()
    {
        $lock = new LockHelper($this->lockFile);
        if ($lock->lock(true)) {
            $this->hasLock = true;
        }

        $lock->unlock();
    }
} 