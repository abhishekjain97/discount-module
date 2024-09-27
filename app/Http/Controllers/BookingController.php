<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
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
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $scheduleId = $request->schedule_id;

        $bookingData = [
            'user_id'           =>   $request->user_id,
            'booking_date'      =>   Carbon::now()->toDateString(),
            'total_amount'      =>   $request->total_amount,
            'discount'          =>   $request->discount,
            'for_member'        =>   $request->for_family_member,
        ];

        $booking = Booking::create($bookingData);

        $booking_id = $booking->id;

        if($booking_id) {
            foreach($request->schedule_id as $scheduleId) {
                $bookingItemData = [
                    'booking_id'   =>  $booking_id,
                    'schedule_id'  =>  $scheduleId
                ];

                BookingItem::create($bookingItemData);
            }
        }

        // Update user left for discount count based on discount type
        $discount = Discount::where('id', $request->discount_id)->firstOrFail();
        $discount->update([
            'user_left'  =>  $discount->user_left - 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Booking added successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
