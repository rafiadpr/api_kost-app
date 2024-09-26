<?php

namespace App\Http\Requests\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AssetRequest extends FormRequest
{
    public $validator = null;

    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function rules()
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    public function createRules(): array
    {
        return [
            'unit_id' => 'required|string|max:855',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }

    private function updateRules(): array
    {
        return [
            'unit_category_id' => 'nullable|string|max:855',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
