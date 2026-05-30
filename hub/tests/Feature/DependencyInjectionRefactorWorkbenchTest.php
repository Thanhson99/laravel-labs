<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class DependencyInjectionRefactorWorkbenchTest extends TestCase
{
    /**
     * The Dependency Injection refactor workbench renders the runnable learning loop.
     */
    public function test_dependency_injection_refactor_workbench_renders(): void
    {
        $response = $this->get('/workbench/dependency-injection-refactor');

        $response
            ->assertOk()
            ->assertSee('Dependency Injection Refactor Workbench')
            ->assertSee('POST /api/practice/dependency-injection-refactor')
            ->assertSee('DependencyInjectionRefactorService')
            ->assertSee('Open binding planner')
            ->assertSee('Scenario preset')
            ->assertSee('Payment gateway')
            ->assertSee('External sync')
            ->assertSee('Contract method')
            ->assertSee('Binding lifetime')
            ->assertSee('Plan DI refactor');
    }

    /**
     * The Dependency Injection refactor API returns before, after, binding, and testing guidance.
     */
    public function test_dependency_injection_refactor_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/dependency-injection-refactor', [
            'class_name' => 'Report Controller',
            'manual_dependency' => 'Csv Report Exporter',
            'dependency_role' => 'export monthly report rows',
            'contract_name' => 'Report Exporter',
            'method_name' => 'export',
            'fake_name' => 'Fake Report Exporter',
            'binding_lifetime' => 'singleton',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.class', 'ReportController')
            ->assertJsonPath('data.dependency', 'CsvReportExporter')
            ->assertJsonPath('data.contract', 'ReportExporter')
            ->assertJsonPath('data.method', 'export')
            ->assertJsonPath('data.fake', 'FakeReportExporter')
            ->assertJsonPath('data.risk_level', 'normal')
            ->assertJsonPath('data.binding_method', 'singleton')
            ->assertJsonPath('data.binding', '$this->app->singleton(ReportExporter::class, CsvReportExporter::class);')
            ->assertJsonPath('data.contract_example', "interface ReportExporter\n{\n    public function export(array \$payload): string;\n}")
            ->assertJsonPath('data.fake_example', "final class FakeReportExporter implements ReportExporter\n{\n    public array \$calls = [];\n\n    public function export(array \$payload): string\n    {\n        \$this->calls[] = \$payload;\n\n        return 'fake-result';\n    }\n}")
            ->assertJsonPath('data.container_test_example', "public function test_container_resolves_report_exporter(): void\n{\n    \$this->assertInstanceOf(CsvReportExporter::class, \$this->app->make(ReportExporter::class));\n\n    \$this->app->instance(ReportExporter::class, new FakeReportExporter());\n\n    \$this->assertInstanceOf(FakeReportExporter::class, \$this->app->make(ReportExporter::class));\n}")
            ->assertJsonPath('data.files.0', 'app/Contracts/ReportExporter.php')
            ->assertJsonPath('data.refactor_map.0.file', 'app/Contracts/ReportExporter.php')
            ->assertJsonPath('data.refactor_map.0.change', 'Create the ReportExporter contract around the export monthly report rows behavior.')
            ->assertJsonPath('data.refactor_map.2.reason', 'The container can resolve the contract for constructor injection.')
            ->assertJsonPath('data.steps.0', 'Extract ReportExporter with a focused `export` method for the export monthly report rows behavior needed by ReportController.')
            ->assertJsonPath('data.steps.2', 'Bind ReportExporter to CsvReportExporter with `singleton` in a service provider.')
            ->assertJsonPath('data.review_checklist.0', 'No `new CsvReportExporter()` calls remain inside ReportController.')
            ->assertJsonPath('data.anti_pattern_scan.0', 'Search for `new CsvReportExporter(` inside controllers, services, jobs, and listeners.')
            ->assertJsonPath('data.risk_controls.0', 'Use FakeReportExporter in tests to prove the caller only depends on ReportExporter.')
            ->assertJsonPath('data.overengineering_guardrails.0', 'Use ReportExporter when export monthly report rows may need a fake, vendor swap, or alternate implementation.')
            ->assertJsonPath('data.verification_commands.0', 'php artisan test --filter ReportControllerTest')
            ->assertJsonPath('data.verification_commands.1', 'php artisan test --filter ReportExporterContainerTest')
            ->assertJsonPath('data.commit_checklist.0', 'The contract, implementation, binding, caller refactor, and fake are included in the same change.')
            ->assertJsonPath('data.warnings.0', 'Do not create the concrete implementation inside the controller or service after adding the constructor dependency.');
    }

    /**
     * Invalid Dependency Injection refactor payloads return validation errors.
     */
    public function test_dependency_injection_refactor_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/dependency-injection-refactor', [
            'class_name' => '<bad>',
            'manual_dependency' => 'x',
            'dependency_role' => '<script>',
            'contract_name' => '',
            'method_name' => '1bad',
            'fake_name' => 'no',
            'binding_lifetime' => 'forever',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['class_name', 'manual_dependency', 'dependency_role', 'contract_name', 'method_name', 'fake_name', 'binding_lifetime']);
    }
}
