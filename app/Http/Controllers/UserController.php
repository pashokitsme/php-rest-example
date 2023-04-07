<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticableRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\RequireAdminRole;
use App\Http\Requests\RequireWaiterRole;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Str;

class UserController extends Controller
{
    function login(LoginRequest $req) {
        $req->user->api_token = Str::random();
        $req->user->save();
        return $this->json(['user_token' => $req->user->api_token]);
    }

    function logout(AuthenticableRequest $req) {
        $req->user->api_token = null;
        $req->user->save();
        return $this->json(['message' => 'logout']);
    }

    function getAll(RequireAdminRole $req) {
        return $this->json(array_map([UserResource::class, 'into'], User::all()->all()));
    }

    function create(CreateUserRequest $req) {
        $user = User::create($req->all());
        $user->save();
        return $this->json(['id' => $user->id, 'status' => 'created']);
    }
}
