<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class NameNormalizerWorkbenchTest extends TestCase
{
    /**
     * The name normalizer workbench renders normalized output.
     */
    public function test_name_normalizer_workbench_renders_output(): void
    {
        $response = $this->get('/workbench/name-normalizer?names=%20%20jane%20%20doe%0ALaravel%20labs');

        $response
            ->assertOk()
            ->assertSee('NameNormalizer')
            ->assertSee('Jane Doe')
            ->assertSee('Laravel Labs');
    }

    /**
     * The name normalizer workbench validates query input size.
     */
    public function test_name_normalizer_workbench_validates_input_size(): void
    {
        $response = $this->getJson('/workbench/name-normalizer?names='.str_repeat('x', 2001));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('names');
    }

    /**
     * The name normalizer API returns normalized names and metadata.
     */
    public function test_name_normalizer_api_returns_normalized_names(): void
    {
        $response = $this->postJson('/api/practice/name-normalizer', [
            'names' => ['  jane   doe ', '', 'LARAVEL labs'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.names.0', 'Jane Doe')
            ->assertJsonPath('data.names.1', 'Laravel Labs')
            ->assertJsonPath('meta.submitted', 3)
            ->assertJsonPath('meta.accepted', 2);
    }

    /**
     * The name normalizer API validates payload shape.
     */
    public function test_name_normalizer_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/name-normalizer', [
            'names' => [],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('names');
    }
}
