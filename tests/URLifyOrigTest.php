<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class URLifyOrigTest extends TestCase
{
    public function testDowncode()
    {
        static::assertSame('  J\'etudie le francais  ', URLify::downcode('  J\'étudie le français  '));
        static::assertSame('Lo siento, no hablo espanol.', URLify::downcode('Lo siento, no hablo español.'));
        static::assertSame('FKsPWS', URLify::downcode('ΦΞΠΏΣ'));
        static::assertSame('foo-bar', URLify::filter('_foo_bar_'));
    }

    public function testFilter()
    {
        static::assertSame('jetudie-le-francais', URLify::filter('  J\'étudie le français  '));
        static::assertSame('lo-siento-no-hablo-espanol', URLify::filter('Lo siento, no hablo español.'));
        static::assertSame('fkspws', URLify::filter('ΦΞΠΏΣ'));
        static::assertSame('da-ban-ruo-jing', URLify::filter('大般若經'));
        static::assertSame('test-da-ban-ruo-jing-.txt', URLify::filter('test-大般若經.txt', 60, '', $file_name = true));
        static::assertSame('yakrhy-ltoytr', URLify::filter('ياكرهي لتويتر'));
        static::assertSame('saaat-25', URLify::filter('ساعت ۲۵'));
        static::assertSame('foto.jpg', URLify::filter('фото.jpg', 60, '', $file_name = true));
        // priorization of language-specific maps
        static::assertSame('aouaou', URLify::filter('ÄÖÜäöü', 60, 'tr'));
        static::assertSame('aeoeueaeoeue', URLify::filter('ÄÖÜäöü', 60, 'de'));

        static::assertSame('bobby-mcferrin-dont-worry-be-happy', URLify::filter("Bobby McFerrin — Don't worry be happy", 600, 'en'));
        // test stripping and conversion of UTF-8 spaces
        static::assertSame('xiang-jing-zhen-ren-test-mahito-mukai', URLify::filter('向井　真人test　(Mahito Mukai)'));
        // Treat underscore as space
        static::assertSame('text_with_underscore', URLify::filter('text_with_underscore', 60, 'en', true, true, true, false));
    }

    public function testAddChars()
    {
        static::assertSame('? (r) 1/4 1/4 3/4 P', URLify::downcode('¿ ® ¼ ¼ ¾ ¶'));
        URLify::add_chars([
            '¿' => '?', '®' => '(r)', '¼' => '1/4',
            '¼' => '1/2', '¾' => '3/4', '¶' => 'P',
        ]);
        static::assertSame('? (r) 1/2 1/2 3/4 P', URLify::downcode('¿ ® ¼ ¼ ¾ ¶'));
        URLify::reset_chars();
    }

    public function testRemoveWords()
    {
        static::assertSame('foo-bar', URLify::filter('foo bar'));
        URLify::remove_words(['foo', 'bar']);
        static::assertSame('', URLify::filter('foo bar', 200, 'en', false, true));
    }

    public function testUnknownLanguageCode()
    {
        static::assertSame('Lo siento, no hablo espanol.', URLify::downcode('Lo siento, no hablo español.', -1));
    }

    public function testRemoveWordsDisable()
    {
        URLify::remove_words(['foo', 'bar']);
        static::assertSame('foo-bar', URLify::filter('foo bar', 60, '', false, false));
    }
}
