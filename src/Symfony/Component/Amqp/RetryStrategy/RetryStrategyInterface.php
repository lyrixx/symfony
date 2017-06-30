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
interface RetryStrategyInterface
{
    /**
     * @param \AMQPEnvelope $msg
     *
     * @return bool
     */
    public function isRetryable(\AMQPEnvelope $msg);

    /**
     * @param \AMQPEnvelope $msg
     *
     * @return int
     */
    public function getWaitingTime(\AMQPEnvelope $msg);
}
