<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\Vendor;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantsPayoutController extends Controller
{

   public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id='')
    {
       return view("restaurants_payouts.index")->with('id',$id);
    }

    public function create($id='')
    {
       return view("restaurants_payouts.create")->with('id',$id);
    }

    /**
     * Get restaurant payouts data for DataTables
     */
    public function getPayoutsData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = strtolower($request->input('search.value', ''));
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'desc');
            $vendorId = $request->input('vendor_id', '');

            // Build base query
            $query = Payout::select('payouts.*')
                ->where('paymentStatus', 'Success');

            // Filter by vendor if provided
            if (!empty($vendorId)) {
                $query->where('vendorID', $vendorId);
                $orderableColumns = ['', 'amount', 'paidDate', 'note', 'adminNote'];
            } else {
                $orderableColumns = ['', 'vendorID', 'amount', 'paidDate', 'note', 'adminNote'];
            }

            $orderByField = $orderableColumns[$orderColumnIndex] ?? 'paidDate';

            // Get all payouts
            $payouts = $query->orderBy('paidDate', 'desc')->get();

            $records = [];
            $filteredRecords = [];

            foreach ($payouts as $payout) {
                // Get restaurant name
                $vendor = Vendor::where('id', $payout->vendorID)->first();
                $payout->restaurantName = $vendor ? $vendor->title : 'Unknown';

                // Format date for search
                $date = '';
                $time = '';
                if ($payout->paidDate) {
                    try {
                        // Handle JSON date format like "2024-05-21T13:45:36.612000Z"
                        $dateStr = trim($payout->paidDate, '"');
                        $dateObj = new \DateTime($dateStr);
                        $date = $dateObj->format('D M d Y');
                        $time = $dateObj->format('g:i:s A');
                    } catch (\Exception $e) {
                        $date = $payout->paidDate;
                    }
                }
                $payout->formattedDate = $date . ' ' . $time;

                // Apply search filter
                if ($searchValue) {
                    if (
                        (isset($payout->restaurantName) && stripos($payout->restaurantName, $searchValue) !== false) ||
                        (isset($payout->amount) && stripos((string)$payout->amount, $searchValue) !== false) ||
                        (isset($payout->formattedDate) && stripos($payout->formattedDate, $searchValue) !== false) ||
                        (isset($payout->note) && stripos($payout->note, $searchValue) !== false) ||
                        (isset($payout->adminNote) && stripos($payout->adminNote, $searchValue) !== false)
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
                'error' => 'Error fetching payouts data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor details by ID
     */
    public function getVendorDetails($vendorId)
    {
        try {
            $vendor = Vendor::where('id', $vendorId)->first();

            if ($vendor) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $vendor->id,
                        'title' => $vendor->title,
                        'author' => $vendor->author,
                        'dine_in_active' => $vendor->dine_in_active ?? false
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching vendor details: ' . $e->getMessage()
            ], 500);
        }
    }

}
