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
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function($q) use ($request) {
                if($request->shop_id) $q->where('shop_id', $request->shop_id);
                if($request->from) $q->whereDate('created_at','>=',$request->from);
                if($request->to) $q->whereDate('created_at','<=',$request->to);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product')
            ->get();

        return response()->json($topProducts);
    }
}
