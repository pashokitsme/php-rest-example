<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string $api_token
 * @property mixed|string $password
 * @property mixed|string $login
 * @property mixed|string $name
 * @property mixed|string $surname
 * @property Role $role
 * @property string $patronymic
 * @property int $id
 * @property mixed $status
 */
class User extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function role() {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    function shifts() {
        return $this->belongsToMany(WorkShift::class, 'shift_workers', 'user_id');
    }
}
