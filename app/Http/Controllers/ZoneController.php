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
            $request->validate([
                'name' => 'required|string|max:255',
                'coordinates' => 'required'
            ]);

            $id = Str::uuid()->toString();

            $zone = new Zone();
            $zone->id = $id;
            $zone->name = $request->name;
            $zone->latitude = $request->latitude;
            $zone->longitude = $request->longitude;
            $zone->area = $request->area;
            $zone->publish = $request->publish ? 1 : 0;

            $zone->save();

            return response()->json([
                'success' => true,
                'message' => 'Zone created successfully',
                'id' => $id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating zone: ' . $e->getMessage());
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
            $zone = Zone::find($id);

            if (!$zone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }

            $zone->name = $request->name;
            $zone->latitude = $request->latitude;
            $zone->longitude = $request->longitude;
            $zone->area = $request->area;
            $zone->publish = $request->publish ? 1 : 0;

            $zone->save();

            return response()->json([
                'success' => true,
                'message' => 'Zone updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating zone: ' . $e->getMessage());
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
            $zone = Zone::find($id);

            if (!$zone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }

            // Convert publish to integer (0 or 1)
            if ($request->has('publish')) {
                $zone->publish = $request->publish ? 1 : 0;
            } else {
                // Toggle current value
                $zone->publish = $zone->publish ? 0 : 1;
            }
            $zone->save();

            return response()->json([
                'success' => true,
                'message' => 'Zone status updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling zone status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating zone status'
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
