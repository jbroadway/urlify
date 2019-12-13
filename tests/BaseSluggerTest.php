<?php

/**
 * Class BaseSluggerTest
 */
abstract class BaseSluggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $sluggerClassName = 'URLify';

    /**
     * @var URLify
     */
    protected $slugger;

    /**
     * @var string
     */
    protected $inputFixturesDir;

    /**
     * @var string
     */
    protected $expectedFixturesDir;

    protected function setUp()
    {
        $sluggerClassNamespace = $this->sluggerClassName;
        $this->slugger = new $sluggerClassNamespace();

        $fixturesBaseDir = __DIR__ . \DIRECTORY_SEPARATOR . 'fixtures' . \DIRECTORY_SEPARATOR . \strtolower($this->sluggerClassName);
        $this->inputFixturesDir = $fixturesBaseDir . \DIRECTORY_SEPARATOR . 'input';
        $this->expectedFixturesDir = $fixturesBaseDir . \DIRECTORY_SEPARATOR . 'expected';
    }

    /**
     * @dataProvider provideSlugFileNames
     *
     * @param $fileName
     *
     * @noinspection PhpUnitTestsInspection - FP: from parent class
     */
    public function testDefaultSlugify($fileName)
    {
        $inputStrings = \file($this->inputFixturesDir . \DIRECTORY_SEPARATOR . $fileName, \FILE_IGNORE_NEW_LINES);
        $expectedSlugs = \file($this->expectedFixturesDir . \DIRECTORY_SEPARATOR . $fileName, \FILE_IGNORE_NEW_LINES);

        $slugger = $this->slugger;
        $slugs = \array_map(
            static function ($string) use ($slugger) {
                /** @noinspection PhpStaticAsDynamicMethodCallInspection */
                return $slugger->filter($string, 200, 'en', false, false, true, '-');
            },
            $inputStrings
        );

        // DEBUG
        //\var_export($slugs);

        foreach ($expectedSlugs as $key => $expectedSlugValue) {
            static::assertSame($expectedSlugs[$key], $slugs[$key], 'tested-file: ' . $fileName . ' | ' . $slugs[$key]);
        }

        static::assertSame($expectedSlugs, $slugs, 'tested-file: ' . $fileName);
    }

    /**
     * @dataProvider provideSlugEdgeCases
     *
     * @param $string
     * @param $expectedSlug
     */
    public function testSlugifyEdgeCases($string, $expectedSlug)
    {
        $slug = URLify::filter($string, 200, 'de', false, true, true, '-');

        static::assertSame($expectedSlug, $slug);
    }

    /**
     * @return array
     */
    public function provideSlugEdgeCases(): array
    {
        return [
            ['', ''],
            ['    ', ''],
            ['-', ''],
            ['-A', 'a'],
            ['A-', 'a'],
            ['-----', ''],
            ['-a-A-A-a-', 'a-a-a-a'],
            ['A-a-A-a-A-a', 'a-a-a-a-a-a'],
            [' -- ', ''],
            ['a--A', 'a-a'],
            ['a- -A', 'a-a'],
            ['a-' . \html_entity_decode('&nbsp;') . '-A', 'a-a'],
            ['a - ' . \html_entity_decode('&nbsp;') . ' -A', 'a-a'],
            [' - - ', ''],
            [' -A- ', 'a'],
            [' - A - ', 'a'],
            ["\0", ''],
            [true, '1'],
            [false, ''],
            [1, '1'],
        ];
    }
}
