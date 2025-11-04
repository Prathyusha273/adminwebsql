<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\Vendor;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id = '')
    {
        return view("payoutRequests.drivers.index")->with('id',$id);
    }

    public function restaurant($id = '')
    {
        return view("payoutRequests.restaurants.index")->with('id',$id);
    }

    /**
     * Get restaurant payout requests data (Pending status)
     */
    public function getRestaurantPayoutRequestsData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = strtolower($request->input('search.value', ''));
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'desc');
            $vendorId = $request->input('vendor_id', '');

            // Build base query for PENDING payouts only
            $query = Payout::select('payouts.*')
                ->where('paymentStatus', 'Pending');

            // Filter by vendor if provided
            if (!empty($vendorId)) {
                $query->where('vendorID', $vendorId);
            }

            $orderableColumns = ['', 'vendorID', 'amount', 'paidDate', 'paymentStatus', 'withdrawMethod'];
            $orderByField = $orderableColumns[$orderColumnIndex] ?? 'paidDate';

            // Get all pending payouts
            $payouts = $query->orderBy('paidDate', 'desc')->get();

            $records = [];
            $filteredRecords = [];

            foreach ($payouts as $payout) {
                // Get restaurant name
                $vendor = Vendor::where('id', $payout->vendorID)->first();
                $payout->restaurantName = $vendor ? $vendor->title : 'Unknown';

                // Format date
                if ($payout->paidDate) {
                    try {
                        $dateStr = trim($payout->paidDate, '"');
                        $dateObj = new \DateTime($dateStr);
                        $payout->formattedDate = $dateObj->format('D M d Y g:i:s A');
                    } catch (\Exception $e) {
                        $payout->formattedDate = $payout->paidDate;
                    }
                }

                // Apply search filter
                if ($searchValue) {
                    if (
                        (isset($payout->restaurantName) && stripos($payout->restaurantName, $searchValue) !== false) ||
                        (isset($payout->amount) && stripos((string)$payout->amount, $searchValue) !== false) ||
                        (isset($payout->formattedDate) && stripos($payout->formattedDate, $searchValue) !== false) ||
                        (isset($payout->note) && stripos($payout->note, $searchValue) !== false) ||
                        (isset($payout->withdrawMethod) && stripos($payout->withdrawMethod, $searchValue) !== false)
                    ) {
                        $filteredRecords[] = $payout;
                    }
                } else {
                    $filteredRecords[] = $payout;
                }
            }

            // Sort filtered records
            usort($filteredRecords, function($a, $b) use ($orderByField, $orderDirection) {
                $aValue = $a->$orderByField ?? '';
                $bValue = $b->$orderByField ?? '';

                if ($orderByField === 'amount') {
                    $aValue = is_numeric($aValue) ? floatval($aValue) : 0;
                    $bValue = is_numeric($bValue) ? floatval($bValue) : 0;
                } elseif ($orderByField === 'paidDate') {
                    try {
                        $aValue = $a->paidDate ? strtotime(trim($a->paidDate, '"')) : 0;
                        $bValue = $b->paidDate ? strtotime(trim($b->paidDate, '"')) : 0;
                    } catch (\Exception $e) {
                        $aValue = 0;
                        $bValue = 0;
                    }
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

            // Get paginated records
            $paginatedRecords = array_slice($filteredRecords, $start, $length);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $paginatedRecords
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw', 0)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error fetching payout requests: ' . $e->getMessage()
            ], 500);
        }
    }

}
