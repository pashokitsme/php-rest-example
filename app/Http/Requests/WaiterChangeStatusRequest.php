<?php

namespace App\Http\Requests;

class WaiterChangeStatusRequest extends RoleSpecifiedRequest
{
    protected string $requiredRole = 'waiter';

    function rules()
    {
        return [
            'status' => 'required', 'string', 'in:paid-up,canceled'
        ];
    }
}
