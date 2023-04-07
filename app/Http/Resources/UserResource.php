<?php

namespace App\Http\Resources;

use App\Models\User;

class UserResource extends Resource
{
    public static function into(User $req) {
        return [
            'id' => $req->id,
            'name' => $req->name,
            'login' => $req->login,
            'group' => $req->role->name,
            'status' => $req->status
        ];
    }

    public function toArray($request)
    {
        return response()->json($this->resource);
    }
}
