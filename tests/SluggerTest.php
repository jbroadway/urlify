<?php declare(strict_types=1);

/**
 * Class SluggerTest
 *
 * @internal
 */
final class SluggerTest extends BaseSluggerTest
{
    /**
     * @return array
     */
    public function provideSlugFileNames(): array
    {
        return [
            ['strings-3.txt'],
        ];
    }

    /**
     * @return array
     */
    public function provideSlugFileNamesWithNull(): array
    {
        return [
            ['iso-8859-1-1.txt'],
            ['iso-8859-2-1.txt'],
            ['iso-8859-3-1.txt'],
            ['iso-8859-4-1.txt'],
        ];
    }
}
