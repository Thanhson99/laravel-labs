<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class UiCopyLanguageTest extends TestCase
{
    /**
     * Hub UI copy should stay English-only; Vietnamese without accents is especially hard to read.
     */
    public function test_ui_copy_does_not_contain_vietnamese_without_accents(): void
    {
        $forbiddenPhrases = [
            'de tu',
            'kiem tra',
            'sau khi',
            'lam TDD',
            'thuc hanh',
            'noi dung',
            'cau hoi',
            'tra loi',
            'ket qua',
            'du lieu',
            'nhap ten',
            'tiep tuc',
            'bat dau',
            'nguoi dung',
            'cong nghe',
            'chinh sua',
            'bo sung',
            'hoan thanh',
            'khong co',
            'chon',
            'hoc toi dau',
            'toi do',
            'moi lab',
            'bien noi dung',
            'bai lam',
            'muc tieu',
            'file can tao',
            'file can sua',
            'command can chay',
            'dieu kien',
            'tu kiem tra',
        ];

        foreach ($this->uiCopyFiles() as $file) {
            $contents = file_get_contents($file);

            $this->assertNotFalse($contents, sprintf('Unable to read UI copy file: %s', $file));

            foreach ($forbiddenPhrases as $phrase) {
                $this->assertDoesNotMatchRegularExpression(
                    '/\b'.preg_quote($phrase, '/').'\b/i',
                    $contents,
                    sprintf('Vietnamese no-accent UI copy found in %s: "%s".', $file, $phrase)
                );
            }
        }
    }

    /**
     * Learner-facing copy should describe real behavior instead of shipping placeholder language.
     */
    public function test_ui_copy_does_not_use_placeholder_language(): void
    {
        foreach ($this->hubApplicationCopyFiles() as $file) {
            $contents = file_get_contents($file);

            $this->assertNotFalse($contents, sprintf('Unable to read UI copy file: %s', $file));
            $this->assertDoesNotMatchRegularExpression(
                '/\bplaceholder\b/i',
                $this->withoutHtmlPlaceholderAttributes($contents),
                sprintf('Placeholder language found in learner-facing copy: %s.', $file)
            );
        }
    }

    /**
     * Learner-facing copy should not normalize throwaway implementation wording.
     */
    public function test_ui_copy_does_not_use_temporary_language(): void
    {
        foreach ($this->hubApplicationCopyFiles() as $file) {
            $contents = file_get_contents($file);

            $this->assertNotFalse($contents, sprintf('Unable to read UI copy file: %s', $file));
            $this->assertDoesNotMatchRegularExpression(
                '/\btemporary\b/i',
                $contents,
                sprintf('Temporary implementation language found in learner-facing copy: %s.', $file)
            );
        }
    }

    /**
     * @return array<int, string>
     */
    private function hubApplicationCopyFiles(): array
    {
        return array_values(array_merge(
            $this->phpFiles(dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'),
            $this->phpFiles(dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'),
            $this->phpFiles(dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Services'),
            [dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'practice.php'],
        ));
    }

    /**
     * @return array<int, string>
     */
    private function uiCopyFiles(): array
    {
        $repositoryRoot = dirname(__DIR__, 3);

        return array_values(array_merge(
            $this->hubApplicationCopyFiles(),
            $this->englishJsonFiles($repositoryRoot.DIRECTORY_SEPARATOR.'data'),
        ));
    }

    /**
     * @return array<int, string>
     */
    private function phpFiles(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                if (str_ends_with($file->getPathname(), DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'welcome.blade.php')) {
                    continue;
                }

                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function withoutHtmlPlaceholderAttributes(string $contents): string
    {
        return preg_replace('/\splaceholder=(["\']).*?\1/i', '', $contents) ?? $contents;
    }

    /**
     * @return array<int, string>
     */
    private function englishJsonFiles(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'json') {
                continue;
            }

            $path = $file->getPathname();

            if (str_ends_with($path, '.vi.json')) {
                continue;
            }

            $files[] = $path;
        }

        return $files;
    }
}
