<?php

namespace App\Services;

use App\Models\vendor_products;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\restaurant_orders;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\CommissionService;

class CacheService
{
    // SQL-backed dashboard stats with optional caching

    /**
     * Cache SQL query results with a given key/ttl
     */
    public static function rememberSqlQuery(string $key, \Closure $callback, int $ttlSeconds = 300)
    {
        return Cache::remember($key, $ttlSeconds, $callback);
    }
    /**
     * Get dashboard statistics (cached for 1 hour)
     */
    public static function getDashboardStats($forceRefresh = false)
    {
        $cacheKey = 'dashboard_stats_sql_v2';
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 300, function () {
        return [
            'orders' => restaurant_orders::count(),
            'products' => vendor_products::count(),
            'users' => User::where('role', '=', 'customer')->count(),
            'drivers' => User::where('role', '=', 'driver')->count(),
            'vendors' => Vendor::count(),
            'earnings' => self::getTotalEarnings(), // Sum of all completed orders
            'admin_commission' => self::getAdminCommission(),
            'orders_by_status' => [
                'placed'     => restaurant_orders::where('status', 'Order Placed')->count(),
                'confirmed'  => restaurant_orders::where('status', 'Order Accepted')->count(),
                'shipped'    => restaurant_orders::where('status', 'Order Shipped')->count(),
                'completed'  => restaurant_orders::where('status', 'Order Completed')->count(),
                'canceled'   => restaurant_orders::where('status', 'Order Rejected')->count(),
                'failed'     => restaurant_orders::where('status', 'Driver Rejected')->count(),
                'pending'    => restaurant_orders::where('status', 'Driver Pending')->count(),
            ],
            // Graph datasets for current year (12 months)
            'sales_by_month' => self::getMonthlyTotals(['toPayAmount','grandTotal','total','amount','totalAmount']),
            'commissions_by_month' => self::getMonthlyTotals(['adminCommission','commission','admin_commission']),
            'top_restaurants' => self::getTopRestaurants(),
            'top_drivers' => self::getTopDrivers(),
            'recent_orders' => self::getRecentOrders(),
            'recent_payouts' => self::getRecentPayouts(),
            'cached_at' => now()->toDateTimeString(),
        ];
        });
    }

    /**
     * Calculate total earnings (sum of all completed order amounts)
     * Uses smart fallback: ToPay first, then toPayAmount for orders without ToPay
     */
    private static function getTotalEarnings()
    {
        try {
            // ✅ Only include orders with status "Order Completed"
            // Use COALESCE to try ToPay first, fallback to toPayAmount
            $total = restaurant_orders::where('status', 'Order Completed')
                ->sum(DB::raw('COALESCE(
                    NULLIF(CAST(ToPay AS DECIMAL(16,2)), 0),
                    CAST(toPayAmount AS DECIMAL(16,2)),
                    0
                )'));

            return round((float) $total, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating total earnings: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Build 12-length array of monthly sums for a numeric column on restaurant_orders
     */
    private static function getMonthlyTotals(array $amountColumns): array
    {
        try {
            $amountCol = self::firstExistingColumn('restaurant_orders', $amountColumns);
            if (!$amountCol) return array_fill(0, 12, 0);

            $dateCol = self::firstExistingColumn('restaurant_orders', ['updated_at','created_at','createdAt','orderDate','date']);
            $year = (int) Carbon::now()->year;
            $dateExpr = $dateCol === 'updated_at' || $dateCol === 'created_at'
                ? 'COALESCE(updated_at, created_at)'
                : 'STR_TO_DATE(' . $dateCol . ', "%Y-%m-%d %H:%i:%s")';

            $rows = DB::table('restaurant_orders')
                ->selectRaw('MONTH(' . $dateExpr . ') as m, SUM(COALESCE(CAST(' . $amountCol . ' AS DECIMAL(16,2)),0)) as t')
                ->whereRaw('YEAR(' . $dateExpr . ') = ?', [$year])
                ->groupBy('m')
                ->pluck('t', 'm');

            $out = [];
            for ($i = 1; $i <= 12; $i++) {
                $out[] = (float) ($rows[$i] ?? 0);
            }
            return $out;
        } catch (\Exception $e) {
            Log::error('Error calculating monthly totals: ' . $e->getMessage());
            return array_fill(0, 12, 0);
        }
    }

    private static function firstExistingColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col)) return $col;
        }
        return null;
    }

    private static function getTopRestaurants(int $limit = 5): array
    {
        try {
            $nameCol = self::firstExistingColumn('vendors', ['title','name']);
            $photoCol = self::firstExistingColumn('vendors', ['photo','profile','logo','logo_url']);
            $ratingCol = self::firstExistingColumn('vendors', ['rating','reviews','review','reviewCount']);
            $dateCol = self::firstExistingColumn('vendors', ['updated_at','created_at','createdAt']);
            $q = DB::table('vendors')->select(['id']);
            if ($nameCol) $q->addSelect(DB::raw($nameCol.' as name'));
            if ($photoCol) $q->addSelect(DB::raw($photoCol.' as photo'));
            if ($ratingCol) $q->addSelect(DB::raw($ratingCol.' as rating'));
            if ($dateCol) { $q->orderByDesc($dateCol); }
            elseif ($ratingCol) { $q->orderByDesc('rating'); }
            else { $q->orderByDesc('id'); }
            return $q->limit($limit)->get()->map(function($r){
                return [
                    'id' => $r->id,
                    'name' => (string) ($r->name ?? ''),
                    'photo' => $r->photo ?? null,
                    'rating' => (float) ($r->rating ?? 0),
                ];
            })->all();
        } catch (\Exception $e) {
            Log::error('Top restaurants error: '.$e->getMessage());
            return [];
        }
    }

    private static function getTopDrivers(int $limit = 5): array
    {
        try {
            $nameCol = self::firstExistingColumn('drivers', ['name','firstName']);
            $photoCol = self::firstExistingColumn('drivers', ['photo','profile','profilePictureURL']);
            $completedCol = self::firstExistingColumn('drivers', ['orderCompleted','orders_completed','completed']);
            $dateCol = self::firstExistingColumn('drivers', ['updated_at','created_at','createdAt']);
            $q = DB::table('drivers')->select(['id']);
            if ($nameCol) $q->addSelect(DB::raw($nameCol.' as name'));
            if ($photoCol) $q->addSelect(DB::raw($photoCol.' as photo'));
            if ($completedCol) $q->addSelect(DB::raw('COALESCE('.$completedCol.',0) as orderCompleted'));
            if ($dateCol) { $q->orderByDesc($dateCol); }
            elseif ($completedCol) { $q->orderByDesc('orderCompleted'); }
            else { $q->orderByDesc('id'); }
            return $q->limit($limit)->get()->map(function($r){
                return [
                    'id' => $r->id,
                    'name' => (string) ($r->name ?? ''),
                    'photo' => $r->photo ?? null,
                    'orderCompleted' => (int) ($r->orderCompleted ?? 0),
                ];
            })->all();
        } catch (\Exception $e) {
            Log::error('Top drivers error: '.$e->getMessage());
            return [];
        }
    }

    private static function getRecentOrders(int $limit = 10): array
    {
        try {
            $amountCol = self::firstExistingColumn('restaurant_orders', ['toPayAmount','grandTotal','total','amount','totalAmount']);
            $vendorNameCol = self::firstExistingColumn('restaurant_orders', ['restaurant_name','vendor_name','restaurant','vendor','vendorTitle']);
            $qtyCol = self::firstExistingColumn('restaurant_orders', ['productsCount','quantity','items_count']);
            $dateCol = self::firstExistingColumn('restaurant_orders', ['createdAt','updated_at','created_at','orderDate','date']);
            $q = DB::table('restaurant_orders')->select(['id']);
            if ($vendorNameCol) $q->addSelect(DB::raw($vendorNameCol.' as vendor_name'));
            if ($amountCol) $q->addSelect(DB::raw('COALESCE(CAST('.$amountCol.' AS DECIMAL(16,2)),0) as amount'));
            if ($qtyCol) $q->addSelect(DB::raw('COALESCE('.$qtyCol.',0) as qty'));
            if ($dateCol) $q->addSelect(DB::raw($dateCol.' as created_at'));
            // Order by createdAt (prioritized) or other date columns
            if ($dateCol === 'createdAt') {
                // createdAt may be text/varchar, convert to datetime for proper sorting
                $q->orderByDesc(DB::raw("STR_TO_DATE(createdAt, '%Y-%m-%d %H:%i:%s')"));
            } elseif ($dateCol === 'updated_at' || $dateCol === 'created_at') {
                $q->orderByDesc($dateCol);
            } else {
                $q->orderByDesc('id');
            }
            return $q->limit($limit)->get()->map(function($r){
                return [
                    'id' => $r->id,
                    'vendor_name' => (string) ($r->vendor_name ?? ''),
                    'amount' => (float) ($r->amount ?? 0),
                    'qty' => (int) ($r->qty ?? 0),
                    'date' => (string) ($r->created_at ?? ''),
                ];
            })->all();
        } catch (\Exception $e) {
            Log::error('Recent orders error: '.$e->getMessage());
            return [];
        }
    }

    private static function getRecentPayouts(int $limit = 10): array
    {
        try {
            // Try common payout tables
            $table = null;
            foreach (['restaurants_payouts','restaurant_payouts','payouts'] as $t) {
                if (Schema::hasTable($t)) { $table = $t; break; }
            }
            if (!$table) return [];
            $restaurantCol = self::firstExistingColumn($table, ['restaurant','vendor','vendorName','restaurant_name']);
            $amountCol = self::firstExistingColumn($table, ['amount','paid_amount','total']);
            $noteCol = self::firstExistingColumn($table, ['note','description','remarks']);
            $dateCol = self::firstExistingColumn($table, ['created_at','date','createdAt','updated_at']);
            $q = DB::table($table)->select(['id']);
            if ($restaurantCol) $q->addSelect(DB::raw($restaurantCol.' as restaurant'));
            if ($amountCol) $q->addSelect(DB::raw('COALESCE(CAST('.$amountCol.' AS DECIMAL(16,2)),0) as amount'));
            if ($dateCol) $q->addSelect(DB::raw($dateCol.' as date'));
            if ($noteCol) $q->addSelect(DB::raw($noteCol.' as note'));
            if ($dateCol) $q->orderByDesc('date'); else $q->orderByDesc('id');
            return $q->limit($limit)->get()->map(function($r){
                return [
                    'restaurant' => (string) ($r->restaurant ?? ''),
                    'amount' => (float) ($r->amount ?? 0),
                    'date' => (string) ($r->date ?? ''),
                    'note' => (string) ($r->note ?? ''),
                ];
            })->all();
        } catch (\Exception $e) {
            Log::error('Recent payouts error: '.$e->getMessage());
            return [];
        }
    }

    private static function getAdminCommission()
    {
        try {
            // Use the CommissionService for accurate calculation
            return CommissionService::calculateTotalAdminCommission();
        } catch (\Exception $e) {
            Log::error('Error calculating admin commission: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Clear dashboard cache
     */
    public static function clearDashboardCache()
    {
        Cache::forget('dashboard_stats_sql_v2');
    }

    /**
     * Get cache metadata
     */
    public static function getCacheStats()
    {
        return [
            'exists' => Cache::has('dashboard_stats_sql_v2'),
            'cached_at' => Cache::get('dashboard_stats_sql_v2.cached_at') ?? null,
        ];
    }
}
