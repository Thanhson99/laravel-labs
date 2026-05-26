<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class StaticPortalCopyTest extends TestCase
{
    /**
     * Static portal labels should not mix English control names into Vietnamese UI copy.
     */
    public function test_static_portal_does_not_use_mixed_language_control_labels(): void
    {
        $contents = file_get_contents(dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'site.js');

        $this->assertNotFalse($contents, 'Unable to read static portal renderer.');
        $this->assertStringContainsString('collapseCode: "Thu gọn mã"', $contents);
        $this->assertStringContainsString('expandCode: "Mở mã"', $contents);
        $this->assertStringContainsString('copyCode: "Sao chép"', $contents);
        $this->assertStringContainsString('copiedCode: "✓ Đã sao chép"', $contents);
        $this->assertStringContainsString('studyOff: "Chế độ học"', $contents);
        $this->assertStringContainsString('studyOn: "Chế độ học: chưa xong"', $contents);
        $this->assertStringContainsString('tip: "Mẹo"', $contents);
        $this->assertStringContainsString('copy: "Sao chép"', $contents);
        $this->assertStringContainsString('copied: "✓ Đã sao chép"', $contents);
        $this->assertStringNotContainsString('language === "vi" ? "Study mode"', $contents);
    }

    /**
     * The static practice page should point readers to runnable Hub code workbenches.
     */
    public function test_static_practice_page_includes_code_reading_workbenches(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $sections = $data['pages']['practice']['sections'] ?? [];
            $workbenchSection = null;

            foreach ($sections as $section) {
                if (($section['type'] ?? null) === 'workbenches') {
                    $workbenchSection = $section;
                    break;
                }
            }

            $this->assertIsArray($workbenchSection, "Missing {$language} practice workbench section.");
            $this->assertCount(14, $workbenchSection['items']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/name-normalizer', $workbenchSection['items'][0]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/topic-intake', $workbenchSection['items'][1]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/http-request-flow', $workbenchSection['items'][2]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/quality-gate', $workbenchSection['items'][3]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/security-escape-preview', $workbenchSection['items'][4]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/collection-filter-preview', $workbenchSection['items'][5]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/async-job-plan', $workbenchSection['items'][6]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/event-listener-plan', $workbenchSection['items'][7]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/container-binding-plan', $workbenchSection['items'][8]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/authorization-policy-plan', $workbenchSection['items'][9]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/cache-strategy-plan', $workbenchSection['items'][10]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/file-storage-plan', $workbenchSection['items'][11]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/rate-limit-plan', $workbenchSection['items'][12]['href']);
            $this->assertContains('hub/app/Services/Practice/PracticeTopicIntakeService.php', $workbenchSection['items'][1]['files']);
            $this->assertContains('hub/app/Services/Practice/HttpRequestFlowTracerService.php', $workbenchSection['items'][2]['files']);
            $this->assertContains('hub/app/Services/Practice/PracticeQualityGateService.php', $workbenchSection['items'][3]['files']);
            $this->assertContains('hub/app/Services/Practice/SecurityEscapePreviewService.php', $workbenchSection['items'][4]['files']);
            $this->assertContains('hub/app/Services/Practice/CollectionFilterPreviewService.php', $workbenchSection['items'][5]['files']);
            $this->assertContains('hub/app/Services/Practice/AsyncJobPlanService.php', $workbenchSection['items'][6]['files']);
            $this->assertContains('hub/app/Services/Practice/EventListenerPlanService.php', $workbenchSection['items'][7]['files']);
            $this->assertContains('hub/app/Services/Practice/ContainerBindingPlanService.php', $workbenchSection['items'][8]['files']);
            $this->assertContains('hub/app/Services/Practice/AuthorizationPolicyPlanService.php', $workbenchSection['items'][9]['files']);
            $this->assertContains('hub/app/Services/Practice/CacheStrategyPlanService.php', $workbenchSection['items'][10]['files']);
            $this->assertContains('hub/app/Services/Practice/FileStoragePlanService.php', $workbenchSection['items'][11]['files']);
            $this->assertContains('hub/app/Services/Practice/RateLimitPlanService.php', $workbenchSection['items'][12]['files']);
        }
    }
}
