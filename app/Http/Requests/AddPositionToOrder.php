<?php

namespace App\Http\Requests;

class AddPositionToOrder extends RequireWaiterRole
{
    function rules()
    {
        return [
            'menu_id' => ['required', 'integer', 'exists:menus,id'],
            'count' => ['required', 'integer']
        ];
    }
}
