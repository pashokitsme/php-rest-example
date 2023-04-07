<?php

namespace App\Http\Requests;

class CreateOrder extends RequireWaiterRole
{
    function rules()
    {
        return [
            'work_shift_id' => ['required', 'integer', 'exists:work_shifts,id'],
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'number_of_person' => ['required', 'integer']
        ];
    }
}
