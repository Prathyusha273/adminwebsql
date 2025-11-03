<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    /**
     * Display the activity logs page
     */
    public function index()
    {
        return view('activity_logs.index');
    }

    /**
     * Get activity logs data for DataTables (Server-side processing)
     */
    public function getActivityLogsData(Request $request)
    {
        try {
            // Get DataTables parameters
            $draw = $request->get('draw', 1);
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $searchValue = $request->get('search')['value'] ?? '';
            $orderColumnIndex = $request->get('order')[0]['column'] ?? 7;
            $orderDirection = $request->get('order')[0]['dir'] ?? 'desc';

            // Column mapping
            $columns = ['user_id', 'user_name', 'user_type', 'role', 'module', 'action', 'description', 'created_at'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

            // Build query
            $query = ActivityLog::query();

            // Apply module filter if provided
            if ($request->has('module') && !empty($request->module)) {
                $query->where('module', $request->module);
            }

            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('user_id', 'like', '%' . $searchValue . '%')
                      ->orWhere('user_name', 'like', '%' . $searchValue . '%')
                      ->orWhere('user_type', 'like', '%' . $searchValue . '%')
                      ->orWhere('role', 'like', '%' . $searchValue . '%')
                      ->orWhere('module', 'like', '%' . $searchValue . '%')
                      ->orWhere('action', 'like', '%' . $searchValue . '%')
                      ->orWhere('description', 'like', '%' . $searchValue . '%')
                      ->orWhere('ip_address', 'like', '%' . $searchValue . '%');
                });
            }

            // Get total records (before filtering)
            $totalRecords = ActivityLog::count();

            // Get filtered records count
            $filteredRecords = $query->count();

            // Apply sorting and pagination
            $logs = $query->orderBy($orderColumn, $orderDirection)
                         ->skip($start)
                         ->take($length)
                         ->get();

            // Format data for DataTables
            $data = [];
            foreach ($logs as $log) {
                $data[] = [
                    'user_id' => '<div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle">
                            <div class="font-weight-bold">' . ($log->user_id ?? 'N/A') . '</div>
                        </div>
                    </div>',

                    'user_name' => '<div class="d-flex align-items-center">
                        <span class="avatar-sm mr-3">
                            <i class="mdi mdi-account"></i>
                            <span class="font-weight-bold">' . ($log->user_name ?? 'Unknown User') . '</span>
                        </span>
                    </div>',

                    'user_type' => '<span class="badge badge-' . $this->getUserTypeBadge($log->user_type) . '">'
                        . ($log->user_type ?? 'N/A') . '</span>',

                    'role' => '<span class="badge badge-info">' . ($log->role ?? 'N/A') . '</span>',

                    'module' => '<span class="badge badge-secondary">' . ($log->module ?? 'N/A') . '</span>',

                    'action' => '<span class="badge badge-' . $this->getActionBadge($log->action) . '">'
                        . ($log->action ?? 'N/A') . '</span>',

                    'description' => '<span class="text-wrap" style="max-width: 500px;">'
                        . ($log->description ?? 'N/A') . '</span>',

                    'created_at' => '<span class="font-weight-semibold">'
                        . ($log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A') . '</span>'
                ];
            }

            // Return DataTables response
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Activity Logs DataTables Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'draw' => $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch activity logs: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get total count of activity logs
     */
    public function getLogsCount(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->has('module') && !empty($request->module)) {
            $query->byModule($request->module);
        }

        $count = $query->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Log an activity via API
     */
    public function logActivity(Request $request)
    {
        $request->validate([
            'module' => 'required|string',
            'action' => 'required|string',
            'description' => 'required|string',
        ]);

        // Try to get authenticated user, but create a fallback if not authenticated
        $user = auth()->user();
        if (!$user) {
            // Create a fallback user object for API calls
            $user = new \stdClass();
            $user->id = 'api_user';
            $user->name = 'API User';
        }

        $module = $request->input('module');
        $action = $request->input('action');
        $description = $request->input('description');

        $success = $this->activityLogger->log($user, $module, $action, $description, $request);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Activity logged successfully' : 'Failed to log activity'
        ]);
    }

    /**
     * Get activity logs for a specific module
     */
    public function getModuleLogs(Request $request, $module)
    {
        $limit = $request->get('limit', 100);
        $logs = ActivityLog::byModule($module)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Get all activity logs
     */
    public function getAllLogs(Request $request)
    {
        $limit = $request->get('limit', 50);
        $logs = ActivityLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Get activity logs for cuisines module (specific endpoint for testing)
     */
    public function getCuisinesLogs(Request $request)
    {
        return $this->getModuleLogs($request, 'cuisines');
    }

    /**
     * Helper function to get badge class for user type
     */
    private function getUserTypeBadge($userType)
    {
        switch($userType) {
            case 'admin': return 'primary';
            case 'merchant': return 'success';
            case 'driver': return 'warning';
            case 'customer': return 'info';
            default: return 'secondary';
        }
    }

    /**
     * Helper function to get badge class for action
     */
    private function getActionBadge($action)
    {
        switch($action) {
            case 'created': return 'success';
            case 'updated': return 'warning';
            case 'deleted': return 'danger';
            case 'viewed': return 'info';
            default: return 'secondary';
        }
    }
}
