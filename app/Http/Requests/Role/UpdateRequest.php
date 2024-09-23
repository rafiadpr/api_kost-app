<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateRequest extends FormRequest
{
    public $validator = null;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'nullable',
            'name' => 'nullable|max:100',
            'access' => 'nullable|max:255',
        ];
    }
}
