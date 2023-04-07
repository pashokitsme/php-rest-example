<?php

namespace App\Http\Requests;

use App\Models\User;

class AuthenticableRequest extends ApiRequest
{
    public User $user;

    public function authorize(): bool
    {
        $bearer = request()->bearerToken();
        if (!$bearer) return false;
        if ($user = User::where('api_token', $bearer)->first()) $this->user = $user;
        return (bool)$user;
    }
}
