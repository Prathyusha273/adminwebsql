<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('zone.index');
    }

    public function edit($id)
    {
        try {
            $zone = Zone::find($id);
            
            if (!$zone) {
                return redirect()->route('zone')->with('error', 'Zone not found');
            }
            
            return view('zone.edit')
                ->with('id', $id)
                ->with('zone', $zone);
        } catch (\Exception $e) {
            \Log::error('Error loading zone edit: ' . $e->getMessage());
            return redirect()->route('zone')->with('error', 'Error loading zone');
        }
    }

    public function create()
    {
        return view('zone.create');
    }

    /**
     * Get all zones data for index page
     */
    public function getZonesData(Request $request)
    {
        try {
            $zones = Zone::select('id', 'name', 'latitude', 'longitude', 'area', 'publish')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $zones
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching zones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching zones data'
            ], 500);
        }
    }

    /**
     * Get single zone by ID
     */
    public function getZoneById($id)
    {
        try {
            $zone = Zone::find($id);

            if (!$zone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $zone
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching zone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching zone data'
            ], 500);
        }
    }

    /**
     * Create new zone
     */
    public function store(Request $request)
    {
        try {
            \Log::info('=== Create Zone Called ===');
            \Log::info('Request data:', $request->all());
            
            $request->validate([
                'name' => 'required|string|max:255',
                'coordinates' => 'required'
            ]);

            $id = Str::uuid()->toString();

            // Use direct DB insert to ensure it saves
            $data = [
                'id' => $id,
                'name' => $request->name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'area' => $request->area,
                'publish' => $request->publish ? 1 : 0
            ];
            
            \Log::info('Data to insert:', $data);
            
            $inserted = \DB::table('zone')->insert($data);

            if ($inserted) {
                \Log::info('✅ Zone created successfully with ID: ' . $id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Zone created successfully',
                    'id' => $id
                ]);
            } else {
                \Log::error('❌ Failed to insert zone');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create zone - database insert failed'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('❌ Error creating zone: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error creating zone: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update zone
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('=== Update Zone Called for ID: ' . $id);
            \Log::info('Request data:', $request->all());
            
            // Check if zone exists
            $zoneExists = \DB::table('zone')->where('id', $id)->exists();

            if (!$zoneExists) {
                \Log::error('Zone not found: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }

            // Use direct DB update to ensure it saves
            $data = [
                'name' => $request->name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'area' => $request->area,
                'publish' => $request->publish ? 1 : 0
            ];
            
            \Log::info('Data to update:', $data);

            $updated = \DB::table('zone')
                ->where('id', $id)
                ->update($data);
            
            \Log::info('DB update result: ' . $updated . ' row(s) affected');

            if ($updated !== false) {
                \Log::info('✅ Zone updated successfully with ID: ' . $id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Zone updated successfully'
                ]);
            } else {
                \Log::error('❌ Failed to update zone');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update zone - database error'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('❌ Error updating zone: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error updating zone: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle zone publish status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            \Log::info('=== Toggle Zone Status for ID: ' . $id);
            \Log::info('Request publish value: ' . ($request->publish ?? 'not set'));
            
            $zone = Zone::find($id);

            if (!$zone) {
                \Log::error('Zone not found: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }

            // ALWAYS toggle - flip the current status
            $currentStatus = $zone->publish;
            $newStatus = $currentStatus ? 0 : 1;
            
            \Log::info('Current status: ' . $currentStatus . ', New status (toggled): ' . $newStatus);

            // Use direct DB update to ensure it saves
            $updated = \DB::table('zone')
                ->where('id', $id)
                ->update(['publish' => $newStatus]);
            
            \Log::info('DB update result: ' . $updated . ' row(s) affected');

            if ($updated !== false) {
                \Log::info('✅ Zone status toggled successfully');
                return response()->json([
                    'success' => true,
                    'message' => 'Zone status updated successfully',
                    'publish' => $newStatus ? true : false
                ]);
            } else {
                \Log::error('❌ Failed to update zone status - 0 rows affected');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update zone status'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('❌ Error toggling zone status: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error updating zone status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete zone
     */
    public function destroy($id)
    {
        try {
            $zone = Zone::find($id);

            if (!$zone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }

            $zone->delete();

            return response()->json([
                'success' => true,
                'message' => 'Zone deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting zone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting zone'
            ], 500);
        }
    }

    /**
     * Delete multiple zones
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->ids;

            if (!$ids || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No zones selected'
                ], 400);
            }

            Zone::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Zones deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting multiple zones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting zones'
            ], 500);
        }
    }
}
