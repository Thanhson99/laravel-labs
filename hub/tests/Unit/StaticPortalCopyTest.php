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
            $this->assertCount(38, $workbenchSection['items']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/name-normalizer', $workbenchSection['items'][0]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/topic-intake', $workbenchSection['items'][1]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/http-request-flow', $workbenchSection['items'][2]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/quality-gate', $workbenchSection['items'][3]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/security-escape-preview', $workbenchSection['items'][4]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/collection-filter-preview', $workbenchSection['items'][5]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/async-job-plan', $workbenchSection['items'][6]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/event-listener-plan', $workbenchSection['items'][7]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/container-binding-plan', $workbenchSection['items'][8]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/dependency-injection-refactor', $workbenchSection['items'][9]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/oop-abstraction-decision', $workbenchSection['items'][10]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/layered-architecture-decision', $workbenchSection['items'][11]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/reverse-proxy-failure-plan', $workbenchSection['items'][12]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/load-balancer-plan', $workbenchSection['items'][13]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/kubernetes-analogy-plan', $workbenchSection['items'][14]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/authorization-policy-plan', $workbenchSection['items'][15]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/cache-strategy-plan', $workbenchSection['items'][16]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/file-storage-plan', $workbenchSection['items'][17]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/rate-limit-plan', $workbenchSection['items'][18]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/jwt-token-storage-plan', $workbenchSection['items'][19]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/jwt-revocation-plan', $workbenchSection['items'][20]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/oauth-flow-plan', $workbenchSection['items'][21]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/graphql-rest-decision', $workbenchSection['items'][22]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/restful-api-naming-plan', $workbenchSection['items'][23]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/ai-hallucination-guard-plan', $workbenchSection['items'][24]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/ai-cloud-interview-rubric', $workbenchSection['items'][25]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/rag-strategy-plan', $workbenchSection['items'][26]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/llm-decision-loop-plan', $workbenchSection['items'][27]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/ai-agent-memory-plan', $workbenchSection['items'][28]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/lsm-tree-plan', $workbenchSection['items'][29]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/system-design-tradeoff-plan', $workbenchSection['items'][30]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/react-render-optimization-plan', $workbenchSection['items'][31]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/javascript-hoisting-lab', $workbenchSection['items'][32]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/idor-access-review', $workbenchSection['items'][33]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/sql-injection-defense-plan', $workbenchSection['items'][34]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/csrf-protection-plan', $workbenchSection['items'][35]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/workbench/siem-elk-plan', $workbenchSection['items'][36]['href']);
            $this->assertSame('{{HUB_BASE_URL}}/api/practice/runtime-smoke-check', $workbenchSection['items'][37]['href']);
            $this->assertContains('hub/app/Services/Practice/PracticeTopicIntakeService.php', $workbenchSection['items'][1]['files']);
            $this->assertContains('hub/app/Services/Practice/HttpRequestFlowTracerService.php', $workbenchSection['items'][2]['files']);
            $this->assertContains('hub/app/Services/Practice/PracticeQualityGateService.php', $workbenchSection['items'][3]['files']);
            $this->assertContains('hub/app/Services/Practice/SecurityEscapePreviewService.php', $workbenchSection['items'][4]['files']);
            $this->assertContains('hub/app/Services/Practice/CollectionFilterPreviewService.php', $workbenchSection['items'][5]['files']);
            $this->assertContains('hub/app/Services/Practice/AsyncJobPlanService.php', $workbenchSection['items'][6]['files']);
            $this->assertContains('hub/app/Services/Practice/EventListenerPlanService.php', $workbenchSection['items'][7]['files']);
            $this->assertContains('hub/app/Services/Practice/ContainerBindingPlanService.php', $workbenchSection['items'][8]['files']);
            $this->assertContains('hub/app/Services/Practice/DependencyInjectionRefactorService.php', $workbenchSection['items'][9]['files']);
            $this->assertContains('hub/app/Services/Practice/OopAbstractionDecisionService.php', $workbenchSection['items'][10]['files']);
            $this->assertContains('hub/app/Services/Practice/LayeredArchitectureDecisionService.php', $workbenchSection['items'][11]['files']);
            $this->assertContains('hub/app/Services/Practice/ReverseProxyFailurePlanService.php', $workbenchSection['items'][12]['files']);
            $this->assertContains('hub/app/Services/Practice/LoadBalancerPlanService.php', $workbenchSection['items'][13]['files']);
            $this->assertContains('hub/app/Services/Practice/KubernetesAnalogyPlanService.php', $workbenchSection['items'][14]['files']);
            $this->assertContains('hub/app/Services/Practice/AuthorizationPolicyPlanService.php', $workbenchSection['items'][15]['files']);
            $this->assertContains('hub/app/Services/Practice/CacheStrategyPlanService.php', $workbenchSection['items'][16]['files']);
            $this->assertContains('hub/app/Services/Practice/FileStoragePlanService.php', $workbenchSection['items'][17]['files']);
            $this->assertContains('hub/app/Services/Practice/RateLimitPlanService.php', $workbenchSection['items'][18]['files']);
            $this->assertContains('hub/app/Services/Practice/JwtTokenStoragePlanService.php', $workbenchSection['items'][19]['files']);
            $this->assertContains('hub/app/Services/Practice/JwtRevocationPlanService.php', $workbenchSection['items'][20]['files']);
            $this->assertContains('hub/app/Services/Practice/OauthFlowPlanService.php', $workbenchSection['items'][21]['files']);
            $this->assertContains('hub/app/Services/Practice/GraphqlRestDecisionService.php', $workbenchSection['items'][22]['files']);
            $this->assertContains('hub/app/Services/Practice/RestfulApiNamingPlanService.php', $workbenchSection['items'][23]['files']);
            $this->assertContains('hub/app/Services/Practice/AiHallucinationGuardPlanService.php', $workbenchSection['items'][24]['files']);
            $this->assertContains('hub/app/Services/Practice/AiCloudInterviewRubricService.php', $workbenchSection['items'][25]['files']);
            $this->assertContains('hub/app/Services/Practice/RagStrategyPlanService.php', $workbenchSection['items'][26]['files']);
            $this->assertContains('hub/app/Services/Practice/LlmDecisionLoopPlanService.php', $workbenchSection['items'][27]['files']);
            $this->assertContains('hub/app/Services/Practice/AiAgentMemoryPlanService.php', $workbenchSection['items'][28]['files']);
            $this->assertContains('hub/app/Services/Practice/LsmTreePlanService.php', $workbenchSection['items'][29]['files']);
            $this->assertContains('hub/app/Services/Practice/SystemDesignTradeoffPlanService.php', $workbenchSection['items'][30]['files']);
            $this->assertContains('hub/app/Services/Practice/ReactRenderOptimizationPlanService.php', $workbenchSection['items'][31]['files']);
            $this->assertContains('hub/app/Services/Practice/JavascriptHoistingLabService.php', $workbenchSection['items'][32]['files']);
            $this->assertContains('hub/app/Services/Practice/IdorAccessReviewService.php', $workbenchSection['items'][33]['files']);
            $this->assertContains('hub/app/Services/Practice/SqlInjectionDefensePlanService.php', $workbenchSection['items'][34]['files']);
            $this->assertContains('hub/app/Services/Practice/CsrfProtectionPlanService.php', $workbenchSection['items'][35]['files']);
            $this->assertContains('hub/app/Services/Practice/SiemElkPlanService.php', $workbenchSection['items'][36]['files']);
        }
    }

    /**
     * The landing page should feature the LLM foundations topic.
     */
    public function test_landing_page_features_llm_foundations_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $llmCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/vibe-coding/prompting.html' && str_contains((string) ($card['summary'] ?? ''), 'LLM')) {
                    $llmCard = $card;
                    break;
                }
            }

            $this->assertIsArray($llmCard, "Missing {$language} LLM foundations landing card.");
        }
    }

    /**
     * The landing page should feature the Predictive AI versus Generative AI topic.
     */
    public function test_landing_page_features_predictive_generative_ai_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $aiTypeCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/vibe-coding/prompting.html' && str_contains((string) ($card['summary'] ?? ''), 'Generative AI')) {
                    $aiTypeCard = $card;
                    break;
                }
            }

            $this->assertIsArray($aiTypeCard, "Missing {$language} Predictive AI versus Generative AI landing card.");
        }
    }

    /**
     * The landing page should feature the JavaScript closure topic.
     */
    public function test_landing_page_features_javascript_closure_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $closureCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/frontend.html' && str_contains((string) ($card['summary'] ?? ''), 'closure')) {
                    $closureCard = $card;
                    break;
                }
            }

            $this->assertIsArray($closureCard, "Missing {$language} JavaScript closure landing card.");
        }
    }

    /**
     * The landing page should feature the JavaScript hoisting topic.
     */
    public function test_landing_page_features_javascript_hoisting_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $hoistingCard = null;

            foreach ($cards as $card) {
                $cardText = strtolower((string) ($card['title'] ?? '').' '.(string) ($card['summary'] ?? ''));

                if (($card['href'] ?? null) === 'sites/laravel/frontend.html' && str_contains($cardText, 'hoisting')) {
                    $hoistingCard = $card;
                    break;
                }
            }

            $this->assertIsArray($hoistingCard, "Missing {$language} JavaScript hoisting landing card.");
        }
    }

    /**
     * The frontend topic should explain JavaScript hoisting with a runnable code example.
     */
    public function test_frontend_topic_explains_javascript_hoisting(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."frontend.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} frontend content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $hoistingItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'hoisting')) {
                    $hoistingItem = $item;
                    break;
                }
            }

            $this->assertIsArray($hoistingItem, "Missing {$language} JavaScript hoisting topic.");
            $this->assertStringContainsString('temporal dead zone', (string) ($hoistingItem['body'] ?? ''));
            $this->assertStringContainsString('sayHi();', (string) ($hoistingItem['code'] ?? ''));
        }
    }

    /**
     * The frontend topic should explain arrow-function this behavior with a runnable code example.
     */
    public function test_frontend_topic_explains_arrow_function_this(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."frontend.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} frontend content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $arrowItem = null;

            foreach ($items as $item) {
                $text = strtolower((string) ($item['title'] ?? '').' '.(string) ($item['body'] ?? ''));

                if (str_contains($text, 'arrow function') && str_contains($text, 'this')) {
                    $arrowItem = $item;
                    break;
                }
            }

            $this->assertIsArray($arrowItem, "Missing {$language} arrow function this topic.");
            $this->assertStringContainsString('lexical', (string) ($arrowItem['body'] ?? ''));
            $this->assertStringContainsString('user.normal()', (string) ($arrowItem['code'] ?? ''));
        }
    }

    /**
     * The landing page should feature the arrow-function this topic.
     */
    public function test_landing_page_features_arrow_function_this_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $arrowCard = null;

            foreach ($cards as $card) {
                $cardText = strtolower((string) ($card['title'] ?? '').' '.(string) ($card['summary'] ?? ''));

                if (($card['href'] ?? null) === 'sites/laravel/frontend.html' && str_contains($cardText, 'arrow') && str_contains($cardText, 'this')) {
                    $arrowCard = $card;
                    break;
                }
            }

            $this->assertIsArray($arrowCard, "Missing {$language} arrow function this landing card.");
        }
    }

    /**
     * The junior interview bank should include a JavaScript hoisting answer linked to the lab.
     */
    public function test_junior_interview_bank_includes_javascript_hoisting_question(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'interview'.DIRECTORY_SEPARATOR."junior.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} junior interview content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $hoistingItem = null;

            foreach ($items as $item) {
                if (str_contains(strtolower((string) ($item['title'] ?? '')), 'hoisting')) {
                    $hoistingItem = $item;
                    break;
                }
            }

            $this->assertIsArray($hoistingItem, "Missing {$language} junior JavaScript hoisting question.");
            $this->assertStringContainsString('temporal dead zone', (string) ($hoistingItem['body'] ?? ''));
            $this->assertSame('{{HUB_BASE_URL}}/workbench/javascript-hoisting-lab', $hoistingItem['links'][0]['href'] ?? null);
        }
    }

    /**
     * The landing page should feature the Cloud Engineer AI interview pack.
     */
    public function test_landing_page_features_ai_cloud_interview_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $aiInterviewCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/interview/devops.html' && str_contains((string) ($card['summary'] ?? ''), 'Cloud')) {
                    $aiInterviewCard = $card;
                    break;
                }
            }

            $this->assertIsArray($aiInterviewCard, "Missing {$language} Cloud AI interview landing card.");
        }
    }

    /**
     * The landing page should feature the RAG patterns topic.
     */
    public function test_landing_page_features_rag_patterns_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $ragCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/vibe-coding/prompting.html' && str_contains((string) ($card['summary'] ?? ''), 'RAG')) {
                    $ragCard = $card;
                    break;
                }
            }

            $this->assertIsArray($ragCard, "Missing {$language} RAG patterns landing card.");
        }
    }

    /**
     * The landing page should feature the GraphQL versus REST API topic.
     */
    public function test_landing_page_features_graphql_rest_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $graphqlCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/api-integration.html' && str_contains((string) ($card['summary'] ?? ''), 'GraphQL')) {
                    $graphqlCard = $card;
                    break;
                }
            }

            $this->assertIsArray($graphqlCard, "Missing {$language} GraphQL REST landing card.");
        }
    }

    /**
     * The landing page should feature the RESTful API naming topic.
     */
    public function test_landing_page_features_restful_api_naming_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $namingCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/api-integration.html'
                    && str_contains((string) ($card['summary'] ?? ''), 'RESTful')) {
                    $namingCard = $card;
                    break;
                }
            }

            $this->assertIsArray($namingCard, "Missing {$language} RESTful API naming landing card.");
        }
    }

    /**
     * The RESTful API naming topic should point into the runnable naming workbench.
     */
    public function test_restful_api_naming_topic_links_to_naming_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."api-integration.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} API integration content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $namingItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/restful-api-naming-plan') {
                    $namingItem = $item;
                    break;
                }
            }

            $this->assertIsArray($namingItem, "Missing {$language} RESTful API naming workbench link.");
        }
    }

    /**
     * The landing page should feature the Covering Index topic.
     */
    public function test_landing_page_features_covering_index_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $coveringIndexCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/performance-search.html' && str_contains((string) ($card['title'] ?? ''), 'Covering Index')) {
                    $coveringIndexCard = $card;
                    break;
                }
            }

            $this->assertIsArray($coveringIndexCard, "Missing {$language} Covering Index landing card.");
        }
    }

    /**
     * The landing page should feature the database locking topic.
     */
    public function test_landing_page_features_database_locking_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $lockingCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/data.html' && str_contains(strtolower((string) ($card['title'] ?? '')), 'locking')) {
                    $lockingCard = $card;
                    break;
                }
            }

            $this->assertIsArray($lockingCard, "Missing {$language} database locking landing card.");
        }
    }

    /**
     * The landing page should feature the reverse proxy failure topic.
     */
    public function test_landing_page_features_reverse_proxy_failure_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $proxyCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/interview/devops.html' && str_contains((string) ($card['summary'] ?? ''), 'proxy')) {
                    $proxyCard = $card;
                    break;
                }
            }

            $this->assertIsArray($proxyCard, "Missing {$language} reverse proxy landing card.");
        }
    }

    /**
     * The landing page should feature the System Design tradeoff topic.
     */
    public function test_landing_page_features_system_design_tradeoff_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $tradeoffCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/interview/senior.html' && str_contains((string) ($card['summary'] ?? ''), 'tradeoff')) {
                    $tradeoffCard = $card;
                    break;
                }
            }

            $this->assertIsArray($tradeoffCard, "Missing {$language} System Design tradeoff landing card.");
        }
    }

    /**
     * The landing page should feature the SIEM ELK topic.
     */
    public function test_landing_page_features_siem_elk_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $siemCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/interview/devops.html' && str_contains((string) ($card['summary'] ?? ''), 'SIEM')) {
                    $siemCard = $card;
                    break;
                }
            }

            $this->assertIsArray($siemCard, "Missing {$language} SIEM ELK landing card.");
        }
    }

    /**
     * The landing page should feature the SQL Injection interview topic.
     */
    public function test_landing_page_features_sql_injection_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $sqlInjectionCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/auth-security.html' && str_contains((string) ($card['summary'] ?? ''), 'SQL Injection')) {
                    $sqlInjectionCard = $card;
                    break;
                }
            }

            $this->assertIsArray($sqlInjectionCard, "Missing {$language} SQL Injection landing card.");
        }
    }

    /**
     * The landing page should feature the CSRF protection topic.
     */
    public function test_landing_page_features_csrf_protection_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $csrfCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/auth-security.html' && str_contains((string) ($card['summary'] ?? ''), 'CSRF')) {
                    $csrfCard = $card;
                    break;
                }
            }

            $this->assertIsArray($csrfCard, "Missing {$language} CSRF landing card.");
        }
    }

    /**
     * The landing page should feature the XSS defense topic.
     */
    public function test_landing_page_features_xss_defense_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $xssCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/auth-security.html' && str_contains((string) ($card['summary'] ?? ''), 'XSS')) {
                    $xssCard = $card;
                    break;
                }
            }

            $this->assertIsArray($xssCard, "Missing {$language} XSS landing card.");
        }
    }

    /**
     * The landing page should feature the Security Misconfiguration topic.
     */
    public function test_landing_page_features_security_misconfiguration_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $misconfigurationCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/auth-security.html' && str_contains((string) ($card['summary'] ?? ''), 'Misconfiguration')) {
                    $misconfigurationCard = $card;
                    break;
                }
            }

            $this->assertIsArray($misconfigurationCard, "Missing {$language} Security Misconfiguration landing card.");
        }
    }

    /**
     * The landing page should feature the Client Credentials OAuth topic.
     */
    public function test_landing_page_features_client_credentials_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $clientCredentialsCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/auth-security.html' && str_contains((string) ($card['summary'] ?? ''), 'Client Credentials')) {
                    $clientCredentialsCard = $card;
                    break;
                }
            }

            $this->assertIsArray($clientCredentialsCard, "Missing {$language} Client Credentials landing card.");
        }
    }

    /**
     * The landing page should feature the PKCE OAuth topic.
     */
    public function test_landing_page_features_pkce_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $pkceCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/laravel/auth-security.html' && str_contains((string) ($card['summary'] ?? ''), 'PKCE')) {
                    $pkceCard = $card;
                    break;
                }
            }

            $this->assertIsArray($pkceCard, "Missing {$language} PKCE landing card.");
        }
    }

    /**
     * The reverse proxy outage topic should point into the proxy failure-mode workbench.
     */
    public function test_reverse_proxy_outage_topic_links_to_proxy_failure_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'interview'.DIRECTORY_SEPARATOR."devops.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} DevOps interview content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $proxyItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/reverse-proxy-failure-plan'
                    && (str_contains((string) ($item['title'] ?? ''), 'Cloudflare') || str_contains((string) ($item['title'] ?? ''), 'reverse proxy'))) {
                    $proxyItem = $item;
                    break;
                }
            }

            $this->assertIsArray($proxyItem, "Missing {$language} reverse proxy outage item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/reverse-proxy-failure-plan',
                $proxyItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The GraphQL versus REST topic should point into the API contract workbench.
     */
    public function test_graphql_rest_topic_links_to_api_contract_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."api-integration.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} API integration content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $graphqlItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'GraphQL')) {
                    $graphqlItem = $item;
                    break;
                }
            }

            $this->assertIsArray($graphqlItem, "Missing {$language} GraphQL REST item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/graphql-rest-decision',
                $graphqlItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The Cloud Engineer AI interview topic should point into the hallucination guard workbench.
     */
    public function test_ai_cloud_interview_topic_links_to_hallucination_guard_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'interview'.DIRECTORY_SEPARATOR."devops.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} DevOps interview content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $aiCloudItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'AI') && (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/ai-cloud-interview-rubric')) {
                    $aiCloudItem = $item;
                    break;
                }
            }

            $this->assertIsArray($aiCloudItem, "Missing {$language} Cloud AI interview item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/ai-cloud-interview-rubric',
                $aiCloudItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The LLM foundations topic should point into the runnable decision-loop workbench.
     */
    public function test_llm_foundations_topic_links_to_decision_loop_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'vibe-coding'.DIRECTORY_SEPARATOR."prompting.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} prompting content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $llmItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'LLM') || str_contains((string) ($item['body'] ?? ''), 'Markov')) {
                    $llmItem = $item;
                    break;
                }
            }

            $this->assertIsArray($llmItem, "Missing {$language} LLM foundations item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/llm-decision-loop-plan',
                $llmItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The Predictive AI versus Generative AI topic should point into the runnable LLM workbench.
     */
    public function test_predictive_generative_ai_topic_links_to_llm_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'vibe-coding'.DIRECTORY_SEPARATOR."prompting.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} prompting content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $aiTypeItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'Predictive AI') && str_contains((string) ($item['title'] ?? ''), 'Generative AI')) {
                    $aiTypeItem = $item;
                    break;
                }
            }

            $this->assertIsArray($aiTypeItem, "Missing {$language} Predictive AI versus Generative AI item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/llm-decision-loop-plan',
                $aiTypeItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The RAG patterns topic should point into the runnable RAG strategy workbench.
     */
    public function test_rag_patterns_topic_links_to_strategy_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'vibe-coding'.DIRECTORY_SEPARATOR."prompting.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} prompting content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $ragItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/rag-strategy-plan') {
                    $ragItem = $item;
                    break;
                }
            }

            $this->assertIsArray($ragItem, "Missing {$language} RAG patterns item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/rag-strategy-plan',
                $ragItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The agent memory topic should point into the LLM decision-loop workbench.
     */
    public function test_agent_memory_topic_links_to_llm_decision_loop_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'vibe-coding'.DIRECTORY_SEPARATOR."prompting.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} prompting content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $memoryItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), $language === 'en' ? 'memory types' : 'Memory Cốt Lõi')) {
                    $memoryItem = $item;
                    break;
                }
            }

            $this->assertIsArray($memoryItem, "Missing {$language} AI agent memory item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/llm-decision-loop-plan',
                $memoryItem['links'][0]['href'] ?? null
            );
            $this->assertStringContainsString('working', strtolower(json_encode($memoryItem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
    }

    /**
     * The landing page should feature the AI agent memory topic.
     */
    public function test_landing_page_features_ai_agent_memory_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} site content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $memoryCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/vibe-coding/prompting.html' && str_contains((string) ($card['summary'] ?? ''), $language === 'en' ? 'working' : 'working')) {
                    $memoryCard = $card;
                    break;
                }
            }

            $this->assertIsArray($memoryCard, "Missing {$language} AI agent memory landing card.");
        }
    }

    /**
     * The static Hub workbench catalog should describe the expanded chatbot context strategy.
     */
    public function test_static_hub_catalog_describes_rag_long_context_cag_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $sections = $data['pages']['practice']['sections'] ?? [];
            $workbenchItem = null;

            foreach ($sections as $section) {
                foreach (($section['items'] ?? []) as $item) {
                    if (($item['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/rag-strategy-plan') {
                        $workbenchItem = $item;
                        break 2;
                    }
                }
            }

            $this->assertIsArray($workbenchItem, "Missing {$language} RAG workbench catalog item.");
            $this->assertStringContainsString('Long Context', (string) ($workbenchItem['body'] ?? ''));
            $this->assertStringContainsString('CAG', (string) ($workbenchItem['body'] ?? ''));
            $this->assertContains(
                $language === 'en'
                    ? 'RAG, Long Context, CAG, and hybrid context strategy routing for AI chatbots'
                    : 'RAG, Long Context, CAG và hybrid context strategy routing cho chatbot AI',
                $workbenchItem['concepts'] ?? []
            );
        }
    }

    /**
     * The System Design tradeoff topic should point into the runnable Hub workbench.
     */
    public function test_system_design_tradeoff_topic_links_to_tradeoff_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'interview'.DIRECTORY_SEPARATOR."senior.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} senior interview content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $tradeoffItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/system-design-tradeoff-plan') {
                    $tradeoffItem = $item;
                    break;
                }
            }

            $this->assertIsArray($tradeoffItem, "Missing {$language} System Design tradeoff item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/system-design-tradeoff-plan',
                $tradeoffItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The SIEM ELK topic should point into the runnable Hub workbench.
     */
    public function test_siem_elk_topic_links_to_planning_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'interview'.DIRECTORY_SEPARATOR."devops.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} devops interview content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $siemItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/siem-elk-plan') {
                    $siemItem = $item;
                    break;
                }
            }

            $this->assertIsArray($siemItem, "Missing {$language} SIEM ELK item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/siem-elk-plan',
                $siemItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The React render optimization topic should point into the runnable Hub workbench.
     */
    public function test_react_render_optimization_topic_links_to_planning_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."frontend.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} Laravel frontend content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $reactItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/react-render-optimization-plan') {
                    $reactItem = $item;
                    break;
                }
            }

            $this->assertIsArray($reactItem, "Missing {$language} React render optimization item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/react-render-optimization-plan',
                $reactItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The JavaScript closure topic should point into the runnable React render workbench.
     */
    public function test_javascript_closure_topic_links_to_planning_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."frontend.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} Laravel frontend content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $closureItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'Closure') || str_contains((string) ($item['title'] ?? ''), 'closure')) {
                    $closureItem = $item;
                    break;
                }
            }

            $this->assertIsArray($closureItem, "Missing {$language} JavaScript closure item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/react-render-optimization-plan',
                $closureItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The SQL Injection topic should point into the runnable Hub workbench.
     */
    public function test_sql_injection_topic_links_to_planning_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $sqlInjectionItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/sql-injection-defense-plan') {
                    $sqlInjectionItem = $item;
                    break;
                }
            }

            $this->assertIsArray($sqlInjectionItem, "Missing {$language} SQL Injection item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/sql-injection-defense-plan',
                $sqlInjectionItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The IDOR topic should be visible from the landing page and link into the runnable access review workbench.
     */
    public function test_idor_topic_is_published_and_links_to_access_review_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $siteContents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");
            $topicContents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($siteContents, "Unable to read {$language} site content.");
            $this->assertNotFalse($topicContents, "Unable to read {$language} auth security content.");

            $siteData = json_decode($siteContents, true, 512, JSON_THROW_ON_ERROR);
            $topicData = json_decode($topicContents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $siteData['pages']['landing']['cards'] ?? [];
            $items = $topicData['items'] ?? [];
            $landingCard = null;
            $idorItem = null;

            foreach ($cards as $card) {
                if (str_contains((string) ($card['title'] ?? ''), 'IDOR')
                    && ($card['href'] ?? null) === 'sites/laravel/auth-security.html') {
                    $landingCard = $card;
                    break;
                }
            }

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/idor-access-review') {
                    $idorItem = $item;
                    break;
                }
            }

            $this->assertIsArray($landingCard, "Missing {$language} IDOR landing card.");
            $this->assertStringContainsString('IDOR', $landingCard['title'] ?? '');
            $this->assertIsArray($idorItem, "Missing {$language} IDOR auth security item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/idor-access-review',
                $idorItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The CSRF topic should point into the runnable Hub workbench.
     */
    public function test_csrf_topic_links_to_planning_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $csrfItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/csrf-protection-plan') {
                    $csrfItem = $item;
                    break;
                }
            }

            $this->assertIsArray($csrfItem, "Missing {$language} CSRF item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/csrf-protection-plan',
                $csrfItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The XSS topic should point into the runnable escape preview workbench.
     */
    public function test_xss_topic_links_to_escape_preview_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $xssItem = null;

            foreach ($items as $item) {
                if (($item['links'][0]['href'] ?? null) === '{{HUB_BASE_URL}}/workbench/security-escape-preview') {
                    $xssItem = $item;
                    break;
                }
            }

            $this->assertIsArray($xssItem, "Missing {$language} XSS item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/security-escape-preview',
                $xssItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The Security Misconfiguration topic should point into the configuration readiness lab.
     */
    public function test_security_misconfiguration_topic_links_to_configuration_readiness_lab(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $item = null;

            foreach ($items as $candidate) {
                if (str_contains((string) ($candidate['title'] ?? ''), 'Security Misconfiguration')) {
                    $item = $candidate;
                    break;
                }
            }

            $this->assertIsArray($item, "Missing {$language} Security Misconfiguration item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/practice/configuration-readiness',
                $item['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The Dependency Injection topic should point from static content into the runnable Hub workbench.
     */
    public function test_dependency_injection_topic_links_to_refactor_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."container-architecture.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} container architecture content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $dependencyInjectionItem = null;

            foreach ($items as $item) {
                if (str_starts_with((string) ($item['title'] ?? ''), '89. Dependency Injection')) {
                    $dependencyInjectionItem = $item;
                    break;
                }
            }

            $this->assertIsArray($dependencyInjectionItem, "Missing {$language} Dependency Injection item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/dependency-injection-refactor',
                $dependencyInjectionItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The Clean Architecture layering topic should point from static content into the Hub exercise.
     */
    public function test_clean_architecture_layering_topic_links_to_hub_exercise(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."container-architecture.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} container architecture content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $layeringItem = null;

            foreach ($items as $item) {
                if (str_starts_with((string) ($item['title'] ?? ''), '90. Clean Architecture P6')) {
                    $layeringItem = $item;
                    break;
                }
            }

            $this->assertIsArray($layeringItem, "Missing {$language} Clean Architecture P6 item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/layered-architecture-decision',
                $layeringItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The JWT storage topic should point from static content into the runnable Hub workbench.
     */
    public function test_jwt_storage_topic_links_to_token_storage_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $jwtStorageItem = null;

            foreach ($items as $item) {
                if (str_starts_with((string) ($item['title'] ?? ''), '104.')) {
                    $jwtStorageItem = $item;
                    break;
                }
            }

            $this->assertIsArray($jwtStorageItem, "Missing {$language} JWT storage item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/jwt-token-storage-plan',
                $jwtStorageItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The JWT revocation topic should point from static content into the runnable Hub workbench.
     */
    public function test_jwt_revocation_topic_links_to_revocation_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $jwtRevocationItem = null;

            foreach ($items as $item) {
                if (str_starts_with((string) ($item['title'] ?? ''), '105.')) {
                    $jwtRevocationItem = $item;
                    break;
                }
            }

            $this->assertIsArray($jwtRevocationItem, "Missing {$language} JWT revocation item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/jwt-revocation-plan',
                $jwtRevocationItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The OAuth topic should point from static content into the runnable Hub workbench.
     */
    public function test_oauth_implicit_flow_topic_links_to_oauth_flow_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $oauthItem = null;

            foreach ($items as $item) {
                if (str_starts_with((string) ($item['title'] ?? ''), '106.')) {
                    $oauthItem = $item;
                    break;
                }
            }

            $this->assertIsArray($oauthItem, "Missing {$language} OAuth flow item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/oauth-flow-plan',
                $oauthItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The Client Credentials topic should point from static content into the OAuth workbench.
     */
    public function test_client_credentials_topic_links_to_oauth_flow_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $clientCredentialsItem = null;

            foreach ($items as $item) {
                if (str_starts_with((string) ($item['title'] ?? ''), '110.')) {
                    $clientCredentialsItem = $item;
                    break;
                }
            }

            $this->assertIsArray($clientCredentialsItem, "Missing {$language} Client Credentials item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/oauth-flow-plan',
                $clientCredentialsItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The PKCE topic should point from static content into the OAuth workbench.
     */
    public function test_pkce_topic_links_to_oauth_flow_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."auth-security.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} auth security content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $pkceItem = null;

            foreach ($items as $item) {
                if (str_starts_with((string) ($item['title'] ?? ''), '111.')) {
                    $pkceItem = $item;
                    break;
                }
            }

            $this->assertIsArray($pkceItem, "Missing {$language} PKCE item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/oauth-flow-plan',
                $pkceItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The landing page should feature the stack versus heap memory topic.
     */
    public function test_landing_page_features_stack_heap_topic(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} static content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $data['pages']['landing']['cards'] ?? [];
            $memoryCard = null;

            foreach ($cards as $card) {
                if (($card['href'] ?? null) === 'sites/php/advanced.html' && str_contains((string) ($card['summary'] ?? ''), 'heap')) {
                    $memoryCard = $card;
                    break;
                }
            }

            $this->assertIsArray($memoryCard, "Missing {$language} stack heap landing card.");
        }
    }

    /**
     * The PHP advanced content should include a stack versus heap question.
     */
    public function test_php_advanced_content_includes_stack_heap_question(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR."advanced.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} PHP advanced content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $questions = $data['questions'] ?? [];
            $memoryQuestion = null;

            foreach ($questions as $question) {
                if (str_contains((string) ($question['answer'] ?? ''), 'Heap') || str_contains((string) ($question['answer'] ?? ''), 'Heap giữ')) {
                    $memoryQuestion = $question;
                    break;
                }
            }

            $this->assertIsArray($memoryQuestion, "Missing {$language} stack heap question.");
        }
    }

    /**
     * The AI review topic should point from static content into the hallucination guard workbench.
     */
    public function test_ai_review_topic_links_to_hallucination_guard_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'vibe-coding'.DIRECTORY_SEPARATOR."ai-review.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} AI review content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $hallucinationItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'Hallucination') || str_contains((string) ($item['title'] ?? ''), 'hallucination')) {
                    $hallucinationItem = $item;
                    break;
                }
            }

            $this->assertIsArray($hallucinationItem, "Missing {$language} hallucination guard item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/ai-hallucination-guard-plan',
                $hallucinationItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The LSM Tree topic should point from static content into the runnable Hub workbench.
     */
    public function test_lsm_tree_topic_links_to_lsm_tree_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."performance-search.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} performance-search content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $lsmTreeItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'LSM Tree')) {
                    $lsmTreeItem = $item;
                    break;
                }
            }

            $this->assertIsArray($lsmTreeItem, "Missing {$language} LSM Tree item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/lsm-tree-plan',
                $lsmTreeItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The Covering Index topic should point into the database query workbench.
     */
    public function test_covering_index_topic_links_to_database_query_workbench(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en', 'vi'] as $language) {
            $contents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."performance-search.{$language}.json");

            $this->assertNotFalse($contents, "Unable to read {$language} performance-search content.");

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $items = $data['items'] ?? [];
            $coveringIndexItem = null;

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'Covering Index')) {
                    $coveringIndexItem = $item;
                    break;
                }
            }

            $this->assertIsArray($coveringIndexItem, "Missing {$language} Covering Index item.");
            $this->assertSame(
                '{{HUB_BASE_URL}}/workbench/collection-filter-preview',
                $coveringIndexItem['links'][0]['href'] ?? null
            );
        }
    }

    /**
     * The BFS and DFS topic should be visible from the landing page and link into traversal practice.
     */
    public function test_bfs_dfs_topic_is_published_on_landing_page_and_links_to_graph_traversal_pipeline(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (['en' => 'BFS', 'vi' => 'BFS'] as $language => $expectedTitlePart) {
            $siteContents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."site-content.{$language}.json");
            $topicContents = file_get_contents($root.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR."performance-search.{$language}.json");

            $this->assertNotFalse($siteContents, "Unable to read {$language} site content.");
            $this->assertNotFalse($topicContents, "Unable to read {$language} performance-search content.");

            $siteData = json_decode($siteContents, true, 512, JSON_THROW_ON_ERROR);
            $topicData = json_decode($topicContents, true, 512, JSON_THROW_ON_ERROR);
            $cards = $siteData['pages']['landing']['cards'] ?? [];
            $items = $topicData['items'] ?? [];

            $landingCard = null;
            $topic = null;

            foreach ($cards as $card) {
                if (str_contains((string) ($card['title'] ?? ''), $expectedTitlePart)
                    && ($card['href'] ?? null) === 'sites/laravel/performance-search.html') {
                    $landingCard = $card;
                    break;
                }
            }

            foreach ($items as $item) {
                if (str_contains((string) ($item['title'] ?? ''), 'BFS')
                    && str_contains((string) ($item['title'] ?? ''), 'DFS')) {
                    $topic = $item;
                    break;
                }
            }

            $this->assertIsArray($landingCard, "Missing {$language} BFS/DFS landing card.");
            $this->assertIsArray($topic, "Missing {$language} BFS/DFS performance-search topic.");
            $this->assertStringContainsString('function bfs', $topic['code'] ?? '');
            $this->assertStringContainsString('function dfs', $topic['code'] ?? '');
            $this->assertSame(
                '{{HUB_BASE_URL}}/practice/technology-learning-pipeline/graph-traversal?search=BFS%20DFS',
                $topic['links'][0]['href'] ?? null
            );
        }
    }
}
