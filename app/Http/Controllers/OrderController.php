<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{

    public function index(Request $request){
        // http://127.0.0.1:8000/api/orders?shop_id=1&status=pending&from=2026-03-01&to=2026-03-10

        $query = Order::with('items.product');
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->paginate(10);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request)
    {

        // {
        //     "shop_id": 1,
        //     "items": [
        //         { "product_id": 2, "qty": 3 },
        //         { "product_id": 3, "qty": 10 }
        //     ]
        // }

        $data = $request->validated();

        $order = DB::transaction(function () use ($data) {

            $order = Order::create([
                'shop_id' => $data['shop_id'],
                'status' => 'pending',
                'total_price' => 0
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {

                $product = Product::lockForUpdate()->find($item['product_id']);

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Not enough stock for product {$product->name}");
                }

                $product->decrement('stock', $item['qty']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_price' => $product->price
                ]);

                $total += $product->price * $item['qty'];
            }

            $order->update([
                'total_price' => $total
            ]);

            return $order->load('items.product');
        });

        return response()->json([
            'success' => true,
            'data' => $order
        ], 201);
    }

    public function show(Order $order)
    {
        // http://127.0.0.1:8000/api/orders/1

        $order->load('items.product');
        return response()->json($order);
    }

    public function cancel(Order $order)
    {

        //http://127.0.0.1:8000/api/orders/1/cancel

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled'
            ], 422);
        }

        DB::transaction(function() use ($order) {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
            $order->update(['status' => 'cancelled']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled'
        ]);
    }

    public function edit(Order $order)
    {
        //
    }


    public function update(Request $request, Order $order)
    {
        //
    }

    public function destroy(Order $order)
    {
        //
    }
}
