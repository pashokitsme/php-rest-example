<?php

namespace App\Http\Requests;

class AddUserToWorkShiftRequest extends RequireAdminRole
{
    public function rules()
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id']
        ];
    }
}
