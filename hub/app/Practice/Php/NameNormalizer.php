<?php

declare(strict_types=1);

namespace App\Practice\Php;

final class NameNormalizer
{
    /**
     * Normalize one name into title case with compact spacing.
     */
    public function normalize(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        if ($value === '') {
            return '';
        }

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Normalize many raw name values and remove empty results.
     *
     * @param  array<int, string>  $values
     * @return array<int, string>
     */
    public function normalizeMany(array $values): array
    {
        return collect($values)
            ->map(fn (string $value): string => $this->normalize($value))
            ->filter(fn (string $value): bool => $value !== '')
            ->values()
            ->all();
    }
}
