<?php

namespace Symfony\Component\Amqp\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Amqp\Exchange;
use Symfony\Component\Amqp\Test\AmqpTestTrait;

class ExchangeTest extends TestCase
{
    use AmqpTestTrait;

    public function getUri()
    {
        return array(
            array('exchange_name=test_ex.default', 'test_ex.default', \AMQP_EX_TYPE_DIRECT, \AMQP_DURABLE),
            array('exchange_name=test_ex.fanout_durable&type=fanout&flags=2', 'test_ex.fanout_durable', \AMQP_EX_TYPE_FANOUT, \AMQP_DURABLE),
        );
    }

    /** @dataProvider getUri */
    public function testCreateFromUri($qsa, $name, $type, $flags)
    {
        $exchange = Exchange::createFromUri(getenv('RABBITMQ_URL').'?'.$qsa);

        $this->assertInstanceOf(Exchange::class, $exchange);
        $this->assertEquals($name, $exchange->getName());
        $this->assertEquals($type, $exchange->getType());
        $this->assertEquals($flags, $exchange->getFlags());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The "exchange_name" must be part of the query string.
     */
    public function testCreateFromUriWithInvalidUri()
    {
        $exchange = Exchange::createFromUri(getenv('RABBITMQ_URL').'/?type=fanout');
    }

    public function testPublish()
    {
        $name = 'test_exchange.publish';

        $exchange = new Exchange($this->createChannel(), $name);

        $queue = $this->createQueue($name);
        $queue->bind($name, $name);

        $this->emptyQueue($name);

        $message = json_encode(microtime(true));
        $exchange->publish($message, $name, \AMQP_MANDATORY, array('content_type' => 'application/json'));

        $this->assertQueueSize(1, $name);
        $message = $this->assertNextMessageBody($message, $name);
        $this->assertSame('application/json', $message->getContentType());
    }
}
