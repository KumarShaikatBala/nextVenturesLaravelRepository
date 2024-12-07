<?php

namespace App\Http\Controllers;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use HttpResponses;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }



    public function index(): jsonResponse
    {
        return $this->orderRepository->getAllOrders();
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 422, ['errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $orders = [];
            $productUpdates = [];
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                if ($product->stock < $productData['quantity']) {
                    return $this->error('Validation error', 422, ['error' => 'Not enough stock for product ID ' . $productData['product_id']]);
                }
                $total_price = $product->price * $productData['quantity'];
                $orders[] = [
                    'user_id' => auth()->id(),
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'total_price' => $total_price,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $productUpdates[] = [
                    'id' => $productData['product_id'],
                    'stock' => $product->stock - $productData['quantity'],
                ];
            }

            Order::insert($orders);
            foreach ($productUpdates as $update) {
                Product::where('id', $update['id'])->update(['stock' => $update['stock']]);
            }

            DB::commit();
            return $this->success(['data' => $orders], 'Orders created successfully.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error', 401, ['error' => $e->getMessage()]);
        }
    }

    public function show(Request $request): JsonResponse
    {
        $orderId = $request->route('id');
        return $this->success(['data' => $this->orderRepository->getOrderById($orderId)], 'Order found.', 200);
    }

    public function destroy(Request $request): JsonResponse
    {
      return  $this->orderRepository->deleteOrder($request->route('id'));
    }



}
