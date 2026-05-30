<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class JavascriptArrowThisLabWorkbenchTest extends TestCase
{
    /**
     * The JavaScript arrow this lab renders the analysis form.
     */
    public function test_javascript_arrow_this_lab_renders(): void
    {
        $response = $this->get('/workbench/javascript-arrow-this-lab');

        $response
            ->assertOk()
            ->assertSee('JavaScript Arrow This Lab')
            ->assertSee('POST /api/practice/javascript-arrow-this-lab')
            ->assertSee('Analyze arrow this')
            ->assertSee('Analysis Result')
            ->assertSee('renderArrowThisJson')
            ->assertSee('call', false)
            ->assertSee('apply', false)
            ->assertSee('bind', false);
    }

    /**
     * The API returns lexical-this analysis and interview guidance.
     */
    public function test_javascript_arrow_this_lab_api_returns_analysis(): void
    {
        $response = $this->postJson('/api/practice/javascript-arrow-this-lab', [
            'snippet' => <<<'JS'
const user = {
  name: 'Son',
  normal() {
    return this.name;
  },
  arrow: () => this.name,
};

user.normal();
user.arrow();
user.arrow.call({ name: 'An' });
JS,
            'scenario' => 'object-method',
            'interview_level' => 'senior',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.topic', 'javascript-arrow-this')
            ->assertJsonPath('data.detected_signals.has_arrow_function', true)
            ->assertJsonPath('data.detected_signals.has_normal_method', true)
            ->assertJsonPath('data.detected_signals.has_object_arrow_property', true)
            ->assertJsonPath('data.detected_signals.has_call_apply_bind', true)
            ->assertJsonPath('data.comparison_trace.0.expected_this', 'call-site object')
            ->assertJsonPath('data.comparison_trace.1.expected_this', 'lexical outer scope')
            ->assertJsonPath('data.comparison_trace.2.expected_this', 'unchanged lexical this')
            ->assertJsonPath('data.trap_checklist.1', 'Do not claim `obj.arrow()` makes `this` equal to `obj`.')
            ->assertJsonPath('data.knowledge_check.2.answer', 'No. They cannot rebind arrow-function `this`.')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter JavascriptArrowThisLabWorkbenchTest');
    }

    /**
     * The API validates required arrow-this lab fields.
     */
    public function test_javascript_arrow_this_lab_api_validates_payload(): void
    {
        $this->postJson('/api/practice/javascript-arrow-this-lab', [
            'snippet' => 'short',
            'scenario' => 'wrong',
            'interview_level' => 'expert',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['snippet', 'scenario', 'interview_level']);
    }
}
