<?php declare(strict_types=1);

/**
 * Class SeoUtf8SluggerTest
 *
 * @internal
 */
final class SeoUtf8SluggerTest extends BaseSluggerTest
{
    /**
     * @return array
     */
    public function provideSlugFileNames(): array
    {
        return [
            ['strings-2.txt'],
            ['sample-utf-8-bom.txt'],
            ['sample-unicode-chart.txt'],
        ];
    }
}
