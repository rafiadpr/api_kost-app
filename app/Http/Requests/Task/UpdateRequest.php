<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateRequest extends FormRequest
{
    public $validator = null;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_auth_id' => 'nullable',
            'description' => 'nullable',
            'status' => 'nullable',
        ];
    }
}
