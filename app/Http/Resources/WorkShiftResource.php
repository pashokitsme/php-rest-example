<?php

namespace App\Http\Resources;

use App\Models\WorkShift;

class WorkShiftResource extends Resource
{
    public static function into(WorkShift $shift) {
        return [
            'id' => $shift->id,
            'start' => $shift->start,
            'end' => $shift->end,
            'active' => $shift->active
        ];
    }
}
