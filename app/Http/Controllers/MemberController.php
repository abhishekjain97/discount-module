<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::all();
        return response()->json([
            'status' => true,
            'message' => 'Members retrieved successfully',
            'data' => $members
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required|string|max:255',
            'relationship' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $member = Member::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Member created successfully',
            'data' => $member
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $member = Member::where('user_id', $id)->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Member found successfully',
            'data' => $member
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Request $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $member)
    {
        //
    }
}
