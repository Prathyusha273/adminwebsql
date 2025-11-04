<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index($id='')
    {
        return view("transactions.index")->with('id',$id);
    }

    /**
     * Get wallet transactions data for DataTables
     */
    public function getTransactionsData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = strtolower($request->input('search.value', ''));
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'desc');
            $userId = $request->input('user_id', '');

            // Build base query
            $query = DB::table('wallet')->select('wallet.*');

            // Filter by user if provided
            if (!empty($userId)) {
                $query->where('user_id', $userId);
                $orderableColumns = ['', 'amount', 'date', 'note'];
            } else {
                $orderableColumns = ['', 'userName', 'amount', 'date', 'note'];
            }

            $orderByField = $orderableColumns[$orderColumnIndex] ?? 'date';

            // Get all transactions
            $transactions = $query->orderBy('date', 'desc')->get();

            $records = [];
            $filteredRecords = [];

            foreach ($transactions as $transaction) {
                // Get user name
                $user = AppUser::where('id', $transaction->user_id)->first();
                $transaction->userName = $user ? ($user->firstName . ' ' . $user->lastName) : 'Unknown';
                $transaction->userType = $transaction->transactionUser ?? 'user';

                // Format date
                if ($transaction->date) {
                    try {
                        $dateStr = trim($transaction->date, '"');
                        $dateObj = new \DateTime($dateStr);
                        $transaction->formattedDate = $dateObj->format('D M d Y g:i:s A');
                    } catch (\Exception $e) {
                        $transaction->formattedDate = $transaction->date;
                    }
                }

                // Apply search filter
                if ($searchValue) {
                    if (
                        (isset($transaction->userName) && stripos($transaction->userName, $searchValue) !== false) ||
                        (isset($transaction->amount) && stripos((string)$transaction->amount, $searchValue) !== false) ||
                        (isset($transaction->formattedDate) && stripos($transaction->formattedDate, $searchValue) !== false) ||
                        (isset($transaction->note) && stripos($transaction->note, $searchValue) !== false)
                    ) {
                        $filteredRecords[] = $transaction;
                    }
                } else {
                    $filteredRecords[] = $transaction;
                }
            }

            // Sort filtered records
            usort($filteredRecords, function($a, $b) use ($orderByField, $orderDirection) {
                $aValue = $a->$orderByField ?? '';
                $bValue = $b->$orderByField ?? '';

                if ($orderByField === 'amount') {
                    $aValue = is_numeric($aValue) ? floatval($aValue) : 0;
                    $bValue = is_numeric($bValue) ? floatval($bValue) : 0;
                } elseif ($orderByField === 'date') {
                    try {
                        $aValue = $a->date ? strtotime(trim($a->date, '"')) : 0;
                        $bValue = $b->date ? strtotime(trim($b->date, '"')) : 0;
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
                'error' => 'Error fetching wallet transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user details by ID
     */
    public function getUserDetails($userId)
    {
        try {
            $user = AppUser::where('id', $userId)->first();

            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'fullName' => $user->firstName . ' ' . $user->lastName,
                        'role' => $user->role
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user details: ' . $e->getMessage()
            ], 500);
        }
    }
}
