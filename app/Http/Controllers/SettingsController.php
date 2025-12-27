<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json([
            // Use bcmul for precision when converting to cents
            'free_shipping_threshold' => (int) bcmul((string) Setting::get('free_shipping_threshold', 1000), '100', 0),
            'shipping_fee' => (int) bcmul((string) Setting::get('shipping_fee', 60), '100', 0),
        ]);
    }
}
