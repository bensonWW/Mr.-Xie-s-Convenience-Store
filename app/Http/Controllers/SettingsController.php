<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json([
            'free_shipping_threshold' => (int) (Setting::get('free_shipping_threshold', 1000) * 100),
            'shipping_fee' => (int) (Setting::get('shipping_fee', 60) * 100),
        ]);
    }
}
