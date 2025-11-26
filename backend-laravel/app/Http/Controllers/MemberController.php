<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::all();
        return response()->json($members);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:5',
            'role' => 'required|integer|in:0,1,2',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
        ]);
        $member = Member::create($validated);

        return response()->json([
            'message' => 'Member created successfully',
            'data' => $member
        ], 201);
    }
}
