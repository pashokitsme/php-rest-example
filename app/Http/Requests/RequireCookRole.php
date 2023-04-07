<?php

namespace App\Http\Requests;

class RequireCookRole extends RoleSpecifiedRequest
{
    protected string $requiredRole = 'cook';
}
