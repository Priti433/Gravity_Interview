<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiwaliSaleController extends Controller
{
    /**
     * Rule 1: Buy One Get One Free (Free item ≤ price of paid item)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saleRule1(Request $request)
    {
        // Get the prices from the request
        $items_price = $request->input('items_price');

        // Sort prices in descending order
        rsort($items_price);

        $discounted_price = [];
        $payable_ammount = [];
        
        // Iterate over the prices in pairs
        for ($i = 0; $i < count($items_price); $i += 2) {
            // Add the first item of the pair to the payable list
            $payable_ammount[] = $items_price[$i];

            // Add the second item of the pair to the discounted list if it exists
            if (isset($items_price[$i + 1])) {
                $discounted_price[] = $items_price[$i + 1];
            }
        }

        // Return the response with discounted and payable items
        return response()->json([
            'discounted_items' => $discounted,
            'payable_items' => $payable_ammount,
        ]);
    }

    /**
     * Rule 2: Buy One Get One Free (Free item < price of paid item) - Alternative Logic
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saleRule2(Request $request)
    {
        // Get the prices from the request
        $items_price = $request->input('items_price');

        // Sort prices in descending order
        rsort($items_price);

        $discounted_price = [];
        $payable_ammount = [];

        // Use an array to keep track of whether an item has been used
        $used = array_fill(0, count($items_price), false);

        // Iterate over the prices
        for ($i = 0; $i < count($items_price); $i++) {
            // If the current item has already been used as a discounted item, skip it
            if ($used[$i]) {
                continue;
            }

            // Add the current item to the payable list
            $payable_ammount[] = $items_price[$i];

            // Find the next item which is less than the current item and has not been used
            for ($j = $i + 1; $j < count($items_price); $j++) {
                if ($items_price[$j] < $items_price[$i] && !$used[$j]) {
                    // Add the found item to the discounted list
                    $discounted_price[] = $items_price[$j];
                    // Mark the item as used
                    $used[$j] = true;
                    break; // Move to the next payable item
                }
            } 
        }
        // Return the response with discounted and payable items
        return response()->json([
            'discounted_items' => $discounted_price,
            'payable_items' => $payable_ammount,
        ]);   
    }

    
    /**
     * Rule 3: Buy Two Get Two Free (Free item < price of paid items)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saleRule3(Request $request)
    {
        // Get the prices from the request
        $prices = $request->input('prices');

        // Sort prices in descending order
        rsort($prices);

        $discounted = [];
        $payable = [];
        
        // Iterate over the prices
        for ($i = 0; $i < count($prices); $i++) {
            // For every set of 4 items, the first 2 are payable and the next 2 are discounted
            if ($i % 4 < 2) {
                // Add to the payable list
                $payable[] = $prices[$i];
            } else {
                // Add to the discounted list
                $discounted[] = $prices[$i];
            }
        }

        // Return the response with discounted and payable items
        return response()->json([
            'discounted_items' => $discounted,
            'payable_items' => $payable,
        ]);
    }
}

?>