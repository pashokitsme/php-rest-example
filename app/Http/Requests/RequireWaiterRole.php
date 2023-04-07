<?php

namespace App\Http\Requests;

class RequireWaiterRole extends RoleSpecifiedRequest
{
    protected string $requiredRole = 'waiter';
}
