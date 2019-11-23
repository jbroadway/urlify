<?php declare(strict_types=1);

/**
 * Class Utf8UrlSlugTest
 *
 * @internal
 */
final class Utf8UrlSlugTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'testiñg test.';
        static::assertSame('testing-test', URLify::filter($str));
    }

    public function testAscii()
    {
        $str = 'testing - test';
        static::assertSame('testing-test', URLify::filter($str));
    }

    public function testMulti()
    {
        $str = "川 đņ ōķ ôõ ö+ ÷ø ųú ûü ũū˙ ^ foo \0 \x1 \\";

        static::assertSame('chuan-dn-ok-oo-o-plus-o-uu-uu-uu-foo', URLify::filter($str));
        static::assertSame('chuan-dn-ok-oo-o-o-uu-uu-uu-foo', URLify::filter($str, 0, 'by'));

        static::assertSame('chuan-dn-ok-oo-oe-plus-o-uu-uue-uu-foo', URLify::filter($str, 0, 'de'));
        static::assertSame('chuan-dn-ok-oo-oe-plus-o-uu-uue-uu-foo', URLify::filter($str, 0, 'de'));
    }

    public function testXss()
    {
        $str = '<script>alert(\'lall\')</script><svg onload=alert(1)>';
        static::assertSame('alert-lall', URLify::filter($str));
    }

    public function testInvalidChar()
    {
        $str = "tes\xE9ting";
        static::assertSame('testing', URLify::filter($str));

        //$str = 'W%F6bse';
        //static::assertSame('Woebse', URLify::filter($str, 200, 'de', false, false, false, '-'));
    }

    public function testEmptyStr()
    {
        $str = '';
        static::assertEmpty(URLify::filter($str));
    }

    public function testNulAndNon7Bit()
    {
        $str = "a\x00ñ\x00c";
        static::assertSame('anc', URLify::filter($str));
    }

    public function testNul()
    {
        $str = "a\x00b\x00c";
        static::assertSame('abc', URLify::filter($str));
    }

    public function testHtml()
    {
        $str = '<p class="label-key" title="2014-03-12 13:06:53Z"><b>3   years ago !!!!</b></p>';
        static::assertSame('3-years-ago', URLify::filter($str));
    }

    public function testChinese()
    {
        $str = '活动日起';
        static::assertSame('huo-dong-ri-qi', URLify::filter($str));
    }
}
