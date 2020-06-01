<?php

/**
 * Class URLifyTest
 *
 * @internal
 */
final class URLifyTest extends \PHPUnit\Framework\TestCase
{
    public function testSlugifyOptions()
    {
        $input = ' a+A+ - a+A_a _';
        $output = URLify::filter($input, 200, 'de', false, true, true, '_');

        static::assertSame('a_plus_a_plus_a_plus_a_a', $output);
    }

    public function testSlugifyOptionsV2()
    {
        $input = ' a+A+ - a+A_a _ ♥';
        $output = URLify::filter($input, 200, 'ar', false, true, true, '_');

        static::assertSame('a_zayd_a_zayd_a_zayd_a_a_hb', $output);
    }

    public function testDowncode()
    {
        $testArray = [
            '  J\'étudie le français  '                             => '  J\'etudie le francais  ',
            'Lo siento, no hablo español.'                          => 'Lo siento, no hablo espanol.',
            '$1 -> %1 -> öäü -> ΦΞΠΏΣ -> 中文空白 -> 💩 '                => ' 1 Dollar -> Prozent 1 -> oeaeue -> FKsPWS -> Zhong Wen Kong Bai  ->  ',
            ' 22.99 € oder $ 19 | 1 $ | $ 1 = foobar'               => ' 22 Euro 99 Cent oder 19 Dollar | 1  Dollar  | 1 Dollar gleich foobar',
            'זאת השפה העברית.‏'                                     => 'zAt hSHph h`bryt.',
            '𐭠 𐭡 𐭢 𐭣 𐭤 𐭥 𐭦 𐭧 𐭨 𐭩 𐭪 𐭫 𐭬 𐭭 𐭮 𐭯 𐭰 𐭱 𐭲 𐭸 𐭹 𐭺 𐭻 𐭼 𐭽 𐭾 𐭿' => '                          ',
            'أحبك'                                                  => 'ahbk',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, URLify::downcode($before, 'de'), $before);
            static::assertSame($after, URLify::transliterate($before, 'de'), $before);
        }

