<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Amqp;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class UrlParser
{
    /**
     * This class should not be instantiated.
     */
    private function __construct()
    {
    }

    public static function parseUrl($url)
    {
        $parts = parse_url($url);

        return array(
            'host' => isset($parts['host']) ? $parts['host'] : 'localhost',
            'login' => isset($parts['user']) ? $parts['user'] : 'guest',
            'password' => isset($parts['pass']) ? $parts['pass'] : 'guest',
            'port' => isset($parts['port']) ? $parts['port'] : 5672,
            'vhost' => isset($parts['path'][1]) ? substr($parts['path'], 1) : '/',
        );
    }
}
