<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class UpdateRequest extends FormRequest
{
    use ConvertsBase64ToFiles; // Library untuk convert base64 menjadi File

    public $validator = null;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-user.jpg',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|max:100',
            'photo' => 'nullable|file|image',
            'email' => 'nullable|email|email:dns|unique:user_auth,email,'. $this->id,
            'password' => 'nullable|min:6',
            'phone_number' => 'nullable',
        ];
    }

    public function resetPassword()
    {
        return [
            'email' => 'required|email|email:dns'. $this->id,
            'password' => 'nullable|min:6',
        ];
    }
}
