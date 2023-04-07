<?php

namespace App\Http\Requests;

class RequireAdminRole extends RoleSpecifiedRequest
{
    protected string $requiredRole = 'admin';
}
