<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Transliterator;

final class EmojiTransliterator extends \Transliterator
{
    private array $map;

    public static function create(string $id, int $direction = self::FORWARD): ?\Transliterator
    {
        if (self::REVERSE === $direction) {
            return \Transliterator::createFromRules('A > B')->createInverse();
        }
        $id = strtolower($id);

        if (!preg_match('/^[a-z0-9@_\\.\\-]*$/', $id)) {
            return \Transliterator::create($id);
        }

        if (!is_file(\dirname(__DIR__)."/Resources/data/transliterator/emoji/{$id}.php")) {
            return \Transliterator::create($id);
        }

        $instance = unserialize(sprintf('O:%d:"%s":1:{s:2:"id";s:%d:"%s";}', \strlen(self::class), self::class, \strlen($id), $id));
        $instance->map = require \dirname(__DIR__)."/Resources/data/transliterator/emoji/{$id}.php";

        return $instance;
    }

    public function createInverse(): ?self
    {
        return \Transliterator::createFromRules('A > B')->createInverse();
    }

    public function getErrorCode(): int|false
    {
        return $this->transliterator?->getErrorCode() ?? false;
    }

    public function getErrorMessage(): string|false
    {
        return $this->transliterator->getErrorMessage() ?? false;
    }

    public static function listIDs(): array|false
    {
        static $ids;

        $ids = [];

        foreach (scandir(\dirname(__DIR__).'/Resources/data/transliterator/emoji/') as $file) {
            if (str_ends_with($file, '.php')) {
                $ids[] = substr($file, 0, -4);
            }
        }

        return $ids;
    }

    public function transliterate(string $string, int $start = 0, int $end = -1): string|false
    {
        static $cookie;
        static $transliterator;

        $cookie ??= md5(random_bytes(8));
        $transliterator ??= \Transliterator::createFromRules('[:any:]* > '.$cookie);

        if (false === $result = $transliterator->transliterate($string, $start, $end)) {
            return false;
        }
        if (4 > \strlen($string)) {
            return $string;
        }
        $map = $this->map;

        if ($cookie === $result) {
            return str_replace($map[0], $map[1], $string);
        }

        $parts = explode($cookie, $result);
        $start = \strlen($parts[0]);
        $length = -\strlen($parts[1]);

        return $parts[0].str_replace($map[0], $map[1], substr($string, $start, $length)).$parts[1];
    }
}
