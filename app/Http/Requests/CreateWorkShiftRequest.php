<?php

namespace App\Http\Requests;

class CreateWorkShiftRequest extends RequireAdminRole
{
    public function rules()
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start']
        ];
    }
}
