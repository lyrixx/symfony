<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Locker\LockerInterface;

/**
 * LockHelper class provides helper to allow only once instance of a command to
 * run.
 *
 * WARNING: This helper works only when using one and only one host. If you have
 * several hosts, you must not use this helper.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Romain Neutron <imprec@gmail.com>
 */
class LockHelper
{
    private $file;
    private $handle;

    public function __construct($file)
    {
        $lockPath = dirname($file);

        if (!is_dir($lockPath) && !@mkdir($lockPath, 0777, true)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist and could not be created.', $lockPath));
        }

        if (!is_writable($lockPath)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable.', $lockPath));
        }

        $this->file = $file;
    }

    public function unlock()
    {
        if (is_resource($this->handle)) {
            flock($this->handle, LOCK_UN | LOCK_NB);
            ftruncate($this->handle, 0);
            fclose($this->handle);
            $this->handle = null;
        }

        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

    public function lock()
    {
        if ($this->isLocked()) {
            return false;
        }

        $this->handle = @fopen($this->file, 'a+');

        if (!is_resource($this->handle)) {
            throw new \RuntimeException(sprintf('Unable to fopen %s.', $this->file));
        }

        $locker = true;

        if (false === flock($this->handle, LOCK_EX | LOCK_NB, $locker)) {
            fclose($this->handle);

            throw new \RuntimeException(sprintf('Unable to lock %s.', $this->file));
        }

        ftruncate($this->handle, 0);
        fwrite($this->handle, (string) getmypid());
        fflush($this->handle);

        // For windows : unlock then lock shared to allow OTHER processes to
        // read the file
        flock($this->handle, LOCK_UN);
        flock($this->handle, LOCK_SH);

        return true;
    }

    private function isLocked()
    {
        $handle = @fopen($this->file, 'r');

        // the file does not exist
        if (!is_resource($handle)) {
            return false;
        }

        $locker = true;
        if (false === flock($handle, LOCK_EX | LOCK_NB, $locker)) {
            // exclusive lock failed, another process is locking
            fclose($handle);

            return true;
        }

        flock($handle, LOCK_UN);
        fclose($handle);

        // lock succeed, anyway, some systems may not support this very well,
        // let's check for a Pid running
        $pid = file_get_contents($this->file);

        if (function_exists('posix_kill')) {
            $running = posix_kill($pid, 0);
        } elseif (function_exists('exec') && !defined('PHP_WINDOWS_VERSION_BUILD')) {
            exec(sprintf('kill -0 %d', $pid), $output, $returnValue);
            $running = 0 === $returnValue;
        } elseif (function_exists(('exec')) && defined('PHP_WINDOWS_VERSION_BUILD')) {
            exec(sprintf('tasklist /FI "PID eq %d"', $pid), $output, $returnValue);
            $running = 0 === $returnValue;
        } else {
            $running = false;
        }

        return $running;
    }
}
