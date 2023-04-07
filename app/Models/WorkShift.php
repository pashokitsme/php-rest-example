<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function users() {
        return $this->belongsToMany(User::class, 'shift_workers', 'id', 'user_id');
    }

    public function addUser($userId) {
        if (ShiftWorkers::where('work_shift_id', $this->id)->where('user_id', $userId)) return false;
        ShiftWorkers::create(['work_shift_id' => $this->id, 'user_id' => $userId]);
        return true;
    }

    public function shifts() {
        return $this->belongsToMany(ShiftWorkers::class, 'shift_workers', 'work_shift_id');
    }

//    public function orders() {
//        return $this->belongsToMany(Order::class, 'orders', );
//    }
}
