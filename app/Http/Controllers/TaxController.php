<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tax;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class TaxController extends Controller
{

     public function __construct()
    {
       $this->middleware('auth');
    }


	  public function index()
    {

        return view("taxes.index");
    }


  public function edit($id)
  {
      return view('taxes.edit')->with('id',$id);
  }

   public function create()
  {
      return view('taxes.create');
  }

    /**
     * Get single tax data (for edit page)
     */
    public function getTax($id)
    {
        try {
            $tax = Tax::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $tax
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tax not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a new tax
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'tax' => 'required|numeric|min:0',
                'type' => 'required|in:fix,percentage',
            ]);

            // Generate unique ID
            $id = uniqid('tax_', true);

            $tax = Tax::create([
                'id' => $id,
                'title' => $request->title,
                'country' => $request->country,
                'tax' => $request->tax,
                'type' => $request->type,
                'enable' => $request->has('enable') ? 1 : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax created successfully',
                'data' => $tax
            ]);
        } catch (\Exception $e) {
            \Log::error('Tax create error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tax: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing tax
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'tax' => 'required|numeric|min:0',
                'type' => 'required|in:fix,percentage',
            ]);

            $tax = Tax::findOrFail($id);
            $tax->update([
                'title' => $request->title,
                'country' => $request->country,
                'tax' => $request->tax,
                'type' => $request->type,
                'enable' => $request->has('enable') ? 1 : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax updated successfully',
                'data' => $tax
            ]);
        } catch (\Exception $e) {
            \Log::error('Tax update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tax: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get taxes data for DataTables (Server-side processing from MySQL)
     */
    public function data(Request $request)
    {
        try {
            // Get DataTables parameters
            $draw = (int) $request->get('draw', 1);
            $start = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 10);
            $searchValue = $request->input('search.value', '');
            $orderColumnIndex = (int) $request->input('order.0.column', 1);
            $orderDirection = $request->input('order.0.dir', 'asc');

            // Column mapping (based on your table structure)
            $columns = ['checkbox', 'title', 'country', 'type', 'tax', 'enable', 'actions'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'title';

            // Build query
            $query = Tax::query();

            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('title', 'like', '%' . $searchValue . '%')
                      ->orWhere('country', 'like', '%' . $searchValue . '%')
                      ->orWhere('type', 'like', '%' . $searchValue . '%')
                      ->orWhere('tax', 'like', '%' . $searchValue . '%');
                });
            }

            // Get total records
            $totalRecords = Tax::count();

            // Get filtered records count
            $filteredRecords = $query->count();

            // Apply sorting and pagination
            if (in_array($orderColumn, ['title', 'country', 'type', 'tax', 'enable'])) {
                $query->orderBy($orderColumn, $orderDirection);
            }

            $taxes = $query->skip($start)
                          ->take($length)
                          ->get();

            // Format data for DataTables
            $data = [];
            foreach ($taxes as $tax) {
                $editUrl = route('tax.edit', ['id' => $tax->id]);

                // Format checkbox
                $checkbox = '<input type="checkbox" id="is_open_' . $tax->id . '" class="is_open" dataId="' . $tax->id . '">
                            <label class="col-3 control-label" for="is_open_' . $tax->id . '"></label>';

                // Format title with link
                $titleLink = '<a href="' . $editUrl . '">' . e($tax->title) . '</a>';

                // Format type
                $typeFormatted = ucfirst($tax->type);

                // Format tax value (tax is stored as string in database)
                $taxValue = '';
                if ($tax->type == 'fix') {
                    $taxValue = '₹' . number_format((float)$tax->tax, 2);
                } else {
                    $taxValue = $tax->tax . '%';
                }

                // Format enable toggle
                $enableToggle = $tax->enable
                    ? '<label class="switch"><input type="checkbox" checked id="' . $tax->id . '" name="isSwitch"><span class="slider round"></span></label>'
                    : '<label class="switch"><input type="checkbox" id="' . $tax->id . '" name="isSwitch"><span class="slider round"></span></label>';

                // Format actions
                $actions = '<a href="' . $editUrl . '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>
                           <a id="' . $tax->id . '" class="delete-btn" name="tax-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';

                $data[] = [
                    $checkbox,
                    $titleLink,
                    e($tax->country),
                    $typeFormatted,
                    $taxValue,
                    $enableToggle,
                    $actions
                ];
            }

            // Return DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Tax DataTables Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'draw' => $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch taxes: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle tax enable status
     */
    public function toggle(Request $request, $id)
    {
        try {
            $tax = Tax::findOrFail($id);
            $tax->enable = $request->input('enable', !$tax->enable);
            $tax->save();

            return response()->json([
                'success' => true,
                'message' => 'Tax status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tax status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a tax
     */
    public function destroy($id)
    {
        try {
            $tax = Tax::findOrFail($id);
            $tax->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tax deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tax: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete taxes
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            Tax::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' taxes deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete taxes: ' . $e->getMessage()
            ], 500);
        }
    }


}
