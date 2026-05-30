<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPipelineIndexTest extends TestCase
{
    /**
     * The technology pipeline index page lists discoverable pipeline links.
     */
    public function test_technology_pipeline_index_page_lists_pipeline_links(): void
    {
        $response = $this->get('/practice/technology-pipelines?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertSee('Technology pipelines from JSON content.')
            ->assertSee('api-validation')
            ->assertSee('Open API validation workbench')
            ->assertSee('Open pipeline')
            ->assertSee('Open code examples')
            ->assertSee('Open code examples API')
            ->assertSee('Open implementation lab')
            ->assertSee('Open lab API')
            ->assertSee('Open commit plan')
            ->assertSee('Open commit API')
            ->assertSee('Open portfolio artifact')
            ->assertSee('Open portfolio API')
            ->assertSee('Open interview pack')
            ->assertSee('Open interview API')
            ->assertSee('Open mastery checkpoint')
            ->assertSee('Open mastery API')
            ->assertSee('Open evidence archive')
            ->assertSee('Open archive API')
            ->assertSee('Open step')
            ->assertSee('Open step API')
            ->assertSee('Open taxonomy')
            ->assertSee('Open pipelines API');
    }

    /**
     * The technology pipeline index API returns grouped pipeline metadata and routes.
     */
    public function test_technology_pipeline_index_api_returns_grouped_pipeline_routes(): void
    {
        $response = $this->getJson('/api/practice/technology-pipelines?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/api-validation?family=laravel&language=en&search=api')
            ->assertJsonPath('data.meta.query.search', 'api')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'source_count',
                            'families',
                            'sources',
                            'sample',
                            'practice',
                            'related_workbench',
                            'pipeline_route',
                            'api_pipeline_route',
                            'code_examples_route',
                            'api_code_examples_route',
                            'implementation_lab_route',
                            'api_implementation_lab_route',
                            'commit_plan_route',
                            'api_commit_plan_route',
                            'portfolio_artifact_route',
                            'api_portfolio_artifact_route',
                            'interview_pack_route',
                            'api_interview_pack_route',
                            'mastery_checkpoint_route',
                            'api_mastery_checkpoint_route',
                            'evidence_archive_route',
                            'api_evidence_archive_route',
                            'workflow_steps' => [
                                '*' => [
                                    'label',
                                    'purpose',
                                    'route',
                                    'api_route',
                                ],
                            ],
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'record_count',
                        'technology_count',
                        'query',
                        'pipeline_count',
                    ],
                ],
            ]);
    }

    /**
     * Security topics appear as first-class pipelines from search results.
     */
    public function test_security_pipeline_index_routes_to_dedicated_security_pipelines(): void
    {
        $cases = [
            [
                '/api/practice/technology-pipelines?language=en&search=SQL%20Injection',
                'sql-injection-defense',
                '/workbench/sql-injection-defense-plan',
                '/practice/technology-learning-pipeline/sql-injection-defense?language=en&search=SQL+Injection',
                '/practice/technology-code-examples/sql-injection-defense?language=en&search=SQL+Injection',
                '/api/practice/technology-code-examples/sql-injection-defense?language=en&search=SQL+Injection',
                '/practice/technology-implementation-lab/sql-injection-defense?language=en&search=SQL+Injection',
                '/api/practice/technology-implementation-lab/sql-injection-defense?language=en&search=SQL+Injection',
            ],
            [
                '/api/practice/technology-pipelines?language=en&search=CSRF',
                'csrf-protection',
                '/workbench/csrf-protection-plan',
                '/practice/technology-learning-pipeline/csrf-protection?language=en&search=CSRF',
                '/practice/technology-code-examples/csrf-protection?language=en&search=CSRF',
                '/api/practice/technology-code-examples/csrf-protection?language=en&search=CSRF',
                '/practice/technology-implementation-lab/csrf-protection?language=en&search=CSRF',
                '/api/practice/technology-implementation-lab/csrf-protection?language=en&search=CSRF',
            ],
            [
                '/api/practice/technology-pipelines?language=en&search=XSS',
                'xss-defense',
                '/workbench/security-escape-preview',
                '/practice/technology-learning-pipeline/xss-defense?language=en&search=XSS',
                '/practice/technology-code-examples/xss-defense?language=en&search=XSS',
                '/api/practice/technology-code-examples/xss-defense?language=en&search=XSS',
                '/practice/technology-implementation-lab/xss-defense?language=en&search=XSS',
                '/api/practice/technology-implementation-lab/xss-defense?language=en&search=XSS',
            ],
            [
                '/api/practice/technology-pipelines?language=en&search=Security%20Misconfiguration',
                'security-misconfiguration',
                '/practice/configuration-readiness',
                '/practice/technology-learning-pipeline/security-misconfiguration?language=en&search=Security+Misconfiguration',
                '/practice/technology-code-examples/security-misconfiguration?language=en&search=Security+Misconfiguration',
                '/api/practice/technology-code-examples/security-misconfiguration?language=en&search=Security+Misconfiguration',
                '/practice/technology-implementation-lab/security-misconfiguration?language=en&search=Security+Misconfiguration',
                '/api/practice/technology-implementation-lab/security-misconfiguration?language=en&search=Security+Misconfiguration',
            ],
            [
                '/api/practice/technology-pipelines?language=en&search=IDOR',
                'idor-access-control',
                '/workbench/idor-access-review',
                '/practice/technology-learning-pipeline/idor-access-control?language=en&search=IDOR',
                '/practice/technology-code-examples/idor-access-control?language=en&search=IDOR',
                '/api/practice/technology-code-examples/idor-access-control?language=en&search=IDOR',
                '/practice/technology-implementation-lab/idor-access-control?language=en&search=IDOR',
                '/api/practice/technology-implementation-lab/idor-access-control?language=en&search=IDOR',
            ],
        ];

        foreach ($cases as [$url, $technology, $workbench, $pipeline, $examples, $apiExamples, $lab, $apiLab]) {
            $this->getJson($url)
                ->assertOk()
                ->assertJsonPath('data.items.0.technology', $technology)
                ->assertJsonPath('data.items.0.related_workbench.path', $workbench)
                ->assertJsonPath('data.items.0.pipeline_route', $pipeline)
                ->assertJsonPath('data.items.0.code_examples_route', $examples)
                ->assertJsonPath('data.items.0.api_code_examples_route', $apiExamples)
                ->assertJsonPath('data.items.0.implementation_lab_route', $lab)
                ->assertJsonPath('data.items.0.api_implementation_lab_route', $apiLab);
        }
    }

    /**
     * PHP stack and heap pipeline index links through the full evidence workflow.
     */
    public function test_php_stack_heap_pipeline_index_routes_to_full_runtime_memory_workflow(): void
    {
        $this->getJson('/api/practice/technology-pipelines?language=en&search=stack%20memory')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'php')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.code_examples_route', '/practice/technology-code-examples/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.implementation_lab_route', '/practice/technology-implementation-lab/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.commit_plan_route', '/practice/technology-commit-plan/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.api_commit_plan_route', '/api/practice/technology-commit-plan/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.portfolio_artifact_route', '/practice/technology-portfolio-artifact/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.api_portfolio_artifact_route', '/api/practice/technology-portfolio-artifact/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.interview_pack_route', '/practice/technology-interview-pack/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.api_interview_pack_route', '/api/practice/technology-interview-pack/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.mastery_checkpoint_route', '/practice/technology-mastery-checkpoint/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.api_mastery_checkpoint_route', '/api/practice/technology-mastery-checkpoint/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.evidence_archive_route', '/practice/technology-evidence-archive/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.api_evidence_archive_route', '/api/practice/technology-evidence-archive/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.workflow_steps.0.label', 'Pipeline')
            ->assertJsonPath('data.items.0.workflow_steps.0.route', '/practice/technology-learning-pipeline/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.workflow_steps.0.api_route', '/api/practice/technology-learning-pipeline/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.workflow_steps.3.label', 'Commit plan')
            ->assertJsonPath('data.items.0.workflow_steps.3.api_route', '/api/practice/technology-commit-plan/php?language=en&search=stack+memory')
            ->assertJsonPath('data.items.0.workflow_steps.7.label', 'Evidence archive')
            ->assertJsonPath('data.items.0.workflow_steps.7.api_route', '/api/practice/technology-evidence-archive/php?language=en&search=stack+memory');
    }

    /**
     * Predictive AI pipeline index links through the full AI type comparison workflow.
     */
    public function test_predictive_generative_ai_pipeline_index_routes_to_full_ai_type_workflow(): void
    {
        $this->getJson('/api/practice/technology-pipelines?language=en&search=Predictive%20AI')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.code_examples_route', '/practice/technology-code-examples/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.implementation_lab_route', '/practice/technology-implementation-lab/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.commit_plan_route', '/practice/technology-commit-plan/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.portfolio_artifact_route', '/practice/technology-portfolio-artifact/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.interview_pack_route', '/practice/technology-interview-pack/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.mastery_checkpoint_route', '/practice/technology-mastery-checkpoint/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.evidence_archive_route', '/practice/technology-evidence-archive/llm-foundations?language=en&search=Predictive+AI')
            ->assertJsonPath('data.items.0.workflow_steps.3.label', 'Commit plan')
            ->assertJsonPath('data.items.0.workflow_steps.7.api_route', '/api/practice/technology-evidence-archive/llm-foundations?language=en&search=Predictive+AI');
    }

    /**
     * JavaScript closure pipeline index links through the full closure workflow.
     */
    public function test_javascript_closure_pipeline_index_routes_to_full_scope_workflow(): void
    {
        $this->getJson('/api/practice/technology-pipelines?language=en&search=JavaScript%20closure')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.code_examples_route', '/practice/technology-code-examples/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.implementation_lab_route', '/practice/technology-implementation-lab/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.commit_plan_route', '/practice/technology-commit-plan/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.portfolio_artifact_route', '/practice/technology-portfolio-artifact/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.interview_pack_route', '/practice/technology-interview-pack/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.mastery_checkpoint_route', '/practice/technology-mastery-checkpoint/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.evidence_archive_route', '/practice/technology-evidence-archive/javascript-closures?language=en&search=JavaScript+closure')
            ->assertJsonPath('data.items.0.workflow_steps.3.label', 'Commit plan')
            ->assertJsonPath('data.items.0.workflow_steps.7.api_route', '/api/practice/technology-evidence-archive/javascript-closures?language=en&search=JavaScript+closure');
    }

    /**
     * Arrow-function this pipeline index links through the same JavaScript interview workflow.
     */
    public function test_arrow_this_pipeline_index_routes_to_full_lexical_this_workflow(): void
    {
        $this->getJson('/api/practice/technology-pipelines?language=en&search=arrow%20function%20this')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/javascript-closures?language=en&search=arrow+function+this')
            ->assertJsonPath('data.items.0.code_examples_route', '/practice/technology-code-examples/javascript-closures?language=en&search=arrow+function+this')
            ->assertJsonPath('data.items.0.implementation_lab_route', '/practice/technology-implementation-lab/javascript-closures?language=en&search=arrow+function+this')
            ->assertJsonPath('data.items.0.interview_pack_route', '/practice/technology-interview-pack/javascript-closures?language=en&search=arrow+function+this')
            ->assertJsonPath('data.items.0.workflow_steps.5.label', 'Interview pack');
    }

    /**
     * Covering Index pipeline index links through the full database performance workflow.
     */
    public function test_covering_index_pipeline_index_routes_to_full_query_plan_workflow(): void
    {
        $this->getJson('/api/practice/technology-pipelines?language=en&search=Covering%20Index')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.code_examples_route', '/practice/technology-code-examples/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.implementation_lab_route', '/practice/technology-implementation-lab/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.commit_plan_route', '/practice/technology-commit-plan/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.portfolio_artifact_route', '/practice/technology-portfolio-artifact/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.interview_pack_route', '/practice/technology-interview-pack/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.mastery_checkpoint_route', '/practice/technology-mastery-checkpoint/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.evidence_archive_route', '/practice/technology-evidence-archive/database-eloquent?language=en&search=Covering+Index')
            ->assertJsonPath('data.items.0.workflow_steps.2.label', 'Implementation lab')
            ->assertJsonPath('data.items.0.workflow_steps.7.api_route', '/api/practice/technology-evidence-archive/database-eloquent?language=en&search=Covering+Index');
    }

    /**
     * Database locking pipeline index links through the full concurrency workflow.
     */
    public function test_database_locking_pipeline_index_routes_to_full_concurrency_workflow(): void
    {
        $this->getJson('/api/practice/technology-pipelines?language=en&search=lockForUpdate')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/database-locking-plan')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.code_examples_route', '/practice/technology-code-examples/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.implementation_lab_route', '/practice/technology-implementation-lab/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.commit_plan_route', '/practice/technology-commit-plan/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.portfolio_artifact_route', '/practice/technology-portfolio-artifact/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.interview_pack_route', '/practice/technology-interview-pack/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.mastery_checkpoint_route', '/practice/technology-mastery-checkpoint/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.evidence_archive_route', '/practice/technology-evidence-archive/database-eloquent?language=en&search=lockForUpdate')
            ->assertJsonPath('data.items.0.workflow_steps.2.label', 'Implementation lab')
            ->assertJsonPath('data.items.0.workflow_steps.7.api_route', '/api/practice/technology-evidence-archive/database-eloquent?language=en&search=lockForUpdate');
    }
}
