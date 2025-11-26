<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        return Store::all();
    }

    public function show($id)
    {
        return Store::with('products')->findOrFail($id);
    }

    public function store(Request $request)
    {
        // Add admin check here

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:stores',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $store = Store::create($request->all());

        return response()->json($store, 201);
    }

    public function update(Request $request, $id)
    {
        // Add authorization check here

        $store = Store::findOrFail($id);
        $store->update($request->all());

        return response()->json($store);
    }
}
