<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function worker() {
        return $this->belongsTo(ShiftWorkers::class, 'shift_worker_id');
    }

    public function status() {
        return $this->belongsTo(StatusOrder::class, 'status_order_id');
    }

    public function table() {
        return $this->belongsTo(Table::class, 'table_id');
    }
}
