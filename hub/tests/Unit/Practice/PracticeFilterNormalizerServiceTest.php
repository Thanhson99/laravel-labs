<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\PracticeFilterNormalizerService;
use PHPUnit\Framework\TestCase;

final class PracticeFilterNormalizerServiceTest extends TestCase
{
    public function test_it_returns_positive_integer_values(): void
    {
        $normalizer = new PracticeFilterNormalizerService;

        $this->assertSame(7, $normalizer->positiveInt('7', 3));
        $this->assertSame(12, $normalizer->positiveInt(12, 3));
    }

    public function test_it_returns_default_for_invalid_values(): void
    {
        $normalizer = new PracticeFilterNormalizerService;

        $this->assertSame(3, $normalizer->positiveInt(null, 3));
        $this->assertSame(3, $normalizer->positiveInt('abc', 3));
        $this->assertSame(3, $normalizer->positiveInt('0', 3));
        $this->assertSame(3, $normalizer->positiveInt('-4', 3));
    }
}
