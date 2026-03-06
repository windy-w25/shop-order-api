<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;



class ReportController extends Controller
{
    // http://127.0.0.1:8000/api/reports/top-products?shop_id=1&from=2026-03-01&to=2026-03-10

    public function topProducts(Request $request)
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.name');

        if ($request->shop_id) {
            $query->where('orders.shop_id', $request->shop_id);
        }

        if ($request->from) {
            $query->whereDate('orders.created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('orders.created_at', '<=', $request->to);
        }

        $products = $query
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => $products
        ]);
    }
}
