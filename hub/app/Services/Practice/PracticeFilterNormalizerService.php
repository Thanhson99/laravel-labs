<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeFilterNormalizerService
{
    /**
     * Normalize a query filter to a positive integer.
     */
    public function positiveInt(mixed $value, int $default): int
    {
        $integer = filter_var($value, FILTER_VALIDATE_INT);

        if ($integer === false || $integer < 1) {
            return $default;
        }

        return $integer;
    }
}
