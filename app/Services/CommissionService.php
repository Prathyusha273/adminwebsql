<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Calculate admin commission for dashboard display
     * Uses actual commission values from orders if available,
     * otherwise calculates based on settings
     */
    public static function calculateTotalAdminCommission()
    {
        try {
            // Only count completed orders (not shipped, only Order Completed)
            $orders = DB::table('restaurant_orders')
                ->where('status', 'Order Completed')
                ->get();

            $total = 0;
            
            foreach ($orders as $order) {
                $commission = self::getOrderCommission($order);
                $total += $commission;
            }

            return round($total, 2);
            
        } catch (\Exception $e) {
            Log::error('Error calculating admin commission: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get commission for a single order
     * SMART DETECTION: If stored value looks like a rate (12, 15, 20, 25), treat it as percentage
     * Otherwise use stored value or calculate from current settings
     */
    public static function getOrderCommission($order)
    {
        $orderAmount = self::getOrderAmount($order);
        $storedCommission = !empty($order->adminCommission) && $order->adminCommission !== 'null' && $order->adminCommission !== '0' 
            ? (float) $order->adminCommission 
            : 0;
        
        // If no order amount, use stored commission if available
        if ($orderAmount <= 0) {
            return $storedCommission;
        }
        
        // Check if stored value looks like it's actually a rate (common rates: 12, 15, 17, 18, 20, 22, 25)
        // If stored commission is a whole number between 10-30, it's likely the rate percentage
        if ($storedCommission > 0 && $storedCommission >= 10 && $storedCommission <= 30 && $storedCommission == floor($storedCommission)) {
            // Stored value is likely the commission RATE, not the calculated amount
            // Calculate: amount * (rate / 100)
            return ($orderAmount * $storedCommission) / 100;
        }
        
        // If stored commission exists and is a reasonable percentage of order (1% to 50%)
        if ($storedCommission > 0) {
            $percentOfOrder = ($storedCommission / $orderAmount) * 100;
            
            // If commission is between 1% and 50% of order value, it's likely a valid calculated amount
            if ($percentOfOrder >= 1 && $percentOfOrder <= 50) {
                return $storedCommission;
            }
        }
        
        // Otherwise calculate based on current settings
        return self::calculateOrderCommission($order);
    }

    /**
     * Calculate commission for an order based on settings
     */
    public static function calculateOrderCommission($order)
    {
        try {
            // Get order amount
            $amount = self::getOrderAmount($order);
            if ($amount <= 0) {
                return 0;
            }

            // Get commission settings (vendor-specific or global)
            $settings = self::getCommissionSettings($order->vendorID ?? null);
            
            if (!$settings || !($settings['isEnabled'] ?? false)) {
                return 0;
            }

            $commissionType = $settings['commissionType'] ?? 'Percent';
            $fixCommission = (float) ($settings['fix_commission'] ?? 0);

            if ($commissionType === 'Fixed') {
                return $fixCommission;
            } else { // Percent
                return ($amount * $fixCommission) / 100;
            }

        } catch (\Exception $e) {
            Log::error('Error calculating commission for order: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get order amount (try multiple column names and JSON fields)
     * Priority: ToPay first (has most data), then toPayAmount
     */
    private static function getOrderAmount($order)
    {
        // First try direct amount fields (ToPay has priority - most data)
        $amountFields = ['ToPay', 'toPayAmount', 'grandTotal', 'total', 'amount', 'totalAmount'];
        
        foreach ($amountFields as $field) {
            if (property_exists($order, $field)) {
                $value = $order->$field;
                
                // Skip null, empty string, or 'null' string
                if ($value === null || $value === '' || $value === 'null') {
                    continue;
                }
                
                // Handle numeric values (including string numbers)
                if (is_numeric($value)) {
                    return (float) $value;
                }
                
                // Handle JSON-encoded values
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (is_numeric($decoded)) {
                        return (float) $decoded;
                    }
                }
            }
        }
        
        // Try calculatedCharges JSON field
        if (property_exists($order, 'calculatedCharges') && !empty($order->calculatedCharges)) {
            $charges = json_decode($order->calculatedCharges, true);
            if (is_array($charges)) {
                // Try common total fields in JSON
                $totalFields = ['total', 'grandTotal', 'toPayAmount', 'amount', 'totalAmount', 'ToPay'];
                foreach ($totalFields as $field) {
                    if (isset($charges[$field]) && is_numeric($charges[$field])) {
                        return (float) $charges[$field];
                    }
                }
            }
        }
        
        // Try products field (might have total in it)
        if (property_exists($order, 'products') && !empty($order->products)) {
            $products = json_decode($order->products, true);
            if (is_array($products)) {
                // Calculate total from products if available
                $total = 0;
                if (isset($products['total']) && is_numeric($products['total'])) {
                    return (float) $products['total'];
                }
            }
        }
        
        return 0;
    }

    /**
     * Get commission settings for a vendor (or global)
     * Priority: Vendor commission (if set and enabled) > Global AdminCommission settings
     */
    private static function getCommissionSettings($vendorId = null)
    {
        // FIRST PRIORITY: Try to get vendor-specific commission settings
        if ($vendorId && $vendorId !== 'null' && $vendorId !== '') {
            $vendor = DB::table('vendors')->where('id', $vendorId)->first();
            if ($vendor && !empty($vendor->adminCommission) && $vendor->adminCommission !== 'null') {
                $vendorSettings = json_decode($vendor->adminCommission, true);
                if (is_array($vendorSettings) && isset($vendorSettings['isEnabled'])) {
                    // If vendor has enabled commission settings, use them (FIRST PRIORITY)
                    if ($vendorSettings['isEnabled'] === true || $vendorSettings['isEnabled'] === 1 || $vendorSettings['isEnabled'] === '1') {
                        return $vendorSettings;
                    }
                    // If vendor explicitly has isEnabled=false, use their disabled state
                    // and don't fall back to global (respect vendor's choice)
                    return $vendorSettings;
                }
            }
        }

        // SECOND PRIORITY: Fall back to global AdminCommission settings
        $globalRec = DB::table('settings')->where('document_name', 'AdminCommission')->first();
        if ($globalRec && $globalRec->fields) {
            return json_decode($globalRec->fields, true);
        }

        // No settings found
        return null;
    }

    /**
     * Calculate and update commission for a specific order
     * Use this when creating/updating orders
     */
    public static function updateOrderCommission($orderId)
    {
        try {
            $order = DB::table('restaurant_orders')->where('id', $orderId)->first();
            if (!$order) {
                return false;
            }

            $commission = self::calculateOrderCommission($order);
            $settings = self::getCommissionSettings($order->vendorID ?? null);
            $commissionType = $settings['commissionType'] ?? 'Percent';

            DB::table('restaurant_orders')->where('id', $orderId)->update([
                'adminCommission' => $commission,
                'adminCommissionType' => $commissionType,
            ]);

            return $commission;

        } catch (\Exception $e) {
            Log::error('Error updating order commission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk update commissions for all orders (useful for migration)
     */
    public static function recalculateAllCommissions($limit = null)
    {
        try {
            $query = DB::table('restaurant_orders')
                ->where('status', 'Order Completed');
            
            if ($limit) {
                $query->limit($limit);
            }

            $orders = $query->get();
            $updated = 0;

            foreach ($orders as $order) {
                $commission = self::calculateOrderCommission($order);
                $settings = self::getCommissionSettings($order->vendorID ?? null);
                $commissionType = $settings['commissionType'] ?? 'Percent';

                DB::table('restaurant_orders')->where('id', $order->id)->update([
                    'adminCommission' => $commission,
                    'adminCommissionType' => $commissionType,
                ]);

                $updated++;
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('Error recalculating commissions: ' . $e->getMessage());
            return 0;
        }
    }
}


