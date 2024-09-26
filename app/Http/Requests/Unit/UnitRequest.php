<?php

namespace App\Http\Requests\Unit;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class UnitRequest extends FormRequest
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
            'unit_category_id' => 'required|string|max:855',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'details.*.description' => 'required|string',
            'details.*.is_available' => 'required|numeric|max:1',
        ];
    }

    private function updateRules(): array
    {
        return [
            'unit_category_id' => 'nullable|string|max:855',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'is_available' => 'Status',
            'field_category_id' => 'Category'
        ];
    }
}
