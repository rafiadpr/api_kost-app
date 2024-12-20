<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public $validator;

    public function authorize()
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function rules()
    {
        return [
            'email'    => 'required',
            'password' => 'required',
        ];
    }
}
