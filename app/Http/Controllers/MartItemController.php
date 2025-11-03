<?php

namespace App\Http\Controllers;

use App\Models\MartItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * MartItemController
 *
 * Handles CRUD operations for mart items using SQL database.
 *
 * Default Fields for New Items:
 * - reviewCount: "0" (string) - Number of reviews
 * - reviewSum: "0" (string) - Sum of review ratings
 * - These fields are automatically set to "0" for all new items
 */
class MartItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id = '')
    {
        return view("martItems.index")->with('id', $id);
    }

    public function edit($id)
    {
        return view('martItems.edit')->with('id', $id);
    }

    public function create($id = '')
    {
        return view('martItems.create')->with('id', $id);
    }

    public function createItem()
    {
        return view('martItems.create');
    }

    /**
     * Get mart items data with filtering, search, and pagination
     */
    public function getMartItemsData(Request $request)
    {
        \Log::info('=== getMartItemsData called ===');
        \Log::info('Request params:', $request->all());
        
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = strtolower((string) $request->input('search.value', ''));
            $orderColumnIdx = (int) $request->input('order.0.column', 0);
            $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            
            \Log::info("Parsed params - draw: $draw, start: $start, length: $length, search: $searchValue");

            // Get filter parameters
            $vendorId = $request->input('vendor_id', '');
            $categoryId = $request->input('category_id', '');
            $brandId = $request->input('brand_id', '');
            $foodType = $request->input('food_type', '');
            $feature = $request->input('feature', '');
            
            \Log::info("Filters - vendor: $vendorId, category: $categoryId, brand: $brandId, type: $foodType, feature: $feature");

            // Base query
            $query = MartItem::select('mart_items.*');

            // Apply vendor filter (only if vendor exists)
            if (!empty($vendorId)) {
                // Check if vendor exists
                $vendorExists = DB::table('vendors')->where('id', $vendorId)->exists();
                \Log::info("Vendor filter - ID: $vendorId, Exists: " . ($vendorExists ? 'YES' : 'NO'));
                
                if ($vendorExists) {
                    $query->where('mart_items.vendorID', $vendorId);
                } else {
                    \Log::warning("Vendor ID '$vendorId' not found - skipping vendor filter");
                    // Don't apply filter if vendor doesn't exist
                }
            }

            // Apply category filter
            if (!empty($categoryId)) {
                $query->where('mart_items.categoryID', $categoryId);
            }

            // Apply brand filter
            if (!empty($brandId)) {
                $query->where('mart_items.brandID', $brandId);
            }

            // Apply food type filter
            if (!empty($foodType)) {
                if ($foodType === 'veg') {
                    $query->where('mart_items.nonveg', 0);
                } elseif ($foodType === 'non-veg') {
                    $query->where('mart_items.nonveg', 1);
                }
            }

            // Apply feature filter
            if (!empty($feature)) {
                switch ($feature) {
                    case 'spotlight':
                        $query->where('mart_items.isSpotlight', 1);
                        break;
                    case 'steal_of_moment':
                        $query->where('mart_items.isStealOfMoment', 1);
                        break;
                    case 'featured':
                        $query->where('mart_items.isFeature', 1);
                        break;
                    case 'trending':
                        $query->where('mart_items.isTrending', 1);
                        break;
                    case 'new':
                        $query->where('mart_items.isNew', 1);
                        break;
                    case 'best_seller':
                        $query->where('mart_items.isBestSeller', 1);
                        break;
                    case 'seasonal':
                        $query->where('mart_items.isSeasonal', 1);
                        break;
                }
            }

            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where(DB::raw('LOWER(mart_items.name)'), 'like', "%{$searchValue}%")
                      ->orWhere(DB::raw('LOWER(mart_items.vendorTitle)'), 'like', "%{$searchValue}%")
                      ->orWhere(DB::raw('LOWER(mart_items.categoryTitle)'), 'like', "%{$searchValue}%")
                      ->orWhere(DB::raw('LOWER(mart_items.brandTitle)'), 'like', "%{$searchValue}%")
                      ->orWhere('mart_items.price', 'like', "%{$searchValue}%")
                      ->orWhere('mart_items.disPrice', 'like', "%{$searchValue}%");
                });
            }

            // Get total count before pagination
            $totalRecords = $query->count();
            
            \Log::info("MartItems Query - Total Records: " . $totalRecords);
            \Log::info("MartItems Query - Start: " . $start . ", Length: " . $length);

            // Apply ordering
            $orderableColumns = ['id', 'name', 'price', 'disPrice', 'vendorTitle', 'categoryTitle', 'brandTitle'];
            $orderByField = $orderableColumns[$orderColumnIdx] ?? 'name';
            
            \Log::info("MartItems Query - Order by: " . $orderByField . " " . $orderDir);
            
            // Simple ordering (the created_at field is already sortable as string)
            if ($orderByField === 'id') {
                $query->orderBy("mart_items.id", $orderDir);
            } else if ($orderByField === 'created_at') {
                $query->orderBy("mart_items.created_at", $orderDir);
            } else {
                $query->orderBy("mart_items.{$orderByField}", $orderDir);
            }

            // Apply pagination
            $items = $query->skip($start)->take($length)->get();
            
            \Log::info("MartItems Query - Items retrieved: " . $items->count());

            // Format data for DataTables
            $data = [];
            foreach ($items as $item) {
                // Parse options if present
                $options = [];
                if (!empty($item->options)) {
                    $optionsData = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                    if (is_array($optionsData)) {
                        $options = $optionsData;
                    }
                }

                $data[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price ?? 0,
                    'disPrice' => $item->disPrice ?? 0,
                    'vendorID' => $item->vendorID,
                    'vendorTitle' => $item->vendorTitle ?? '',
                    'categoryID' => $item->categoryID,
                    'categoryTitle' => $item->categoryTitle ?? '',
                    'subcategoryID' => $item->subcategoryID,
                    'subcategoryTitle' => $item->subcategoryTitle ?? '',
                    'brandID' => $item->brandID,
                    'brandTitle' => $item->brandTitle ?? '',
                    'photo' => $item->photo,
                    'description' => $item->description,
                    'publish' => $item->publish ? true : false,
                    'isAvailable' => $item->isAvailable ? true : false,
                    'nonveg' => $item->nonveg ? true : false,
                    'section' => $item->section ?? 'General',
                    'has_options' => $item->has_options ? true : false,
                    'options' => $options,
                    'options_count' => $item->options_count ?? 0,
                    'price_range' => $item->price_range,
                    'min_price' => $item->min_price ?? 0,
                    'max_price' => $item->max_price ?? 0,
                    'best_value_option' => $item->best_value_option,
                    'savings_percentage' => $item->savings_percentage ?? 0,
                    'reviewCount' => $item->reviewCount ?? '0',
                    'reviewSum' => $item->reviewSum ?? '0',
                    'rating' => $item->rating ?? 0,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            }

            \Log::info("MartItems Query - Data array count: " . count($data));
            \Log::info("MartItems Query - Returning response with draw: " . $draw);
            
            $response = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ];
            
            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error in getMartItemsData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'draw' => $draw ?? 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get single mart item by ID
     */
    public function getMartItemById($id)
    {
        try {
            $item = MartItem::find($id);
            
            if (!$item) {
                return response()->json(['error' => 'Item not found'], 404);
            }

            // Parse options if present
            $options = [];
            if (!empty($item->options)) {
                $optionsData = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                if (is_array($optionsData)) {
                    $options = $optionsData;
                }
            }

            return response()->json([
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price ?? 0,
                'disPrice' => $item->disPrice ?? 0,
                'vendorID' => $item->vendorID,
                'vendorTitle' => $item->vendorTitle ?? '',
                'categoryID' => $item->categoryID,
                'categoryTitle' => $item->categoryTitle ?? '',
                'subcategoryID' => $item->subcategoryID,
                'subcategoryTitle' => $item->subcategoryTitle ?? '',
                'brandID' => $item->brandID,
                'brandTitle' => $item->brandTitle ?? '',
                'photo' => $item->photo,
                'description' => $item->description,
                'publish' => $item->publish ? true : false,
                'isAvailable' => $item->isAvailable ? true : false,
                'nonveg' => $item->nonveg ? true : false,
                'veg' => $item->veg ? true : false,
                'section' => $item->section ?? 'General',
                'quantity' => $item->quantity ?? 10,
                'calories' => $item->calories ?? 0,
                'grams' => $item->grams ?? 0,
                'proteins' => $item->proteins ?? 0,
                'fats' => $item->fats ?? 0,
                'has_options' => $item->has_options ? true : false,
                'options' => $options,
                'options_count' => $item->options_count ?? 0,
                'options_toggle' => $item->options_toggle ? true : false,
                'options_enabled' => $item->options_enabled ? true : false,
                'price_range' => $item->price_range,
                'min_price' => $item->min_price ?? 0,
                'max_price' => $item->max_price ?? 0,
                'default_option_id' => $item->default_option_id,
                'best_value_option' => $item->best_value_option,
                'savings_percentage' => $item->savings_percentage ?? 0,
                'reviewCount' => $item->reviewCount ?? '0',
                'reviewSum' => $item->reviewSum ?? '0',
                'rating' => $item->rating ?? 0,
                'reviews' => $item->reviews ?? 0,
                'isStealOfMoment' => $item->isStealOfMoment ? true : false,
                'isTrending' => $item->isTrending ? true : false,
                'isSeasonal' => $item->isSeasonal ? true : false,
                'isBestSeller' => $item->isBestSeller ? true : false,
                'isNew' => $item->isNew ? true : false,
                'isSpotlight' => $item->isSpotlight ? true : false,
                'isFeature' => $item->isFeature ? true : false,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getMartItemById: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch item'], 500);
        }
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $id)
    {
        try {
            \Log::info('=== togglePublish called for ID: ' . $id);
            
            $item = MartItem::find($id);
            
            if (!$item) {
                \Log::error('Item not found: ' . $id);
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            // Get current status and toggle it
            $currentStatus = $item->publish;
            $newStatus = $currentStatus ? 0 : 1;
            
            \Log::info('Current publish status: ' . $currentStatus . ', New status: ' . $newStatus);

            // Use direct DB update to ensure it saves
            $updated = DB::table('mart_items')
                ->where('id', $id)
                ->update([
                    'publish' => $newStatus,
                    'updated_at' => '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"'
                ]);

            \Log::info('DB update result: ' . $updated . ' row(s) affected');

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Publish status updated successfully',
                    'publish' => $newStatus ? true : false
                ]);
            } else {
                \Log::error('No rows updated for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update - no rows affected'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Error in togglePublish: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle availability status
     */
    public function toggleAvailability(Request $request, $id)
    {
        try {
            \Log::info('=== toggleAvailability called for ID: ' . $id);
            
            $item = MartItem::find($id);
            
            if (!$item) {
                \Log::error('Item not found: ' . $id);
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            // Get current status and toggle it
            $currentStatus = $item->isAvailable;
            $newStatus = $currentStatus ? 0 : 1;
            
            \Log::info('Current availability status: ' . $currentStatus . ', New status: ' . $newStatus);

            // Use direct DB update to ensure it saves
            $updated = DB::table('mart_items')
                ->where('id', $id)
                ->update([
                    'isAvailable' => $newStatus,
                    'updated_at' => '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"'
                ]);

            \Log::info('DB update result: ' . $updated . ' row(s) affected');

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Availability status updated successfully',
                    'isAvailable' => $newStatus ? true : false
                ]);
            } else {
                \Log::error('No rows updated for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update - no rows affected'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Error in toggleAvailability: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete mart item
     */
    public function deleteMartItem($id)
    {
        try {
            $item = MartItem::find($id);
            
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in deleteMartItem: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete item'], 500);
        }
    }

    /**
     * Bulk delete mart items
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids) || !is_array($ids)) {
                return response()->json(['success' => false, 'message' => 'No items selected'], 400);
            }

            MartItem::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' item(s) deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in bulkDelete: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete items'], 500);
        }
    }

    /**
     * Inline update for price fields
     */
    public function inlineUpdate(Request $request, $id)
    {
        try {
            $item = MartItem::find($id);
            
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            $field = $request->input('field');
            $value = $request->input('value', 0);

            // Validate field
            if (!in_array($field, ['price', 'disPrice'])) {
                return response()->json(['success' => false, 'message' => 'Invalid field'], 400);
            }

            // Validate value
            if (!is_numeric($value) || $value < 0) {
                return response()->json(['success' => false, 'message' => 'Invalid value'], 400);
            }

            $value = (int) $value;
            $item->$field = $value;

            // If updating price and disPrice is greater than price, reset disPrice
            if ($field === 'price' && $item->disPrice > $value) {
                $item->disPrice = 0;
                $message = 'Price updated successfully. Discount price was reset because it was greater than the new price.';
            } else {
                $message = ucfirst($field) . ' updated successfully';
            }

            // If updating disPrice and it's greater than price, return error
            if ($field === 'disPrice' && $value > $item->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Discount price cannot be greater than regular price'
                ], 400);
            }

            $item->updated_at = '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"';
            $item->save();

            return response()->json([
                'success' => true,
                'message' => $message,
                'field' => $field,
                'value' => $value,
                'price' => $item->price,
                'disPrice' => $item->disPrice
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in inlineUpdate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update'], 500);
        }
    }

    /**
     * Get mart categories
     */
    public function getCategories()
    {
        try {
            $categories = DB::table('mart_categories')
                ->select('id', 'title')
                ->orderBy('title', 'asc')
                ->get()
                ->toArray();

            return response()->json(array_values($categories));

        } catch (\Exception $e) {
            \Log::error('Error in getCategories: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Get brands
     */
    public function getBrands()
    {
        try {
            // Check if brands table exists
            if (!DB::getSchemaBuilder()->hasTable('brands')) {
                return response()->json([]);
            }

            $brands = DB::table('brands')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();

            return response()->json(array_values($brands));

        } catch (\Exception $e) {
            \Log::error('Error in getBrands: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Get subcategories (optionally filtered by category ID)
     */
    public function getSubcategories(Request $request)
    {
        try {
            $categoryId = $request->input('category_id', '');
            
            $query = DB::table('mart_subcategories')
                ->select('id', 'title', 'parent_category_id as categoryID', 'parent_category_title', 'photo', 'publish');
            
            // Filter by category if provided
            if (!empty($categoryId)) {
                $query->where('parent_category_id', $categoryId);
            }
            
            // Only show published subcategories
            $query->where('publish', 1);
            
            $subcategories = $query->orderBy('title', 'asc')
                ->get()
                ->toArray();

            \Log::info('Fetched ' . count($subcategories) . ' subcategories' . ($categoryId ? ' for category ' . $categoryId : ''));
            
            return response()->json(array_values($subcategories));

        } catch (\Exception $e) {
            \Log::error('Error in getSubcategories: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([]);
        }
    }

    /**
     * Get mart vendors
     */
    public function getVendors()
    {
        try {
            $vendors = DB::table('vendors')
                ->select('id', 'title')
                ->where('vType', 'mart')
                ->orderBy('title', 'asc')
                ->get()
                ->toArray();

            return response()->json(array_values($vendors));

                    } catch (\Exception $e) {
            \Log::error('Error in getVendors: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Get placeholder image from settings
     */
    public function getPlaceholderImage()
    {
        try {
            // Settings table structure: doc_id, document_name, fields
            if (DB::getSchemaBuilder()->hasTable('settings')) {
                $setting = DB::table('settings')
                    ->where('document_name', 'placeHolderImage')
                    ->first();

                if ($setting && !empty($setting->fields)) {
                    $fieldsData = json_decode($setting->fields, true);
                    if (isset($fieldsData['image'])) {
                        return response()->json(['image' => $fieldsData['image']]);
                    }
                }
            }

            // Return default placeholder if not found
            return response()->json(['image' => asset('images/placeholder-image.png')]);

        } catch (\Exception $e) {
            \Log::error('Error in getPlaceholderImage: ' . $e->getMessage());
            return response()->json(['image' => asset('images/placeholder-image.png')]);
        }
    }

    /**
     * Get currency settings
     */
    public function getCurrencySettings()
    {
        try {
            $currency = DB::table('currencies')
                ->where('isActive', 1)
                ->first();

            if ($currency) {
                return response()->json([
                    'symbol' => $currency->symbol ?? '$',
                    'symbolAtRight' => $currency->symbolAtRight ?? false,
                    'decimal_degits' => $currency->decimal_degits ?? 0,
                ]);
            }

            return response()->json([
                'symbol' => '$',
                'symbolAtRight' => false,
                'decimal_degits' => 0,
            ]);

                    } catch (\Exception $e) {
            \Log::error('Error in getCurrencySettings: ' . $e->getMessage());
            return response()->json([
                'symbol' => '$',
                'symbolAtRight' => false,
                'decimal_degits' => 0,
            ]);
        }
    }

    /**
     * Store new mart item (SQL-based)
     */
    public function store(Request $request)
    {
        try {
            \Log::info('=== Store Mart Item Called ===');
            \Log::info('Request data:', $request->all());
            
            // Generate unique ID
            $itemId = $request->input('id', uniqid());
            \Log::info('Generated item ID: ' . $itemId);
            
            // Prepare data
            $data = [
                'id' => $itemId,
                'name' => $request->input('name'),
                'price' => (int) $request->input('price', 0),
                'disPrice' => (int) $request->input('disPrice', 0),
                'vendorID' => $request->input('vendorID'),
                'vendorTitle' => $request->input('vendorTitle', ''),
                'categoryID' => $request->input('categoryID'),
                'categoryTitle' => $request->input('categoryTitle', ''),
                'subcategoryID' => $request->input('subcategoryID', ''),
                'subcategoryTitle' => $request->input('subcategoryTitle', ''),
                'brandID' => $request->input('brandID', ''),
                'brandTitle' => $request->input('brandTitle', ''),
                'photo' => $request->input('photo', ''),
                'description' => $request->input('description', ''),
                'section' => $request->input('section', 'General'),
                'publish' => $request->input('publish', false) ? 1 : 0,
                'isAvailable' => $request->input('isAvailable', true) ? 1 : 0,
                'nonveg' => $request->input('nonveg', false) ? 1 : 0,
                'veg' => $request->input('veg', true) ? 1 : 0,
                'quantity' => (int) $request->input('quantity', 10),
                'calories' => (int) $request->input('calories', 0),
                'grams' => (int) $request->input('grams', 0),
                'proteins' => (int) $request->input('proteins', 0),
                'fats' => (int) $request->input('fats', 0),
                'isSpotlight' => $request->input('isSpotlight', false) ? 1 : 0,
                'isStealOfMoment' => $request->input('isStealOfMoment', false) ? 1 : 0,
                'isFeature' => $request->input('isFeature', false) ? 1 : 0,
                'isTrending' => $request->input('isTrending', false) ? 1 : 0,
                'isNew' => $request->input('isNew', false) ? 1 : 0,
                'isBestSeller' => $request->input('isBestSeller', false) ? 1 : 0,
                'isSeasonal' => $request->input('isSeasonal', false) ? 1 : 0,
                'has_options' => $request->input('has_options', false) ? 1 : 0,
                'options' => $request->input('options', '[]'),
                'options_count' => (int) $request->input('options_count', 0),
                'options_toggle' => $request->input('options_toggle', false) ? 1 : 0,
                'options_enabled' => $request->input('options_enabled', false) ? 1 : 0,
                'price_range' => $request->input('price_range', ''),
                'min_price' => (int) $request->input('min_price', 0),
                'max_price' => (int) $request->input('max_price', 0),
                'default_option_id' => $request->input('default_option_id', ''),
                'best_value_option' => $request->input('best_value_option', ''),
                'savings_percentage' => (float) $request->input('savings_percentage', 0),
                'addOnsTitle' => $request->input('addOnsTitle', '[]'),
                'addOnsPrice' => $request->input('addOnsPrice', '[]'),
                'product_specification' => $request->input('product_specification', '{}'),
                'item_attribute' => $request->input('item_attribute', null),
                'reviewCount' => '0',
                'reviewSum' => '0',
                'reviews' => 0,
                'rating' => 0,
                'created_at' => '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"',
                'updated_at' => '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"',
            ];

            \Log::info('Data to insert:', $data);

            // Use direct DB insert to ensure it saves
            $inserted = DB::table('mart_items')->insert($data);
            
            if ($inserted) {
                \Log::info('✅ Mart item created successfully with ID: ' . $itemId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Mart item created successfully',
                    'id' => $itemId
                ]);
                        } else {
                \Log::error('❌ Failed to insert mart item - no rows affected');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create mart item - database insert failed'
                ], 500);
            }

                    } catch (\Exception $e) {
            \Log::error('❌ Error creating mart item: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create mart item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing mart item (SQL-based)
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('=== Update Mart Item Called for ID: ' . $id);
            \Log::info('Request data:', $request->all());
            
            // Check if item exists
            $itemExists = DB::table('mart_items')->where('id', $id)->exists();
            
            if (!$itemExists) {
                \Log::error('Item not found: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Prepare update data - use ALL fields from request
            $data = [
                'name' => $request->input('name'),
                'price' => (int) $request->input('price', 0),
                'disPrice' => (int) $request->input('disPrice', 0),
                'vendorID' => $request->input('vendorID'),
                'vendorTitle' => $request->input('vendorTitle', ''),
                'categoryID' => $request->input('categoryID'),
                'categoryTitle' => $request->input('categoryTitle', ''),
                'subcategoryID' => $request->input('subcategoryID', ''),
                'subcategoryTitle' => $request->input('subcategoryTitle', ''),
                'brandID' => $request->input('brandID', ''),
                'brandTitle' => $request->input('brandTitle', ''),
                'photo' => $request->input('photo', ''),
                'description' => $request->input('description', ''),
                'section' => $request->input('section', 'General'),
                'publish' => $request->input('publish', false) ? 1 : 0,
                'isAvailable' => $request->input('isAvailable', true) ? 1 : 0,
                'nonveg' => $request->input('nonveg', false) ? 1 : 0,
                'veg' => $request->input('veg', true) ? 1 : 0,
                'quantity' => (int) $request->input('quantity', 10),
                'calories' => (int) $request->input('calories', 0),
                'grams' => (int) $request->input('grams', 0),
                'proteins' => (int) $request->input('proteins', 0),
                'fats' => (int) $request->input('fats', 0),
                'isSpotlight' => $request->input('isSpotlight', false) ? 1 : 0,
                'isStealOfMoment' => $request->input('isStealOfMoment', false) ? 1 : 0,
                'isFeature' => $request->input('isFeature', false) ? 1 : 0,
                'isTrending' => $request->input('isTrending', false) ? 1 : 0,
                'isNew' => $request->input('isNew', false) ? 1 : 0,
                'isBestSeller' => $request->input('isBestSeller', false) ? 1 : 0,
                'isSeasonal' => $request->input('isSeasonal', false) ? 1 : 0,
                'has_options' => $request->input('has_options', false) ? 1 : 0,
                'options' => $request->input('options', '[]'),
                'options_count' => (int) $request->input('options_count', 0),
                'options_toggle' => $request->input('options_toggle', false) ? 1 : 0,
                'options_enabled' => $request->input('options_enabled', false) ? 1 : 0,
                'price_range' => $request->input('price_range', ''),
                'min_price' => (int) $request->input('min_price', 0),
                'max_price' => (int) $request->input('max_price', 0),
                'default_option_id' => $request->input('default_option_id', ''),
                'best_value_option' => $request->input('best_value_option', ''),
                'savings_percentage' => (float) $request->input('savings_percentage', 0),
                'addOnsTitle' => $request->input('addOnsTitle', '[]'),
                'addOnsPrice' => $request->input('addOnsPrice', '[]'),
                'product_specification' => $request->input('product_specification', '{}'),
                'item_attribute' => $request->input('item_attribute', null),
                'updated_at' => '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"',
            ];
            
            \Log::info('Data to update:', $data);

            // Use direct DB update to ensure it saves
            $updated = DB::table('mart_items')
                ->where('id', $id)
                ->update($data);
            
            \Log::info('DB update result: ' . $updated . ' row(s) affected');

            if ($updated !== false) { // update() returns false on error, 0 or 1 on success
                \Log::info('✅ Mart item updated successfully with ID: ' . $id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Mart item updated successfully'
                ]);
            } else {
                \Log::error('❌ Failed to update mart item - database error');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update mart item - database error'
                ], 500);
            }

            } catch (\Exception $e) {
            \Log::error('❌ Error updating mart item: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update mart item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download template for bulk import
     */
    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/mart_items_import_template.xlsx');

        // Create template directory if it doesn't exist
        $templateDir = dirname($filePath);
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        // Generate template if it doesn't exist
        if (!file_exists($filePath)) {
            $this->generateTemplate($filePath);
        }

        return response()->download($filePath, 'mart_items_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="mart_items_import_template.xlsx"'
        ]);
    }

    /**
     * Generate Excel template for mart items import
     */
    private function generateTemplate($filePath)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = [
                'name', 'price', 'description', 'vendorID', 'vendorName',
                'categoryID', 'categoryName', 'subcategoryID', 'subcategoryName',
                'section', 'disPrice', 'publish', 'nonveg', 'isAvailable', 'photo',
                'spotlight', 'steal_of_moment', 'featured', 'trending', 'new', 'best_seller', 'seasonal',
                'brandID', 'brandName'
            ];

            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $column++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

        } catch (\Exception $e) {
            \Log::error('Error generating template: ' . $e->getMessage());
        }
    }

    /**
     * Import mart items from Excel (kept as is since it uses Firebase)
     * Note: This will need to be updated if you want to fully migrate from Firebase
     */
    public function import(Request $request)
    {
        // This method still uses Firebase - can be updated later if needed
        return back()->with('error', 'Import functionality is not yet available for SQL version');
    }
}
