<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\PracticeQualityGateService;
use PHPUnit\Framework\TestCase;

final class PracticeQualityGateServiceTest extends TestCase
{
    /**
     * Passing verification results are marked ready.
     */
    public function test_passing_results_are_ready(): void
    {
        $result = (new PracticeQualityGateService)->evaluate([
            'tests' => 31,
            'assertions' => 1519,
            'failures' => 0,
            'pint' => true,
        ]);

        $this->assertSame('ready', $result['status']);
        $this->assertTrue($result['passed']);
        $this->assertTrue($result['checks']['tests_pass']);
    }

    /**
     * Missing tests and style failures are marked as needs-work.
     */
    public function test_failed_results_need_work(): void
    {
        $result = (new PracticeQualityGateService)->evaluate([
            'tests' => 0,
            'assertions' => 0,
            'failures' => 1,
            'pint' => false,
        ]);

        $this->assertSame('needs-work', $result['status']);
        $this->assertFalse($result['passed']);
        $this->assertFalse($result['checks']['tests_exist']);
        $this->assertFalse($result['checks']['style_passes']);
    }
}
