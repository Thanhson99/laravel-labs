<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\OopAbstractionDecisionService;
use PHPUnit\Framework\TestCase;

final class OopAbstractionDecisionServiceTest extends TestCase
{
    /**
     * Same-family classes with shared state should use an abstract class.
     */
    public function test_decision_recommends_abstract_class_for_shared_base_behavior(): void
    {
        $decision = (new OopAbstractionDecisionService)->decide([
            'scenario' => 'File Report',
            'relationship' => 'same-family',
            'shared_behavior' => 'read file contents',
            'needs_multiple_implementations' => false,
            'has_shared_state' => true,
        ]);

        $this->assertSame('abstract class', $decision['recommendation']);
        $this->assertStringContainsString('BaseFileReport', $decision['code_example']);
        $this->assertSame('Start by saying an abstract class is useful when a family of classes shares base behavior or state.', $decision['interview_rubric']['opening']);
        $this->assertSame('Use BaseFileReport when child classes share stable setup but still need custom behavior.', $decision['interview_rubric']['example']);
        $this->assertSame('abstract class', $decision['comparison_table'][1]['choice']);
        $this->assertSame('Create BaseImportJob when several import jobs share file parsing and retry metadata.', $decision['comparison_table'][1]['laravel_example']);
        $this->assertSame('app/Support/BaseFileReport.php', $decision['implementation_plan']['files'][0]);
        $this->assertSame('Move only stable shared state or base behavior into BaseFileReport.', $decision['implementation_plan']['steps'][0]);
        $this->assertSame('A class can extend only one parent class.', $decision['tradeoffs'][0]);
        $this->assertSame('Putting too much mutable state in the base class.', $decision['anti_patterns'][0]);
        $this->assertSame('Create a tiny test subclass of BaseFileReport to verify shared base behavior.', $decision['testing_strategy'][0]);
        $this->assertSame('Shared state or helpers', $decision['decision_matrix'][3]['signal']);
        $this->assertSame('Favors an abstract class for one object family.', $decision['decision_matrix'][3]['impact']);
    }

    /**
     * A single stable class should stay concrete until abstraction is earned.
     */
    public function test_decision_keeps_single_stable_class_concrete(): void
    {
        $decision = (new OopAbstractionDecisionService)->decide([
            'scenario' => 'Invoice Formatter',
            'relationship' => 'single-class',
            'shared_behavior' => '',
            'needs_multiple_implementations' => false,
            'has_shared_state' => false,
        ]);

        $this->assertSame('concrete class', $decision['recommendation']);
        $this->assertStringContainsString('Keep this concrete', $decision['code_example']);
        $this->assertSame('Start by saying simple concrete code is often the right first choice.', $decision['interview_rubric']['opening']);
        $this->assertSame('Keep InvoiceFormatter concrete until another implementation, shared base behavior, or testing need appears.', $decision['interview_rubric']['example']);
        $this->assertSame('concrete class', $decision['comparison_table'][2]['choice']);
        $this->assertSame('Keep InvoiceFormatter concrete until another formatter or caller contract exists.', $decision['comparison_table'][2]['laravel_example']);
        $this->assertSame('app/Services/InvoiceFormatter.php', $decision['implementation_plan']['files'][0]);
        $this->assertSame('No abstraction was added only for future guesses.', $decision['implementation_plan']['review'][1]);
        $this->assertSame('Concrete code is easier to read when there is only one behavior.', $decision['tradeoffs'][0]);
        $this->assertSame('Keeping a concrete class after multiple callers need different implementations.', $decision['anti_patterns'][0]);
        $this->assertSame('Write direct unit tests for InvoiceFormatter before extracting any abstraction.', $decision['testing_strategy'][0]);
        $this->assertSame('single stable class', $decision['decision_matrix'][0]['value']);
        $this->assertSame('Weakens the case for adding an interface today.', $decision['decision_matrix'][1]['impact']);
    }
}
