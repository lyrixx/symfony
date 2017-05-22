<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Amqp\Helper;

use Symfony\Component\Amqp\Broker;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class MessageExporter
{
    private $broker;

    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function export($queueName, $ack = false)
    {
        $this->checkQueueName($queueName);

        $messages = array();
        while (false !== $message = $this->broker->get($queueName)) {
            $messages[] = $message;
        }

        if (!$messages) {
            return;
        }

        $filename = sprintf('%s/symfony-amqp-consumer-queue-%s.tar', sys_get_temp_dir(), str_replace('.', '-', $queueName));
        $tgz = $filename.'.gz';

        // A previous phar could exist
        if (file_exists($filename)) {
            unlink($filename);
        }
        if (file_exists($tgz)) {
            unlink($tgz);
        }

        $phar = new \PharData($filename);
        foreach ($messages as $i => $message) {
            if ($ack) {
                $this->broker->ack($message, null, $queueName);
            } else {
                $this->broker->nack($message, \AMQP_REQUEUE, $queueName);
            }
            $buffer = '';
            foreach ($message->getHeaders() as $name => $value) {
                $buffer .= sprintf("%s: %s\n", $name, $value);
            }
            $buffer .= "\n";
            $buffer .= $message->getBody();
            $phar->addFromString('message-'.$i, $buffer);
        }
        $phar->compress(\Phar::GZ);

        // we can remove the phar, as we only use the gz'ed one
        unlink($filename);

        return $tgz;
    }

    protected function checkQueueName($queueName)
    {
        if ('.dead' !== substr($queueName, -5)) {
            throw new \InvalidArgumentException('Only dead queue can be exported.');
        }
    }
}
