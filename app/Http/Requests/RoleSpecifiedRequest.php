<?php

namespace App\Http\Requests;

use App\Exceptions\ForbiddenException;


class RoleSpecifiedRequest extends AuthenticableRequest
{
    protected string $requiredRole;

    public function authorize(): bool
    {
        return parent::authorize() && $this->user->role->code === $this->requiredRole;
    }

    protected function failedAuthorization()
    {
        throw new ForbiddenException();
    }
}
