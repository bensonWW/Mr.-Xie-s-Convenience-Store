<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('user')->latest()->paginate(20);
    }

    public function show($id)
    {
        return Order::with('items.product', 'user')->findOrFail($id);
    }
}
