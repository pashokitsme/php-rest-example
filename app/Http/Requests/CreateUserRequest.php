<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends RequireAdminRole
{
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'login' => ['required', 'string', 'unique:users,login'],
            'password' => ['required', 'string'],
            'role_id' => ['required', 'integer', 'exists:roles,id']
        ];
    }
}
