<?php declare(strict_types=1);

/**
 * Class Utf8SluggerTest
 *
 * @internal
 */
final class Utf8SluggerTest extends BaseSluggerTest
{
    /**
     * @return array
     */
    public function provideSlugFileNames(): array
    {
        return [
            ['iso-8859-1.txt'],
            ['iso-8859-2.txt'],
            ['iso-8859-3.txt'],
            ['iso-8859-4.txt'],
            ['pangrams.txt'],
            ['arabic.txt'],
            ['hebrew.txt'],
            ['japanese.txt'],
        ];
    }
}
