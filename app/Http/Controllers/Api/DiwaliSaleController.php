<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiwaliSaleController extends Controller
{
    /**
     * Apply the sale rule based on the rule ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleSaleRule(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'rule_id' => 'required|integer|between:1,3', // Rule ID must be an integer between 1 and 3
            'items_price' => 'required|array', // Items price must be an array
            'items_price.*' => 'required|numeric', // Each item price must be numeric
        ]);

        $rule_id = $request->input('rule_id');
        $items_price = $request->input('items_price');

        // Sort prices in descending order
        rsort($items_price);
        $discounted_price = [];
        $payable_amount = [];

        switch ($rule_id) {
            case 1:
                // Rule 1: Buy One Get One Free (Free item ≤ price of paid item)
                for ($i = 0; $i < count($items_price); $i += 2) {
                    $payable_amount[] = $items_price[$i];
                    if (isset($items_price[$i + 1])) {
                        $discounted_price[] = $items_price[$i + 1];
                    }
                }
                break;

            case 2:
                // Rule 2: Buy One Get One Free (Free item < price of paid item)
                $used = array_fill(0, count($items_price), false);
                for ($i = 0; $i < count($items_price); $i++) {
                    if ($used[$i]) {
                        continue;
                    }
                    $payable_amount[] = $items_price[$i];
                    for ($j = $i + 1; $j < count($items_price); $j++) {
                        if ($items_price[$j] < $items_price[$i] && !$used[$j]) {
                            $discounted_price[] = $items_price[$j];
                            $used[$j] = true;
                            break;
                        }
                    }
                }
                break;

            case 3:
                // Rule 3: Buy Two Get Two Free (Free item < price of paid items)
                for ($i = 0; $i < count($items_price); $i++) {
                    // For every set of 4 items, the first 2 are payable and the next 2 are discounted
                    if ($i % 4 < 2) {
                        // Add to the payable list
                        $payable_ammount[] = $items_price[$i];
                    } else {
                        // Add to the discounted list
                        $discounted_price[] = $items_price[$i];
                    }
                }
                break;

            default:
                return response()->json(['error' => 'Invalid rule ID'], 400);
        }

        // Return the response with discounted and payable items
        return response()->json([
            'discounted_items' => $discounted_price,
            'payable_items' => $payable_amount,
        ]);
    }
}
?>