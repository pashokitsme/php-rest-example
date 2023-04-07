<?php

namespace App\Http\Resources;

use App\Models\WorkShift;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends Resource
{
    public static function into(WorkShift $shift, int $total, $orders) {
        return [
            'id' => $shift->id,
            'start' => $shift->start,
            'end' => $shift->end,
            'active' => $shift->active,
            'orders' => $orders,
            'amount_for_all' => $total,
        ];
    }
}
