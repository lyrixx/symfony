<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Tests\Transliterator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Intl\Transliterator\EmojiTransliterator;

/**
 * @requires extension intl
 */
final class EmojiTransliteratorTest extends TestCase
{
    public function provideTransliterateTests(): iterable
    {
        yield [
            'fr',
            'un 😺, 🐈‍⬛, et a 🦁 vont au 🏞️',
            'un chat qui sourit, chat noir, et a tête de lion vont au parc national️',
        ];
        yield [
            'en',
            'a 😺, 🐈‍⬛, and a 🦁 go to 🏞️... 😍 🎉 💛',
            'a grinning cat, black cat, and a lion go to national park️... smiling face with heart-eyes party popper yellow heart',
        ];

        $specialArrowInput = '↔ - ↔️'; // The first arrow is particularly problematic!
        yield [
            'en',
            $specialArrowInput,
            'left-right arrow - left-right arrow️',
        ];
        yield [
            'fr',
            $specialArrowInput,
            'flèche gauche droite - flèche gauche droite️',
        ];
    }

    /** @dataProvider provideTransliterateTests */
    public function testTransliterate(string $locale, string $input, string $expected)
    {
        $tr = EmojiTransliterator::create($locale);

        $this->assertSame($expected, $tr->transliterate($input));
    }

    public function provideLocaleTest(): iterable
    {
        $file = (new Finder())
            ->in(__DIR__.'/../../Resources/data/transliterator/emoji')
            ->name('*.php')
            ->files()
        ;

        foreach ($file as $file) {
            yield [$file->getBasename('.php')];
        }
    }

    /** @dataProvider provideLocaleTest */
    public function testAllTransliterator(string $locale)
    {
        $tr = EmojiTransliterator::create($locale);

        $this->assertNotEmpty($tr->transliterate('😀'));
    }

    public function testTransliterateWithInvalidLocale()
    {
        $this->iniSet('intl.use_exceptions', '1');
        $this->expectException(\IntlException::class);
        $this->expectExceptionMessage('transliterator_create: unable to open ICU transliterator with id "invalid"');

        EmojiTransliterator::create('invalid');
    }

    public function testListIds()
    {
        $this->assertContains('en_ca', EmojiTransliterator::listIDs());
        $this->assertNotContains('..', EmojiTransliterator::listIDs());
    }
}
