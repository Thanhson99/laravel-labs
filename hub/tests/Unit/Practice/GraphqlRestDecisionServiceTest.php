<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\GraphqlRestDecisionService;
use PHPUnit\Framework\TestCase;

final class GraphqlRestDecisionServiceTest extends TestCase
{
    /**
     * GraphQL recommendations include the operational controls that make the choice safe.
     */
    public function test_graphql_recommendation_includes_operational_controls(): void
    {
        $plan = (new GraphqlRestDecisionService)->plan([
            'client_type' => 'bff',
            'data_shape' => 'graph-shaped',
            'field_flexibility' => 'high',
            'cache_priority' => 'medium',
            'relationship_depth' => 'deep',
            'team_graphql_experience' => 'strong',
            'authorization_complexity' => 'high',
        ]);

        $this->assertSame('graphql', $plan['recommendation']['style']);
        $this->assertSame(7, $plan['score_breakdown']['graphql_score']);
        $this->assertSame(2, $plan['score_breakdown']['rest_score']);
        $this->assertSame(5, $plan['score_breakdown']['margin']);
        $this->assertSame('high', $plan['score_breakdown']['confidence']);
        $this->assertSame('high', $plan['risk_score']['level']);
        $this->assertContains('Set depth, complexity, pagination, and maximum result-size limits.', $plan['n_plus_one_plan']);
        $this->assertSame('GraphQL as a database tunnel', $plan['anti_patterns'][0]['pattern']);
        $this->assertSame('pilot schema', $plan['migration_path'][0]['phase']);
        $this->assertSame('Resolver latency or query count grows with nested list size.', $plan['reconsideration_triggers'][0]['trigger']);
        $this->assertContains('graphql_query_complexity by operation_name', $plan['observability_plan']['metrics']);
        $this->assertStringContainsString('Decision: Use GraphQL for this API boundary', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('Decision Score:', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('Confidence: high', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('relationship_depth: GraphQL +2, REST +0', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('GraphQL as a database tunnel', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('Reconsider This Decision When:', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('Observability Plan:', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('Verification Tests:', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('GraphQL is schema/query-oriented', $plan['interview_answer']);
    }

    /**
     * REST recommendations preserve simple endpoint and HTTP cache semantics.
     */
    public function test_rest_recommendation_keeps_http_contract_controls(): void
    {
        $plan = (new GraphqlRestDecisionService)->plan([
            'client_type' => 'public-api',
            'data_shape' => 'resource-crud',
            'field_flexibility' => 'low',
            'cache_priority' => 'high',
            'relationship_depth' => 'shallow',
            'team_graphql_experience' => 'none',
            'authorization_complexity' => 'medium',
        ]);

        $this->assertSame('rest', $plan['recommendation']['style']);
        $this->assertSame(0, $plan['score_breakdown']['graphql_score']);
        $this->assertSame(6, $plan['score_breakdown']['rest_score']);
        $this->assertSame('rest', $plan['score_breakdown']['winner']);
        $this->assertSame('high', $plan['score_breakdown']['confidence']);
        $this->assertSame('endpoint/resource contract', $plan['contract_shape']['style']);
        $this->assertContains('routes/api.php names stable endpoint contracts.', $plan['laravel_boundaries']);
        $this->assertSame('Endpoint explosion', $plan['anti_patterns'][0]['pattern']);
        $this->assertSame('contract inventory', $plan['migration_path'][0]['phase']);
        $this->assertSame('Clients add many round trips to compose one screen.', $plan['reconsideration_triggers'][0]['trigger']);
        $this->assertContains('http_cache_hit_ratio by route', $plan['observability_plan']['metrics']);
        $this->assertStringContainsString('Decision: Use REST for this API boundary', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('HTTP cache headers', $plan['decision_memo_markdown']);
        $this->assertStringContainsString('REST is endpoint/resource-oriented', $plan['interview_answer']);
    }
}
