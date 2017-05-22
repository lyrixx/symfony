<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Amqp\RetryStrategy;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ConstantRetryStrategy implements RetryStrategyInterface
{
    private $time;
    private $max;

    /**
     * @param int $time Time to wait in the queue in seconds
     * @param int $max  The maximum number of attempts (0 means no limit)
     */
    public function __construct($time, $max = 0)
    {
        $time = (int) $time;

        if ($time < 1) {
            throw new \InvalidArgumentException('"time" should be at least 1.');
        }

        $this->time = $time;
        $this->max = $max;
    }

    public function isRetryable(\AMQPEnvelope $msg)
    {
        return $this->max ? $msg->getHeader('retries') < $this->max : true;
    }

    public function getWaitingTime(\AMQPEnvelope $msg)
    {
        return $this->time;
    }
}
