<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Practice\Php\NameNormalizer;
use PHPUnit\Framework\TestCase;

final class NameNormalizerTest extends TestCase
{
    /**
     * The normalizer trims, compacts spacing, and title-cases names.
     */
    public function test_it_normalizes_one_name(): void
    {
        $normalizer = new NameNormalizer;

        $this->assertSame('Jane Doe', $normalizer->normalize('  jane   doe  '));
        $this->assertSame('Laravel Labs', $normalizer->normalize('LARAVEL labs'));
    }

    /**
     * Empty normalized values are removed from batch output.
     */
    public function test_it_normalizes_many_names_and_skips_empty_values(): void
    {
        $normalizer = new NameNormalizer;

        $this->assertSame(
            ['Jane Doe', 'Api Practice'],
            $normalizer->normalizeMany(['  jane doe ', '', ' API   practice ']),
        );
    }
}
