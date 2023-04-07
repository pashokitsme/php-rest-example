<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class ApiRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [];
    }

    protected function failedAuthorization()
    {
        throw new UnauthorizedException();
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            "code" => 422,
            "message" => "Validation failed",
            "errors" => $validator->errors()
        ]), 422);
    }
}
