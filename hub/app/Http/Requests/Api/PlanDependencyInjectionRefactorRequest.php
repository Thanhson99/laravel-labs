<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanDependencyInjectionRefactorRequest extends FormRequest
{
    /**
     * Allow public dependency-injection refactor planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for dependency-injection refactor input.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'class_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'manual_dependency' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'dependency_role' => ['required', 'string', 'min:6', 'max:140', 'regex:/^[A-Za-z0-9 _.,-]+$/'],
            'contract_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'method_name' => ['required', 'string', 'min:3', 'max:40', 'regex:/^[A-Za-z][A-Za-z0-9_]*$/'],
            'fake_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'binding_lifetime' => ['required', 'string', Rule::in(['bind', 'singleton'])],
        ];
    }

    /**
     * Return normalized dependency-injection refactor input.
     *
     * @return array{class_name: string, manual_dependency: string, dependency_role: string, contract_name: string, method_name: string, fake_name: string, binding_lifetime: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'class_name' => trim((string) $validated['class_name']),
            'manual_dependency' => trim((string) $validated['manual_dependency']),
            'dependency_role' => trim((string) $validated['dependency_role']),
            'contract_name' => trim((string) $validated['contract_name']),
            'method_name' => trim((string) $validated['method_name']),
            'fake_name' => trim((string) $validated['fake_name']),
            'binding_lifetime' => (string) $validated['binding_lifetime'],
        ];
    }
}
