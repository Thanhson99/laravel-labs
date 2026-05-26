<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class RouteFileDocumentationTest extends TestCase
{
    /**
     * Route files are part of the learning surface, so every executable route map line needs nearby intent.
     */
    public function test_route_files_have_file_headers_and_route_comments(): void
    {
        foreach ($this->routeFiles() as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);

            $this->assertNotFalse($lines, sprintf('Unable to read route file: %s', $file));
            $this->assertHasFileHeader($file, $lines);
            $this->assertExecutableRouteLinesHaveComments($file, $lines);
        }
    }

    /**
     * @return array<int, string>
     */
    private function routeFiles(): array
    {
        $routePath = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'routes';
        $rootFiles = glob($routePath.DIRECTORY_SEPARATOR.'*.php') ?: [];
        $groupedFiles = glob($routePath.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'*.php') ?: [];

        return array_values(array_merge($rootFiles, $groupedFiles));
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function assertHasFileHeader(string $file, array $lines): void
    {
        $firstExecutableIndex = $this->firstExecutableRouteLineIndex($lines);

        $this->assertNotNull(
            $firstExecutableIndex,
            sprintf('Route file has no route declarations or route includes: %s', $file)
        );

        $this->assertTrue(
            $this->hasPreviousComment($lines, $firstExecutableIndex),
            sprintf('Route file is missing a header comment before its first executable route line: %s', $file)
        );
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function assertExecutableRouteLinesHaveComments(string $file, array $lines): void
    {
        foreach ($lines as $index => $line) {
            if (! $this->isExecutableRouteLine($line)) {
                continue;
            }

            $this->assertTrue(
                $this->hasPreviousComment($lines, $index),
                sprintf('Missing route-purpose comment in %s on line %d.', $file, $index + 1)
            );

            $this->assertTrue(
                $this->hasPurposefulPreviousComment($lines, $index),
                sprintf('Route-purpose comment should start with a clear verb in %s on line %d.', $file, $index + 1)
            );
        }
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function firstExecutableRouteLineIndex(array $lines): ?int
    {
        foreach ($lines as $index => $line) {
            if ($this->isExecutableRouteLine($line)) {
                return $index;
            }
        }

        return null;
    }

    private function isExecutableRouteLine(string $line): bool
    {
        $line = trim($line);

        return str_starts_with($line, 'Route::')
            || str_starts_with($line, 'Artisan::command')
            || str_starts_with($line, 'require __DIR__');
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function hasPreviousComment(array $lines, int $index): bool
    {
        return $this->previousComment($lines, $index) !== null;
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function hasPurposefulPreviousComment(array $lines, int $index): bool
    {
        $comment = $this->previousComment($lines, $index);

        if ($comment === null) {
            return false;
        }

        return preg_match('/^\/\/ (Display|Evaluate|Keep|Load|Normalize|Open|Return|Show|Store|Summarize)\\b/', $comment) === 1;
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function previousComment(array $lines, int $index): ?string
    {
        for ($cursor = $index - 1; $cursor >= 0; $cursor--) {
            $line = trim($lines[$cursor]);

            if ($line === '') {
                continue;
            }

            return str_starts_with($line, '//') ? $line : null;
        }

        return null;
    }
}
