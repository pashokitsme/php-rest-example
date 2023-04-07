<?php

namespace App\Http\Requests;

use App\Exceptions\LoginFailedException;
use App\Models\User;

class LoginRequest extends ApiRequest
{
    public User $user;

    public function authorize()
    {
       if ($user = User::where('password', $this->password)->where('login', $this->login)->first()) $this->user = $user;
       return $user;
    }

    protected function failedAuthorization()
    {
        throw new LoginFailedException();
    }

    public function rules()
    {
        return [
            'login' => ['required', 'string', 'exists:users,login'],
            'password' => ['required', 'string', 'exists:users,password']
        ];
    }
}
