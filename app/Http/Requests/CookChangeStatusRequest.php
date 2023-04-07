<?php

namespace App\Http\Requests;

class CookChangeStatusRequest extends RoleSpecifiedRequest
{
    protected string $requiredRole = 'cook';

    function rules()
    {
        return [
            'status' => 'required', 'string', 'in:preparing,ready'
        ];
    }
}
