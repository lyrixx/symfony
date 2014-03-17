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

/**
 * LockHelper class provides helper to allow only once instance of a command to
 * run.
 *
 * WARNING: This helper works only when using one and only one host. If you have
 * several hosts, you must not use this helper.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Romain Neutron <imprec@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
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

    public function lock($blocking = false)
    {
        if ($this->handle) {
            return true;
        }

        // Set an error handler to not trigger the registered error handler if
        // the file can not be open.
        set_error_handler('var_dump', 0);
        $this->handle = @fopen($this->file, 'a+');
        restore_error_handler();

        if (!$this->handle) {
            throw new \RuntimeException(sprintf('Unable to fopen "%s".', $this->file));
        }

        // On Windows, even if PHP doc says the contrary, LOCK_NB works, see
        // https://bugs.php.net/54129
        if (!flock($this->handle, LOCK_EX | ($blocking ? 0 : LOCK_NB))) {
            fclose($this->handle);
            $this->handle = null;

            return false;
        }

        return true;
    }

    public function unlock()
    {
        if ($this->handle) {
            flock($this->handle, LOCK_UN | LOCK_NB);
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
