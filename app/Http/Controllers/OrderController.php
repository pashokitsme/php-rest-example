<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPositionToOrder;
use App\Http\Requests\AuthenticableRequest;
use App\Http\Requests\CookChangeStatusRequest;
use App\Http\Requests\CreateOrder;
use App\Http\Requests\RequireCookRole;
use App\Http\Requests\WaiterChangeStatusRequest;
use App\Http\Requests\RequireWaiterRole;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use App\Models\Order;
use App\Models\OrderMenu;
use App\Models\ShiftWorkers;
use App\Models\StatusOrder;
use App\Models\Table;
use App\Models\User;
use App\Models\WorkShift;

class OrderController extends Controller
{
    function orders(AuthenticableRequest $req, $id) {
        if (!$shift = WorkShift::find($id))
            return $this->error('WorkShift ' . $id . ' not found', 422);

        $orders = ShiftWorkers::where('work_shift_id', $id)
            ->select('shift_workers.id')
            ->join('users', 'users.id', '=', 'shift_workers.user_id')
            ->join('orders', 'orders.shift_worker_id', '=', 'shift_workers.id')
            ->join('tables', 'tables.id', '=', 'orders.table_id')
            ->join('status_orders', 'status_order_id', '=', 'orders.status_order_id')
            ->join('order_menus', 'order_menus.order_id', '=', 'orders.id')
            ->join('menus', 'order_menus.menu_id', '=', 'menus.id')
            ->select(
                'orders.id as id',
                'tables.name as table',
                'users.name as shift_worker',
                'status_orders.name as status',
                'menus.price as price',
                'order_menus.count as count',
                'orders.created_at'
            )
            ->get();

        $total_price = 0;
        foreach ($orders as $order) {
            $total_price += $order->price * $order->count;
        }

        $orders = array_map([OrderResource::class, 'into'], $orders->all());
        $outcome = OrdersResource::into($shift, $total_price, $orders);

        return $this->json($outcome);
    }

    function create(CreateOrder $req) {
        $shift = WorkShift::find($req->work_shift_id);
        if ($shift->active) return $this->error('Forbidden. The shift must be active!', 403);
        if (!$shift->users->contains($req->user->id)) return $this->error('Forbidden. You don\'t work this shift!', 403);
        $table = Table::find($req->table_id)->get();
        $order = Order::create([
            'number_of_person' => $req->number_of_person,
            'table_id' => $req->table_id,
            'shift_worker_id' => $req->work_shift_id,
            'status_order_id' => 1]);
        $order->save();
        return OrderResource::into($order);
    }

    function lookup(RequireWaiterRole $req, $id) {
        if (!$order = Order::find($id))
            return $this->error('Order ' . $id . ' does\'nt exists', 422);

        if (!$this->isOwns($req->user, $order))
            return $this->error('Forbidden. You did not accept this order!', 403);

        if ($order->status->code !== 'taken')
            return $this->error('Forbidden. You did not accept this order!', 403);

        $table = $order->table->name;
        $status = $order->status->name;
        $worker = $order->worker->user->name;
        $create_at = $order->create_at;
        $positions = Order::select("orders.*")
            ->where('orders.id', $id)
            ->join('order_menus', 'order_menus.id', '=', 'orders.id')
            ->join('menus', 'menus.id', '=', 'order_menus.id')
            ->select(
                'orders.id as id',
                'order_menus.count as count',
                'menus.name as position',
                'menus.price as price')
            ->get()->all();

        return $this->json(
            [
                'id' => $id,
                'table' => $table,
                'shift_workers' => $worker,
                'created_at' => $create_at,
                'status' => $status,
                'positions' => $positions
            ]
        );
    }

    function changeStatusAsWaiter(WaiterChangeStatusRequest $req, $id) {
        if (!$order = Order::find($id))
            return $this->error('Order ' . $id . ' does\'nt exists', 422);

        if (!$this->isOwns($req->user, $order))
            return $this->error('Forbidden. You did not accept this order!', 403);

        return $this->changeStatus($order, $req->status, ['cancelled', 'paid-up']);
    }

    function changeStatusAsCook(CookChangeStatusRequest $req, $id) {
        if (!$order = Order::find($id))
            return $this->error('Order ' . $id . ' does\'nt exists', 422);

        if (!$this->isOwns($req->user, $order))
            return $this->error('Forbidden. You did not accept this order!', 403);

        return $this->changeStatus($order, $req->status, ['preparing', 'ready']);
    }

    function changeStatus($order, $status, $allowed) {
        if (!in_array($order->status->code, $allowed))
            return $this->error('Forbidden. You did not accept this order!', 403);

        if (!StatusOrder::all()->contains('code', $status))
            return $this->error('Forbidden. Can\'t change existing order status!', 403);

        if (!$order->worker->shift->active)
            return $this->error('Forbidden. You can\'t change the order status of closed shift!', 403);

        $order->status_order_id = StatusOrder::where('code', $status)->first()->id;
        $order->save();

        return $this->json(['id' => $order->id, 'status' => $status]);
    }

    function addPosition(AddPositionToOrder $req, $id) {
        if (!$order = Order::find($id))
            return $this->error('Order ' . $id . ' does\'nt exists', 422);

        if (!$this->isOwns($req->user, $order))
            return $this->error('Forbidden. You did not accept this order!', 403);

        $order = OrderMenu::create(['order_id' => $order->id, 'menu_id' => $req->menu_id, 'count' => $req->count]);
        $order->save();
        return $order;
    }

    function isOwns(User $user, Order $order): bool {
        return ShiftWorkers::where('work_shift_id', $user->id)->where('user_id', $order->shift_worker_id)->count() > 0;
    }

    function lookupTaken(RequireCookRole $req, $id) {
        if (!$order = Order::find($id))
            return $this->error('Order ' . $id . ' does\'nt exists', 422);

        if (!$this->isOwns($req->user, $order))
            return $this->error('Forbidden. You did not accept this order!', 403);

        $table = $order->table->name;
        $status = $order->status->name;
        $worker = $order->worker->user->name;
        $create_at = $order->create_at;
        $positions = Order::select("orders.*")
            ->where('orders.id', $id)
            ->join('order_menus', 'order_menus.id', '=', 'orders.id')
            ->join('menus', 'menus.id', '=', 'order_menus.id')
            ->select(
                'orders.id as id',
                'order_menus.count as count',
                'menus.name as position',
                'menus.price as price')
            ->get()->all();

        return $this->json(
            [
                'id' => $id,
                'table' => $table,
                'shift_workers' => $worker,
                'created_at' => $create_at,
                'status' => $status,
                'positions' => $positions
            ]
        );
    }
}
