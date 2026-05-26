<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeNoteTest extends TestCase
{
    /**
     * The thin-controller note page renders service-backed content.
     */
    public function test_thin_controller_note_page_renders_service_content(): void
    {
        $response = $this->get('/practice-notes/thin-controller');

        $response
            ->assertOk()
            ->assertSee('Thin controllers keep Laravel practice focused')
            ->assertSee('Controller receives a service through dependency injection.')
            ->assertSee('Feature test checks visible behavior.');
    }
}
