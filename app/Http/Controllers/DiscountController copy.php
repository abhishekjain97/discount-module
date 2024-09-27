<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Booking;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public $discountData = null;
  
    /* 
        This method handles applying a discount for either a family member or a recurring booking. 
        It first retrieves the necessary request data (user_id, for_family_member, current_member, schedule_id, discount_type, and total). 
        Depending on whether the request is for a family member discount or a recurring discount, it calls the appropriate discount calculation method.
    */
    public function apply(Request $request) 
    {
        try {
            $userId = $request->user_id;
            $member_id = $request->current_member;
            $scheduleId = is_array($request->schedule_id) ? $request->schedule_id : json_decode($request->schedule_id, true);
            $discount_id = $request->discount_id;
            $total = $request->total;
            $discount = 0;

            // Validate inputs
            if (empty($userId) || $userId == 0 || empty($discount_id) || $discount_id == 0 || empty($total) || $total <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Missing required parameters.',
                    'discount' => 0
                ], 400);
            }


            // Get a discount baed on discount id and check is this discount is available or not
            $this->discountData = Discount::where('id', $discount_id)->where('user_left' ,'>', 0)->first();

            if (!$this->discountData) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired discount ID.',
                    'discount' => 0
                ], 404);
            }
            

            // Apply discount based on fixed and recurring
            if($member_id) { 
                // If discount for family
                $discount = $this->applyFamilyMemberDiscount($userId, $scheduleId, $member_id, $total);
            } else {
                // If it is a recurring discount
                $discount = $this->applyRecurringDiscount($userId, $scheduleId, $total);
            }

            
            return response()->json([
                'status' => true,
                'message' => 'Discount apply successfully',
                'discount' => $discount
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while applying the discount.',
                'error' => $e->getMessage(), // You can include the error message for debugging, but avoid exposing sensitive data
                'discount' => 0
            ], 500);
        }
    }

    /* 
        This method calculates a discount for a family member if they have not already booked the same schedule or if the same schedule was booked for another family member.
    */
    public function applyFamilyMemberDiscount($userId, $scheduleId, $member_id, $total)
    {
        $bookings = Booking::join('booking_items', 'bookings.id', '=', 'booking_items.booking_id')
                            ->where('bookings.user_id', $userId)
                            ->where('bookings.for_member', 1)
                            ->select('bookings.*', 'booking_items.*')
                            ->get();

        if(count($bookings) > 0) {
            // Validate if the current member already have a booking or not
            // If YES then discount not applied
            if($this->checkIfMemberAlreadyHaveBooking($bookings, $member_id)){
                return 0;
            }

            // If NO then check the if we already booked the same schedule in past
            // If YES then apply the discoun dased on discount type
            if($this->checkIfAlreadyBookdTheSameScheduleItem($bookings, $scheduleId)) {
                return $this->calulateDiscount($total);   
            }
        } 
        
        return 0;
    }

    /* 
        This method checks if the current family member already has a booking. If no, no discount is applied.
    */
    public function checkIfMemberAlreadyHaveBooking($bookings, $member_id) 
    {
        foreach($bookings as $booking) {
            if($booking->member_id == $member_id) {
                return true;
            }
        }
        return false;
    }

    /* 
        This method checks if any other family member has booked the same schedule. 
        If yes, the member is eligible for a discount.
    */
    public function checkIfAlreadyBookdTheSameScheduleItem($bookings, $scheduleId) {
        foreach($bookings as $booking) {
            if(in_array($booking->schedule_id, $scheduleId)) {
                // If we find the schedule id 
                // Which means user previously complete there booking for the current schedules
                return true;
            }
        }

        return false;
    }

    /* 
        This method calculates a discount for recurring bookings if the user has previously booked the same schedule.
    */
    public function applyRecurringDiscount($userId, $scheduleId, $total)
    {
        // Get all the booking and booking items based on user_id 
        $bookings = Booking::join('booking_items', 'bookings.id', '=', 'booking_items.booking_id')
                            ->where('bookings.user_id', $userId)
                            ->where('bookings.for_member', 0)
                            ->select('bookings.*', 'booking_items.*')
                            ->get();
     
        // If user booking exist then we check if the user booked for same schedule or not 
        if(count($bookings) > 0) {

            if($this->checkIfAlreadyBookdTheSameScheduleItem($bookings, $scheduleId)) {
                return $this->calulateDiscount($total);   
            }
        }
        return 0;
    }

    /* 
        This method calculates the discount based on the type ('fixed' or 'percentage') and the maximum discount allowed.
    */
    public function calulateDiscount($total)
    {
        // Check if the discount type is fixed then apply fixed discount logic
        // else apply the percentage based discount

        if($this->discountData->discount_type == 'fixed') {
            return $this->discountData->discount_value;
        } else {
            $amount = ($total * $this->discountData->discount_value) / 100;

            // If the discount price less then the max discount price, return discount amount
            // Else return the max discount price
            if($amount <= $this->discountData->max_discount_amount) {
                return round($amount);
            } else {
                return $this->discountData->max_discount_amount;
            }
        }

        return 0;
    }
  





    
    /*
     This method handles the creation of a new discount.
    */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount_type' => 'required',
            'discount_value' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'max_discount_amount' => ['numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'user_left' => ['required', 'numeric'],
            'valid_until' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $discount = Discount::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Discount created successfully',
            'data' => $discount
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $discount)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDiscountRequest $request, Discount $discount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        //
    }
}
