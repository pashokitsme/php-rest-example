<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserToWorkShiftRequest;
use App\Http\Requests\CreateWorkShiftRequest;
use App\Http\Requests\RequireAdminRole;
use App\Http\Resources\WorkShiftResource;
use App\Models\WorkShift;

class WorkShiftController extends Controller
{
    function create(CreateWorkShiftRequest $req) {
        $shift = WorkShift::create($req->all());
        $shift->save();
        return $this->json(['id' => $shift->id, 'start' => $shift->start, 'end' => $shift->end]);
    }

    function close(RequireAdminRole $req, $id) {
        if (!$shift = WorkShift::find($id))
            return $this->error('not_found', 'WorkShift ' . $id . ' not found');

        if (!$shift->active)
            return $this->error('Forbidden. The shift is already closed!', 403);

        $shift->active = false;
        $shift->save();
        return $this->json(WorkShiftResource::into($shift));
    }

    function open(RequireAdminRole $req, $id) {
        if (!$shift = WorkShift::find($id))
            return $this->error('WorkShift ' . $id . ' not found', 422);

        if (WorkShift::where('active', 1)->count() > 0) {
            return $this->error('Forbidden. There are open shifts!', 403);
        }

        $shift->active = true;
        $shift->save();
        return $this->json(WorkShiftResource::into($shift));

    }

    function addUser(AddUserToWorkShiftRequest $req, $id) {
        if (!$shift = WorkShift::find($id))
            return $this->error('WorkShift ' . $id . ' not found', 422);

        $created = $shift->addUser($req->user_id);
        $shift->save();
        return $created
            ? $this->json(['id_user' => $req->user_id, 'status' => 'added'], 201)
            : $this->error('Forbidden. The worker is already on shift', 403);
    }


}
