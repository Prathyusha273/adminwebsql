<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Payout;
use App\Models\DriverPayout;
use App\Models\Currency;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPaymentsController extends Controller
{

   public function __construct()
    {
        $this->middleware('auth');
    }

	public function index()
    {
       return view("payments.index");
    }

    public function driverIndex()
 	{
    	return view("payments.driver_index");
 	}

    /**
     * Get active currency settings
     */
    public function getCurrency()
    {
        try {
            $currency = Currency::where('isActive', true)->first();

            if ($currency) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'symbol' => $currency->symbol,
                        'code' => $currency->code,
                        'symbolAtRight' => $currency->symbolAtRight,
                        'name' => $currency->name,
                        'decimal_degits' => $currency->decimal_degits
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No active currency found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching currency: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments data for DataTables
     */
    public function getPaymentsData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = strtolower($request->input('search.value', ''));
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'asc');

            $orderableColumns = ['title', 'totalAmount', 'paidAmount', 'remainingAmount'];
            $orderByField = $orderableColumns[$orderColumnIndex] ?? 'title';

            // Get vendors with their payment information
            $query = Vendor::select('vendors.*')
                ->orderBy('title', 'asc');

            $vendors = $query->get();

            $records = [];
            $filteredRecords = [];

            foreach ($vendors as $vendor) {
                $paymentData = $this->calculateVendorPayment($vendor->id);

                if ($paymentData['total'] != 0) {
                    $vendor->totalAmount = $paymentData['total'];
                    $vendor->paidAmount = $paymentData['paid_price_val'];
                    $vendor->remainingAmount = $paymentData['remaining_val'];

                    // Apply search filter
                    if ($searchValue) {
                        if (
                            (isset($vendor->title) && stripos($vendor->title, $searchValue) !== false) ||
                            stripos((string)$paymentData['total'], $searchValue) !== false ||
                            stripos((string)$paymentData['paid_price_val'], $searchValue) !== false ||
                            stripos((string)$paymentData['remaining_val'], $searchValue) !== false
                        ) {
                            $filteredRecords[] = $vendor;
                        }
                    } else {
                        $filteredRecords[] = $vendor;
                    }
                }
            }

            // Sort filtered records
            usort($filteredRecords, function($a, $b) use ($orderByField, $orderDirection) {
                $aValue = $a->$orderByField ?? '';
                $bValue = $b->$orderByField ?? '';

                if (in_array($orderByField, ['totalAmount', 'paidAmount', 'remainingAmount'])) {
                    $aValue = is_numeric($aValue) ? floatval($aValue) : 0;
                    $bValue = is_numeric($bValue) ? floatval($bValue) : 0;
                } else {
                    $aValue = strtolower($aValue);
                    $bValue = strtolower($bValue);
                }

                if ($orderDirection === 'asc') {
                    return ($aValue > $bValue) ? 1 : -1;
                } else {
                    return ($aValue < $bValue) ? 1 : -1;
                }
            });

            $totalRecords = count($filteredRecords);

            // Calculate summary statistics
            $total_payments = 0;
            $total_paid_amounts = 0;
            $total_remaining_amounts = 0;

            foreach ($filteredRecords as $record) {
                if ($record && abs($record->totalAmount) != 0) {
                    $total_payments += floatval($record->totalAmount);
                    $total_paid_amounts += floatval($record->paidAmount);
                    $total_remaining_amounts += floatval($record->remainingAmount);
                }
            }

            // Get paginated records
            $paginatedRecords = array_slice($filteredRecords, $start, $length);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $paginatedRecords,
                'summary' => [
                    'rest_count' => $totalRecords,
                    'total_payments' => $total_payments,
                    'total_paid_amounts' => $total_paid_amounts,
                    'total_remaining_amounts' => $total_remaining_amounts
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw', 0)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error fetching payments data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate vendor payment details
     */
    private function calculateVendorPayment($vendorID)
    {
        $paid_price = 0;
        $total_price = 0;
        $remaining = 0;

        // Get successful payouts for this vendor
        $payouts = Payout::where('vendorID', $vendorID)
            ->where('paymentStatus', 'Success')
            ->get();

        foreach ($payouts as $payout) {
            $paid_price += floatval($payout->amount);
        }

        // Get vendor user wallet amount
        $vendor_user = AppUser::where('vendorID', $vendorID)->first();
        $wallet_amount = 0;

        if ($vendor_user && isset($vendor_user->wallet_amount)) {
            $wallet_amount = floatval($vendor_user->wallet_amount);
        }

        $remaining = $wallet_amount;
        $total_price = $wallet_amount + $paid_price;

        if (is_nan($paid_price)) {
            $paid_price = 0;
        }
        if (is_nan($total_price)) {
            $total_price = 0;
        }
        if (is_nan($remaining)) {
            $remaining = 0;
        }

        return [
            'total' => $total_price,
            'paid_price_val' => $paid_price,
            'remaining_val' => $remaining
        ];
    }

    /**
     * Get payment summary statistics for dashboard
     */
    public function getPaymentsSummary()
    {
        try {
            $vendors = Vendor::orderBy('title', 'asc')->get();

            $total_payments = 0;
            $total_paid_amounts = 0;
            $total_remaining_amounts = 0;
            $vendor_count = 0;

            foreach ($vendors as $vendor) {
                $paymentData = $this->calculateVendorPayment($vendor->id);

                if ($paymentData['total'] != 0) {
                    $total_payments += floatval($paymentData['total']);
                    $total_paid_amounts += floatval($paymentData['paid_price_val']);
                    $total_remaining_amounts += floatval($paymentData['remaining_val']);
                    $vendor_count++;
                }
            }

            // Get active currency
            $currency = Currency::where('isActive', true)->first();
            $currencySymbol = $currency ? $currency->symbol : '₹';
            $currencyAtRight = $currency ? $currency->symbolAtRight : false;
            $decimal_degits = $currency ? $currency->decimal_degits : 2;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_earnings' => $total_payments,
                    'total_paid' => $total_paid_amounts,
                    'total_remaining' => $total_remaining_amounts,
                    'vendor_count' => $vendor_count,
                    'currency' => [
                        'symbol' => $currencySymbol,
                        'symbolAtRight' => $currencyAtRight,
                        'decimal_degits' => $decimal_degits
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching payment summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver payments data for DataTables
     */
    public function getDriverPaymentsData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = strtolower($request->input('search.value', ''));
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'asc');

            $orderableColumns = ['fullName', 'totalAmount', 'paidAmount', 'remainingAmount'];
            $orderByField = $orderableColumns[$orderColumnIndex] ?? 'fullName';

            // Get drivers (users with role = 'driver')
            $query = AppUser::where('role', 'driver')
                ->orderBy('firstName', 'asc');

            $drivers = $query->get();

            $records = [];
            $filteredRecords = [];

            foreach ($drivers as $driver) {
                $paymentData = $this->calculateDriverPayment($driver->id);

                if ($paymentData['total'] != 0) {
                    $driver->totalAmount = $paymentData['total'];
                    $driver->paidAmount = $paymentData['paid_price_val'];
                    $driver->remainingAmount = $paymentData['remaining_val'];
                    $driver->fullName = ($driver->firstName ?? '') . ' ' . ($driver->lastName ?? '');

                    // Apply search filter
                    if ($searchValue) {
                        if (
                            (isset($driver->fullName) && stripos($driver->fullName, $searchValue) !== false) ||
                            stripos((string)$paymentData['total'], $searchValue) !== false ||
                            stripos((string)$paymentData['paid_price_val'], $searchValue) !== false ||
                            stripos((string)$paymentData['remaining_val'], $searchValue) !== false
                        ) {
                            $filteredRecords[] = $driver;
                        }
                    } else {
                        $filteredRecords[] = $driver;
                    }
                }
            }

            // Sort filtered records
            usort($filteredRecords, function($a, $b) use ($orderByField, $orderDirection) {
                $aValue = $a->$orderByField ?? '';
                $bValue = $b->$orderByField ?? '';

                if (in_array($orderByField, ['totalAmount', 'paidAmount', 'remainingAmount'])) {
                    $aValue = is_numeric($aValue) ? floatval($aValue) : 0;
                    $bValue = is_numeric($bValue) ? floatval($bValue) : 0;
                } else {
                    $aValue = strtolower($aValue);
                    $bValue = strtolower($bValue);
                }

                if ($orderDirection === 'asc') {
                    return ($aValue > $bValue) ? 1 : -1;
                } else {
                    return ($aValue < $bValue) ? 1 : -1;
                }
            });

            $totalRecords = count($filteredRecords);

            // Calculate summary statistics
            $total_payments = 0;
            $total_paid_amounts = 0;
            $total_remaining_amounts = 0;

            foreach ($filteredRecords as $record) {
                if ($record && abs($record->totalAmount) != 0) {
                    $total_payments += floatval($record->totalAmount);
                    $total_paid_amounts += floatval($record->paidAmount);
                    $total_remaining_amounts += floatval($record->remainingAmount);
                }
            }

            // Get paginated records
            $paginatedRecords = array_slice($filteredRecords, $start, $length);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $paginatedRecords,
                'summary' => [
                    'rest_count' => $totalRecords,
                    'total_payments' => $total_payments,
                    'total_paid_amounts' => $total_paid_amounts,
                    'total_remaining_amounts' => $total_remaining_amounts
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw', 0)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error fetching driver payments data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate driver payment details
     */
    private function calculateDriverPayment($driverID)
    {
        $paid_price = 0;
        $total_price = 0;
        $remaining = 0;

        // Get all payouts for this driver
        $payouts = DriverPayout::where('driverID', $driverID)->get();

        foreach ($payouts as $payout) {
            $paid_price += floatval($payout->amount);
        }

        // Get driver wallet amount
        $driver = AppUser::where('id', $driverID)->first();
        $wallet_amount = 0;

        if ($driver && isset($driver->wallet_amount)) {
            $wallet_amount = floatval($driver->wallet_amount);
        }

        $remaining = $wallet_amount;
        $total_price = $wallet_amount + $paid_price;

        if (is_nan($paid_price)) {
            $paid_price = 0;
        }
        if (is_nan($total_price)) {
            $total_price = 0;
        }
        if (is_nan($remaining)) {
            $remaining = 0;
        }

        return [
            'total' => $total_price,
            'paid_price_val' => $paid_price,
            'remaining_val' => $remaining
        ];
    }

}
