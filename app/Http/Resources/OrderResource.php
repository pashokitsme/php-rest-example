<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends Resource
{
    public static function into($order) {
        return [
            'id' => $order->id,
            'table' => $order->table,
            'shift_workers' => $order->shift_worker,
            'status' => $order->status,
            'price' => $order->price,
            'created_at' => $order->created_at
        ];
    }
}