        static::assertSame('FKsPWS, Zhong Wen Kong Bai ', URLify::downcode('ΦΞΠΏΣ, 中文空白', 'de', 'X'));
        static::assertSame('FKsPWS, Zhong Wen Kong Bai ', URLify::downcode('ΦΞΠΏΣ, 中文空白', 'de', ''));
    }

    public function testRemoveWordsDisable()
    {
        URLify::remove_words(['foo', 'bar']);
        static::assertSame('foo-bar', URLify::filter('foo bar'));
        URLify::reset_remove_list();
    }

    public function testRemoveWordsEnabled()
    {
        URLify::remove_words(['foo', 'bar'], 'en');
        static::assertSame('foo-bar', URLify::filter('foo bar', 10, 'de', false, true));
        URLify::reset_remove_list('en');

        URLify::remove_words(['foo', 'bar'], 'en');
        static::assertSame('', URLify::filter('foo bar', 10, 'en', false, true));
        URLify::reset_remove_list('en');

        URLify::remove_words(['foo', 'bar'], 'de');
        static::assertSame('', URLify::filter('foo bar', 10, 'de', false, true));
        URLify::reset_remove_list('de');

        URLify::remove_words(['foo', 'bär'], 'de');
        static::assertSame('bar', URLify::filter('foo bar', 10, 'de', false, true));
        URLify::reset_remove_list('de');
    }

    public function testDefaultFilter()
    {
        $testArray = [
            '  J\'étudie le français  '                                                    => 'Jetudie-le-francais',
            'Lo siento, no hablo español.'                                                 => 'Lo-siento-no-hablo-espanol',
            '—ΦΞΠΏΣ—Test—'                                                                 => 'FKsPWS-Test',
            '大般若經'                                                                         => 'Da-Ban-Ruo-Jing',
            'ياكرهي لتويتر'                                                                => 'yakrhy-ltoytr',
            'ساعت ۲۵'                                                                      => 'saaat-25',
            "test\xe2\x80\x99öäü"                                                          => 'test-oeaeue',
            'Ɓtest'                                                                        => 'Btest',
            '-ABC-中文空白'                                                                    => 'ABC-Zhong-Wen-Kong-Bai',
            ' '                                                                            => '',
            ''                                                                             => '',
            '1 ₣ || ä#ü'                                                                   => '1-French-franc-aeue',
            '∆ € $ Þ λ  I am A web Develópêr'                                              => 'Unterschied-Euro-Dollar-TH-l-I-am-A-web-Developer',
            '<strong>Subject<BR class="test">from a<br style="clear:both;" />CMS</strong>' => 'Subject-from-a-CMS',
            'that it\'s \'eleven\' \'o\'clock\''                                           => 'that-its-eleven-oclock',
        ];

        for ($i = 0; $i < 10; ++$i) { // increase this value to test the performance
            foreach ($testArray as $before => $after) {
                static::assertSame($after, URLify::filter($before, 200, 'de', false, false, false, '-'), $before);
            }
        }

        // test static cache
        static::assertSame('foo-bar', URLify::filter('_foo_bar_'));
        static::assertSame('foo-bar', URLify::filter('_foo_bar_'));

        // test no language
        static::assertSame('foo-bar', URLify::filter('_foo_bar_', -1, ''));

        // test no "separator"
        static::assertSame('foo-bar', URLify::filter('_foo_bar_', -1, 'de', false, false, false, ''));

        // test new "separator"
        static::assertSame('foo_bar', URLify::filter('_foo_bar_', -1, 'de', false, false, false, '_'));

        // test default "separator"
        static::assertSame('foo-bar', URLify::filter('_foo_bar_', -1, 'de', false, false, false));
    }

    public function testFilterLanguage()
    {
        $testArray = [
            'abz'        => ['أبز' => 'ar'],
            ''           => ['' => 'ar'],
            'testoeaeue' => ['testöäü' => 'de'],
        ];

        foreach ($testArray as $after => $beforeArray) {
            foreach ($beforeArray as $before => $lang) {
                static::assertSame($after, URLify::filter($before, 60, $lang), $before);
            }
        }
    }

    public function testFilterFile()
    {
        $testArray = [
            'test-eDa-Ban-Ruo-Jing-.txt'             => "test-\xe9\x00\x0é大般若經.txt",
            'test-Da-Ban-Ruo-Jing-.txt'              => 'test-大般若經.txt',
            'foto.jpg'                               => 'фото.jpg',
            'Foto.jpg'                               => 'Фото.jpg',
            'oeaeue-test'                            => 'öäü  - test',
            'SHdgSHdg.png'                           => 'שדגשדג.png',
            'c-r-aaaaaeaaeOOOOOe141234SSucdthu-.jpg' => '—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–.jpg',
            '000-c-c-.txt'                           => '000—©—©.txt',
            ''                                       => ' ',
        ];

        foreach ($testArray as $after => $before) {
            static::assertSame($after, URLify::filter($before, 60, 'de', true, false, false, '-'), $before);
        }

        // clean file-names
        static::assertSame('foto.jpg', URLify::filter('Фото.jpg', 60, 'de', true, false, true));
    }

    public function testFilter()
    {
        static::assertSame('aeoeueaeoeue-der-und-aeoeueaeoeue', URLify::filter('ÄÖÜäöü&amp;der & ÄÖÜäöü', 60, 'de', false));
        static::assertSame('aeoeueaeoeue-der', URLify::filter('ÄÖÜäöü-der', 60, 'de', false));
        static::assertSame('aeoeueaeoeue der', URLify::filter('ÄÖÜäöü-der', 60, 'de', false, false, true, ' '));
        static::assertSame('aeoeueaeoeue#der', URLify::filter('####ÄÖÜäöü-der', 60, 'de', false, false, true, '#'));
        static::assertSame('aeoeueaeoeue', URLify::filter('ÄÖÜäöü-der-die-das', 60, 'de', false, true));
        static::assertSame('bobby-mcferrin-dont-worry-be-happy', URLify::filter('Bobby McFerrin — Don\'t worry be happy', 600, 'en'));
        static::assertSame('ouaou', URLify::filter('ÖÜäöü', 60, 'tr'));
        static::assertSame('hello-zs-privet', URLify::filter('hello žš, привет', 60, 'ru'));

        // test stripping and conversion of UTF-8 spaces
        static::assertSame('xiang-jing-zhen-ren-test-mahito-mukai', URLify::filter('向井　真人test　(Mahito Mukai)'));
    }

    public function testFilterAllLanguages()
    {
        static::assertSame('d-shh-l-c-r-aaaaaeaaeoooooe141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'de'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'latin'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'latin_symbols'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'el'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'tr'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'ru'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'uk'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'cs'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'pl'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'ro'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'lv'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'lt'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'vn'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'ar'));
        static::assertSame('dj-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'sr'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'az'));
        static::assertSame('d-shh-l-c-r-aaaaaaaeooooo141234ssucdthu', URLify::filter('Đ-щ-λ—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–', -1, 'other'));
    }

    public function testAddArrayToSeparator()
    {
        static::assertSame('r-14-14-34-test-p', URLify::filter('¿ ® ¼ ¼ ¾ test ¶'));

        URLify::add_array_to_separator(
            [
                '/®/',
                '/tester/',
            ]
        );
        static::assertSame('14-14-34-p-abc', URLify::filter('? ¿ >-< &amp; ® ¼ ¼ ¾ ¶ <br> ; ! abc'));
        URLify::reset_array_to_separator();

        // merge

        URLify::add_array_to_separator(
            [
                '/®/',
                '/tester/',
            ],
            false
        );
        static::assertSame('and-amp-14-14-34-p-abc', URLify::filter('? ¿ >-< &amp; ® ¼ ¼ ¾ ¶ <br> ; ! abc'));
        URLify::reset_array_to_separator();
    }

    public function testAddChars()
    {
        static::assertSame('? (r) 1/4 1/4 3/4 P', URLify::downcode('¿ ® ¼ ¼ ¾ ¶', 'latin', '?'));

        URLify::add_chars(
            [
                '¿' => '?',
                '®' => '(r)',
                '¼' => '1/4',
                '¾' => '3/4',
                '¶' => 'p',
            ]
        );
        static::assertSame('? (r) 1/4 1/4 3/4 p', URLify::downcode('¿ ® ¼ ¼ ¾ ¶'));

        URLify::reset_chars();
    }

    public function testRemoveWords()
    {
        static::assertSame('foo-bar', URLify::filter('foo bar', 60, 'de', false, true));

        // append (array) v1
        URLify::remove_words(
            [
                'foo',
                'bar',
            ],
            'de',
            true
        );
        static::assertSame('', URLify::filter('foo bar', 60, 'de', false, true));

        // append (array) v2
        URLify::remove_words(
            [
                'foo/bar',
                '\n',
            ],
            'de',
            true
        );
        static::assertSame('lall-n', URLify::filter('foo / bar lall \n', 60, 'de', false, true));

        // append (string)
        URLify::remove_words('lall', 'de', true);
        static::assertSame('123', URLify::filter('foo bar lall 123 ', 60, 'de', false, true));

        // reset
        URLify::reset_remove_list('de');

        // replace
        static::assertSame('foo-bar', URLify::filter('foo bar', 60, 'de', false, true));
        URLify::remove_words(
            [
                'foo',
                'bar',
            ],
            'de',
            false
        );
        static::assertSame('', URLify::filter('foo bar', 60, 'de', false, true));

        // reset
        URLify::reset_remove_list('de');
    }

    public function testManyRoundsWithUnknownLanguageCode()
    {
        $result = [];
        for ($i = 0; $i < 100; ++$i) {
            $result[] = URLify::downcode('Lo siento, no hablo español.', $i);
        }

        foreach ($result as $res) {
            static::assertSame('Lo siento, no hablo espanol.', $res);
        }
    }

    public function testUrlSlug()
    {
        $tests = [
            '  -ABC-中文空白-  ' => 'abc-zhong-wen-kong-bai',
            '      - ÖÄÜ- '  => 'oau',
            'öäü'            => 'oau',
            ''               => '',
            ' test test'     => 'test-test',
            'أبز'            => 'abz',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, URLify::filter($before, 100, 'latin', false, true, true, '-'), 'tested: ' . $before);
        }

        $tests = [
            '  -ABC-中文空白-  ' => 'abc',
            '      - ÖÄÜ- '  => 'oau',
            '  öäüabc'       => 'oaua',
            ' DÃ¼sseldorf'   => 'da14', // "duss" with fixes UTF-8 see "Portable UTF-8"
            'Abcdef'         => 'abcd',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, URLify::filter($before, 4, 'latin', false, true, true, '-'), $before);
        }

        // ---

        $tests = [
            '  -ABC-中文空白-  ' => 'abc',
            '      - ÖÄÜ- '  => 'oeae',
            '  öäüabc'       => 'oeae',
            ' DÃ¼sseldorf'   => 'da14', // "duss" with fixes UTF-8 see "Portable UTF-8"
            'Abcdef'         => 'abcd',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, URLify::filter($before, 4, 'de', false, true, true, '-'), $before);
        }

        // ---

        $tests = [
            'Facebook bekämpft erstmals Durchsuchungsbefehle'       => 'facebook-bekaempft-erstmals-durchsuchungsbefehle',
            '123  -ABC-中文空白-  '                                     => '123-abc-zhong-kong-bai',
            '      - ÖÄÜ- '                                         => 'oeaeue',
            'öäü'                                                   => 'oeaeue',
            '$1 -> %1 -> öäü -> ΦΞΠΏΣ -> 中文空白 -> 💩 '                => '1-dollar-prozent-1-oeaeue-fkspws-zhong-kong-bai',
            'זאת השפה העברית.‏'                                     => 'zat-hshph-h-bryt',
            '𐭠 𐭡 𐭢 𐭣 𐭤 𐭥 𐭦 𐭧 𐭨 𐭩 𐭪 𐭫 𐭬 𐭭 𐭮 𐭯 𐭰 𐭱 𐭲 𐭸 𐭹 𐭺 𐭻 𐭼 𐭽 𐭾 𐭿' => '',
            'أحبك'                                                  => 'ahbk',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, URLify::filter($before, 100, 'de', false, true, true, '-'), $before);
        }

        $invalidTest = [
            // Min/max overlong
            "\xC0\x80a"                 => 'Overlong representation of U+0000 | 1',
            "\xE0\x80\x80a"             => 'Overlong representation of U+0000 | 2',
            "\xF0\x80\x80\x80a"         => 'Overlong representation of U+0000 | 3',
            "\xF8\x80\x80\x80\x80a"     => 'Overlong representation of U+0000 | 4',
            "\xFC\x80\x80\x80\x80\x80a" => 'Overlong representation of U+0000 | 5',
            "\xC1\xBFa"                 => 'Overlong representation of U+007F | 6',
            "\xE0\x9F\xBFa"             => 'Overlong representation of U+07FF | 7',
            "\xF0\x8F\xBF\xBFa"         => 'Overlong representation of U+FFFF | 8',
            "a\xDF"                     => 'Incomplete two byte sequence (missing final byte) | 9',
            "a\xEF\xBF"                 => 'Incomplete three byte sequence (missing final byte) | 10',
            "a\xF4\xBF\xBF"             => 'Incomplete four byte sequence (missing final byte) | 11',
            // Min/max continuation bytes
            "a\x80" => 'Lone 80 continuation byte | 12',
            "a\xBF" => 'Lone BF continuation byte | 13',
            // Invalid bytes (these can never occur)
            "a\xFE" => 'Invalid FE byte | 14',
            "a\xFF" => 'Invalid FF byte | 15',
        ];

        foreach ($invalidTest as $test => $note) {
            $result = URLify::filter($test);
            static::assertTrue($result === 'a' || $result === '', $note);
        }

        // ---

        $tests = [
            'Facebook bekämpft erstmals / Durchsuchungsbefehle' => 'facebook/bekaempft/erstmals/durchsuchungsbefehle',
            '  -ABC-中文空白-  '                                    => 'abc/zhong/kong/bai',
            '    #  - ÖÄÜ- '                                    => 'oeaeue',
            'öä \nü'                                            => 'oeae/nue',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, URLify::filter($before, 100, 'de_ch', false, true, true, '/'), $before);
        }

        // ---

        $tests = [
            'Facebook bekämpft erstmals / Durchsuchungsbefehle' => 'facebook/bekampft/erstmals/durchsuchungsbefehle',
            '  -ABC-中文空白-  '                                    => 'abc/zhong/wen/kong/bai',
            '    #  - ÖÄÜ- '                                    => 'oau',
            'öä \nü'                                            => 'oa/nu',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, URLify::filter($before, 100, 'ru', false, true, true, '/'), $before);
        }
    }

    public function testGetRemoveList()
    {
        // reset
        URLify::reset_remove_list('de');

        $test = new URLify();

        $removeArray = $this->invokeMethod($test, 'get_remove_list', ['de']);
        static::assertIsArray($removeArray);
        static::assertTrue(\in_array('ein', $removeArray, true));

        $removeArray = $this->invokeMethod($test, 'get_remove_list', ['']);
        static::assertIsArray($removeArray);
        static::assertFalse(\in_array('ein', $removeArray, true));

        // reset
        URLify::reset_remove_list('de');
    }

    public function testUnknownLanguageCode()
    {
        for ($i = 0; $i < 100; ++$i) {
            static::assertSame('Lo siento, no hablo espanol.', URLify::downcode('Lo siento, no hablo español.', -1));
            URLify::downcode('Lo siento, no hablo español.');
        }
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @throws \ReflectionException
     *
     * @return mixed method return
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(\get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
