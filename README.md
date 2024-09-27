# API Documentation: Apply Discount

## Overview

The **Apply Discount** API endpoint allows users to apply discount rules based on specific parameters, including user information and the discount type. This endpoint supports both family member discounts and recurring discounts.

## Endpoint

### GET /api/discount/apply-discount

This endpoint applies the discount rules based on the provided query parameters.

## Query Parameters

| Parameter       | Type   | Required | Description                                                  |
|------------------|--------|----------|--------------------------------------------------------------|
| `user_id`        | INT    | Yes      | The unique identifier of the user applying the discount.     |
| `for_member`     | TINYINT    | Yes      | Indicates whether the booking is for a family member (1 for yes, 0 for no).        |
| `schedule_id`    | ARRAY  | Yes      | The array of IDs representing the schedule(s) being booked. |
| `discount_id`    | INT    | Yes      | The ID of the discount to be applied.                        |
| `total`          | DECIMAL | Yes     | The total amount before the discount is applied.            |

# Assumption

- **Family Member Discounts:** Implement a discount rule that applies a discount if any family member has previously purchased the same schedule.
- **Recurring Discounts:** When a user is booking a schedule that they have previously booked, then a recurring discount applies.

## How to Use


1. **Construct the Request:**
   Make a GET request to the endpoint with the required query parameters. The parameters should be included in the URL as query string parameters.

   Example request URL:

   ```
   GET /api/discount/apply-discount?user_id=1&for_member=0&schedule_id=[1, 2]&discount_id=3&total=2500.00
   ```

2. **Response:**
   The API will return a JSON response indicating the success or failure of the discount application. A successful response will include the applied discount amount.

    #### Example successful response:
    ```json
    {
        "status": true,
        "message": "Discount applied successfully",
        "discount": 10.00 // The amount of the discount applied
    }
    ```

    #### Example error response:
    **Missing Required Parameters**
    - **HTTP Status Code**: `400 Bad Request`
    - **Response Body**:
    ```json
    {
        "status": false,
        "message": "Missing required parameters.",
        "discount": 0
    }
    ```

    **Invalid or Expired Discount ID**
    - **HTTP Status Code**: `404 Not Found`
    - **Response Body**:
    ```json
    {
        "status": false,
        "message": "Invalid or expired discount ID.",
        "discount": 0
    }
    ```

    **Internal Server Error**
    - **HTTP Status Code**: `500 Internal Server Error`
    - **Response Body**:
    ```json
    {
        "status": false,
        "message": "An error occurred while applying the discount.",
        "error": "Error message details",
        "discount": 0
    }
    ```



# Discount Module Documentation

In this document, I am demonstrating how the discount module applies in different scenarios.

I have created a temporary booking page to demonstrate the discount rules.

## Code Implementation

### Apply Discount

This method handles applying a discount for either a family member or a recurring booking. It first retrieves the necessary request data (`user_id`, `for_member`, `schedule_id`, `discount_id`, and `total`). Depending on whether the request is for a family member discount or a recurring discount, it calls the appropriate discount calculation method.

```php
public function apply(Request $request) 
{
    try {
        $userId = $request->user_id;
        $for_family_member = $request->for_family_member;
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
        if($for_family_member) { 
            // If discount for family
            $discount = $this->applyFamilyMemberDiscount($userId, $scheduleId, $total);
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
```

## Family Discount

Implement a discount rule that applies a discount if any family member has previously purchased the same schedule. The discount can be a fixed amount or a percentage of the booking cost.

This method calculates a discount for a family member if they have not already booked the same schedule or if the same schedule was booked for another family member.

```php
public function applyFamilyMemberDiscount($userId, $scheduleId, $total)
{
    $bookings = Booking::join('booking_items', 'bookings.id', '=', 'booking_items.booking_id')
                        ->where('bookings.user_id', $userId)
                        ->where('bookings.for_member', 1)
                        ->select('bookings.*', 'booking_items.*')
                        ->get();

    if(count($bookings) > 0) {
        if($this->checkIfAlreadyBookdTheSameScheduleItem($bookings, $scheduleId)) {
            return $this->calulateDiscount($total);   
        }
    } 
    
    return 0;
}
```

### Check if the Same Schedule is Already Booked

This method checks if any other family member has booked the same schedule. If yes, the member is eligible for a discount.

```php
public function checkIfAlreadyBookedTheSameScheduleItem($bookings, $scheduleId)
{
    foreach($bookings as $booking) {
        // check if the current schedules id are available in the booking or not
        if(in_array($booking->schedule_id, $scheduleId)) {
            return true;
        }
    }

    return false;
}
```

## Recurring Discount

Implement a discount rule for recurring bookings. When an user books the same schedule or subscription again, the discount is applied automatically.

This method calculates a discount for recurring bookings if the user has previously booked the same schedule.

```php
public function applyRecurringDiscount($userId, $scheduleId, $total)
{
    $bookings = Booking::join('booking_items', 'bookings.id', '=', 'booking_items.booking_id')
                        ->where('bookings.user_id', $userId)
                        ->where('bookings.for_member', 0)
                        ->select('bookings.*', 'booking_items.*')
                        ->get();

    if(count($bookings) > 0) {
        if($this->checkIfAlreadyBookedTheSameScheduleItem($bookings, $scheduleId)) {
            return $this->calculateDiscount($total);  
        }
    }

    return 0;
}
```

### Calculate Discount

This method calculates the discount based on the type (`fixed` or `percentage`) and the maximum discount allowed.

```php
public function calculateDiscount($total)
{
    // Check if the discount type is fixed then apply fixed discount logic
    // else apply the percentage based discount

    if($this->discountData->discount_type == 'fixed') {
        return $this->discountData->discount_value;
    } else {
        $amount = ($total * $this->discountData->discount_value) / 100;

        // If the discount price less then the max discount price, return calculated discount amount
        // Else return the max discount price

        if($amount <= $this->discountData->max_discount_amount) {
            return round($amount);
        } else {
            return $this->discountData->max_discount_amount;
        }
    }

    return 0;
}
```