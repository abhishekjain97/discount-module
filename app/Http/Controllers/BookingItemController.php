<?php

namespace App\Http\Controllers;

use App\Models\BookingItem;
use App\Http\Requests\StoreBookingItemRequest;
use App\Http\Requests\UpdateBookingItemRequest;

class BookingItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreBookingItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BookingItem $bookingItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookingItem $bookingItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingItemRequest $request, BookingItem $bookingItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingItem $bookingItem)
    {
        //
    }
}
