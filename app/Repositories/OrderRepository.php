<?php

namespace App\Repositories;

use App\Http\Resources\OrderResource;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Traits\HttpResponses;

class OrderRepository implements OrderRepositoryInterface
{
    use HttpResponses;

    public function getAllOrders()
    {
        $query = Order::with('user:id,name,email', 'product:id,name,price,stock')->latest();

        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        $orders = OrderResource::collection($query->paginate(10));
        if ($orders->isEmpty()) {
            return $this->error('No orders found.', 404);
        }
        return $this->success(['data' => $orders->response()
            ->getData(true)

        ], 'Orders found.', 200);
    }

    public function getOrderById($orderId)
    {
        return Order::findOrFail($orderId);
    }

    public function deleteOrder($orderId)
    {
        try {
            $order = Order::find($orderId);
            if ($order) {
                $order->delete();
                return $this->success(['data' => $order], 'Order deleted.', 200);
            } else {
                return $this->error('Not found', 404, ['error' => 'Product not found.']);
            }
        } catch (\Exception $e) {
            return $this->error('Error', 401, ['error' => $e->getMessage()]);
        }


        /*  return Order::destroy($orderId);*/
    }

    public function createOrder(array $orderDetails)
    {
        return Order::create($orderDetails);
    }

    public function updateOrder($orderId, array $newDetails)
    {
        return Order::whereId($orderId)->update($newDetails);
    }
}
