<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\DB;

class FoodController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($id='')
    {
        return view("foods.index")->with('id',$id);
    }

    public function edit($id)
    {
        return view('foods.edit')->with('id',$id);
    }

    public function create($id='')
    {
        return view('foods.create')->with('id',$id);
    }
    public function createfood()
    {
        return view('foods.create');
    }

    /**
     * Get foods data for DataTables (SQL-based)
     */
    public function data(Request $request)
    {
        $userPermissions = json_decode(@session('user_permissions'), true) ?: [];
        $canDelete = in_array('foods.delete', $userPermissions);

        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = strtolower((string) data_get($request->input('search'), 'value', ''));

        // Filters
        $restaurantFilter = $request->input('restaurant');
        $categoryFilter = $request->input('category');
        $foodTypeFilter = $request->input('foodType');
        $restaurantId = $request->input('restaurantId'); // For restaurant-specific view

        // Build query
        $query = \DB::table('vendor_products as vp')
            ->leftJoin('vendors as v', 'v.id', '=', 'vp.vendorID')
            ->leftJoin('vendor_categories as vc', 'vc.id', '=', 'vp.categoryID')
            ->select(
                'vp.id',
                'vp.name',
                'vp.photo',
                'vp.price',
                'vp.disPrice',
                'vp.vendorID',
                'vp.categoryID',
                'vp.description',
                'vp.publish',
                'vp.nonveg',
                'v.title as restaurant_name',
                'vc.title as category_name'
            );

        // Apply filters
        if ($restaurantId) {
            $query->where('vp.vendorID', $restaurantId);
        }
        if ($restaurantFilter) {
            $query->where('vp.vendorID', $restaurantFilter);
        }
        if ($categoryFilter) {
            $query->where('vp.categoryID', $categoryFilter);
        }
        if ($foodTypeFilter === 'veg') {
            $query->where('vp.nonveg', false);
        } elseif ($foodTypeFilter === 'non-veg') {
            $query->where('vp.nonveg', true);
        }

        // Count total
        $total = $query->count();

        // Search
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where(\DB::raw('LOWER(vp.name)'), 'like', '%' . $search . '%')
                  ->orWhere(\DB::raw('LOWER(v.title)'), 'like', '%' . $search . '%')
                  ->orWhere(\DB::raw('LOWER(vc.title)'), 'like', '%' . $search . '%')
                  ->orWhere('vp.price', 'like', '%' . $search . '%')
                  ->orWhere('vp.disPrice', 'like', '%' . $search . '%');
            });
        }

        $recordsFiltered = $query->count();

        // Ordering
        $order = $request->input('order.0', ['column' => 1, 'dir' => 'asc']);
        $orderColumnIndex = (int) data_get($order, 'column', 1);
        $orderDir = data_get($order, 'dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $orderableColumns = $canDelete
            ? ['', 'vp.name', 'vp.price', 'vp.disPrice', 'restaurant_name', 'category_name', '', '']
            : ['vp.name', 'vp.price', 'vp.disPrice', 'restaurant_name', 'category_name', '', ''];

        $orderBy = $orderableColumns[$orderColumnIndex] ?? 'vp.name';
        if ($orderBy && $orderBy !== '') {
            $query->orderBy($orderBy, $orderDir);
        }

        // Pagination
        $foods = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = [];
        foreach ($foods as $food) {
            $data[] = [
                'id' => $food->id,
                'name' => $food->name,
                'photo' => $food->photo,
                'price' => $food->price,
                'disPrice' => $food->disPrice,
                'vendorID' => $food->vendorID,
                'categoryID' => $food->categoryID,
                'restaurant_name' => $food->restaurant_name,
                'category_name' => $food->category_name,
                'description' => $food->description,
                'publish' => $food->publish,
                'nonveg' => $food->nonveg,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Get filter options (restaurants and categories)
     */
    public function options(Request $request)
    {
        $type = $request->input('type'); // 'restaurants' or 'categories'

        if ($type === 'restaurants') {
            $restaurants = \DB::table('vendors')
                ->where('vType', 'restaurant')
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->orderBy('title', 'asc')
                ->get(['id', 'title']);

            return response()->json(['success' => true, 'data' => $restaurants]);
        }

        if ($type === 'categories') {
            $categories = \DB::table('vendor_categories')
                ->orderBy('title', 'asc')
                ->get(['id', 'title']);

            return response()->json(['success' => true, 'data' => $categories]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid type']);
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $id)
    {
        try {
            $publish = $request->input('publish') === 'true' || $request->input('publish') === true;

            DB::table('vendor_products')
                ->where('id', $id)
                ->update(['publish' => $publish]);

            return response()->json([
                'success' => true,
                'message' => 'Publish status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating publish status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find vendor ID by name
     */
    private function findVendorByName($vendorName, $firestore)
    {
        try {
            $vendors = $firestore->collection('vendors')
                ->where('title', '==', trim($vendorName))
                ->limit(1)
                ->documents();

            foreach ($vendors as $vendor) {
                return $vendor->id();
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        return null;
    }

    /**
     * Find category ID by name
     */
    private function findCategoryByName($categoryName, $firestore)
    {
        try {
            $categories = $firestore->collection('vendor_categories')
                ->where('title', '==', trim($categoryName))
                ->limit(1)
                ->documents();

            foreach ($categories as $category) {
                return $category->id();
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        return null;
    }

    /**
     * Resolve vendor ID - try direct ID first, then name lookup
     */
    private function resolveVendorID($vendorInput, $firestore)
    {
        // First try as direct ID
        try {
            $vendorDoc = $firestore->collection('vendors')->document($vendorInput)->snapshot();
            if ($vendorDoc->exists()) {
                return $vendorInput; // Return the ID as-is
            }
        } catch (\Exception $e) {
            // Continue to name lookup
        }

        // If not found as ID, try name lookup
        return $this->findVendorByName($vendorInput, $firestore);
    }

    /**
     * Resolve category ID - try direct ID first, then name lookup
     */
    private function resolveCategoryID($categoryInput, $firestore)
    {
        // First try as direct ID
        try {
            $categoryDoc = $firestore->collection('vendor_categories')->document($categoryInput)->snapshot();
            if ($categoryDoc->exists()) {
                return $categoryInput; // Return the ID as-is
            }
        } catch (\Exception $e) {
            // Continue to name lookup
        }

        // If not found as ID, try name lookup
        return $this->findCategoryByName($categoryInput, $firestore);
    }

    /**
     * Resolve media image from media collection
     */
    private function resolveMediaImage($imageInput, $firestore)
    {
        if (empty($imageInput)) {
            return null;
        }

        try {
            // If input is already a full image_path URL, return it directly
            if (filter_var($imageInput, FILTER_VALIDATE_URL) && strpos($imageInput, 'firebasestorage.googleapis.com') !== false) {
                return [
                    'image_path' => $imageInput,
                    'image_name' => basename(parse_url($imageInput, PHP_URL_PATH)),
                    'name' => 'Direct URL',
                    'slug' => 'direct-url'
                ];
            }

            // Try lookup by image_name, then name, then slug, then image_path
            $mediaData = $this->queryMediaByField($firestore, 'image_name', $imageInput);
            if ($mediaData) {
                return $mediaData;
            }

            $mediaData = $this->queryMediaByField($firestore, 'name', $imageInput);
            if ($mediaData) {
                return $mediaData;
            }

            $mediaData = $this->queryMediaByField($firestore, 'slug', $imageInput);
            if ($mediaData) {
                return $mediaData;
            }

            // Try image_path if it looks like a URL
            if (strpos($imageInput, 'http') === 0) {
                $mediaData = $this->queryMediaByField($firestore, 'image_path', $imageInput);
                if ($mediaData) {
                    return $mediaData;
                }
            }

        } catch (\Exception $e) {
            \Log::warning('Media lookup failed for: ' . $imageInput . ' - ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Query media collection by specific field
     */
    private function queryMediaByField($firestore, $field, $value)
    {
        try {
            $query = $firestore->collection('media')
                ->where($field, '==', $value)
                ->limit(1);

            $documents = $query->documents();

            foreach ($documents as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    return [
                        'image_path' => $data['image_path'] ?? '',
                        'image_name' => $data['image_name'] ?? '',
                        'name' => $data['name'] ?? '',
                        'slug' => $data['slug'] ?? ''
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Media query failed for field '$field' with value '$value': " . $e->getMessage());
        }

        return null;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $spreadsheet = IOFactory::load($request->file('file'));
        $rows = $spreadsheet->getActiveSheet()->toArray();

        if (empty($rows) || count($rows) < 2) {
            return back()->withErrors(['file' => 'The uploaded file is empty or missing data.']);
        }

        $headers = array_map('trim', array_shift($rows));

        // Initialize Firestore client
        $firestore = new FirestoreClient([
            'projectId' => config('firestore.project_id'),
            'keyFilePath' => config('firestore.credentials'),
        ]);

        $collection = $firestore->collection('vendor_products');
        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
            $data = array_combine($headers, $row);

            // Skip empty rows
            if (empty($data['name'])) {
                continue;
            }

            try {
                // Validate required fields
                if (empty($data['name']) || empty($data['price']) || empty($data['vendorID']) || empty($data['categoryID'])) {
                    $errors[] = "Row $rowNumber: Missing required fields (name, price, vendorID, categoryID)";
                    continue;
                }

                // Resolve vendor ID (supports both ID and name)
                $resolvedVendorID = $this->resolveVendorID($data['vendorID'], $firestore);
                if (!$resolvedVendorID) {
                    $errors[] = "Row $rowNumber: Vendor '{$data['vendorID']}' not found (neither as ID nor name)";
                    continue;
                }

                // Resolve category ID (supports both ID and name)
                $resolvedCategoryID = $this->resolveCategoryID($data['categoryID'], $firestore);
                if (!$resolvedCategoryID) {
                    $errors[] = "Row $rowNumber: Category '{$data['categoryID']}' not found (neither as ID nor name)";
                    continue;
                }

                // Resolve photo from media collection
                $resolvedPhoto = '';
                $resolvedPhotos = [];
                if (!empty($data['photo'])) {
                    $mediaData = $this->resolveMediaImage($data['photo'], $firestore);
                    if ($mediaData && !empty($mediaData['image_path'])) {
                        $resolvedPhoto = $mediaData['image_path'];
                        $resolvedPhotos = [$mediaData['image_path']];
                        \Log::info("Food import: Resolved photo for '{$data['name']}' from '{$data['photo']}' to '{$resolvedPhoto}'");
                    } else {
                        \Log::warning("Food import: Could not resolve photo '{$data['photo']}' for food '{$data['name']}'");
                    }
                }

                // Get vendor title for consistency
                $vendorTitle = '';
                try {
                    $vendorDoc = $firestore->collection('vendors')->document($resolvedVendorID)->snapshot();
                    if ($vendorDoc->exists()) {
                        $vendorData = $vendorDoc->data();
                        $vendorTitle = $vendorData['title'] ?? '';
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not fetch vendor title for ID: $resolvedVendorID");
                }

                // Get category title for consistency
                $categoryTitle = '';
                try {
                    $categoryDoc = $firestore->collection('vendor_categories')->document($resolvedCategoryID)->snapshot();
                    if ($categoryDoc->exists()) {
                        $categoryData = $categoryDoc->data();
                        $categoryTitle = $categoryData['title'] ?? '';
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not fetch category title for ID: $resolvedCategoryID");
                }

                // Prepare food data - handling all variations in your document structure
                $foodData = [
                    'name' => trim($data['name']),
                    'price' => $data['price'], // Keep original format (string or number)
                    'description' => trim($data['description'] ?? ''),
                    'vendorID' => $resolvedVendorID,
                    'vendorTitle' => $vendorTitle, // Add vendor title for consistency
                    'categoryID' => $resolvedCategoryID,
                    'categoryTitle' => $categoryTitle, // Add category title for consistency
                    'disPrice' => !empty($data['disPrice']) ? $data['disPrice'] : '', // Keep original format
                    'publish' => strtolower($data['publish'] ?? 'true') === 'true',
                    'nonveg' => strtolower($data['nonveg'] ?? 'false') === 'true',
                    'veg' => strtolower($data['nonveg'] ?? 'false') === 'true' ? false : true, // Opposite of nonveg
                    'isAvailable' => strtolower($data['isAvailable'] ?? 'true') === 'true',
                    'quantity' => -1, // Number format
                    'calories' => 0, // Number format
                    'grams' => 0, // Number format
                    'proteins' => 0, // Number format
                    'fats' => 0, // Number format
                    'photo' => $resolvedPhoto, // Use resolved photo
                    'photos' => $resolvedPhotos, // Use resolved photos array
                    'addOnsTitle' => [], // Array format
                    'addOnsPrice' => [], // Array format
                    'takeawayOption' => false, // Boolean format
                    'product_specification' => null, // NULL format to match your structure
                    'item_attribute' => null, // Null format
                    'migratedBy' => 'excel_import', // String format for new imports
                    'vType' => 'restaurant', // String format for new imports
                    'createdAt' => new \Google\Cloud\Core\Timestamp(new \DateTime()), // Timestamp format
                ];

                // Create document with auto-generated ID
                $docRef = $collection->add($foodData);

                // Set the internal 'id' field to match the Firestore document ID
                $docRef->set(['id' => $docRef->id()], ['merge' => true]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row $rowNumber: " . $e->getMessage();
            }
        }

        if ($imported === 0) {
            return back()->withErrors(['file' => 'No valid rows were found to import.']);
        }

        $message = "Foods imported successfully! ($imported rows)";
        if (!empty($errors)) {
            $message .= " Errors: " . implode('; ', $errors);
        }

        return back()->with('success', $message);
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/foods_import_template.xlsx');
        $templateDir = dirname($filePath);

        // Create template directory if it doesn't exist
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        // Generate template if it doesn't exist
        if (!file_exists($filePath)) {
            $this->generateTemplate($filePath);
        }

        return response()->download($filePath, 'foods_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="foods_import_template.xlsx"'
        ]);
    }

    /**
     * Generate Excel template for food import
     */
    private function generateTemplate($filePath)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = [
                'A1' => 'name',
                'B1' => 'price',
                'C1' => 'description',
                'D1' => 'vendorID',
                'E1' => 'categoryID',
                'F1' => 'disPrice',
                'G1' => 'publish',
                'H1' => 'nonveg',
                'I1' => 'isAvailable',
                'J1' => 'photo'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Add sample data
            $sampleData = [
                'A2' => 'Sample Food Item',
                'B2' => '150.00',
                'C2' => 'This is a sample food item description',
                'D2' => 'Sample Restaurant',
                'E2' => 'Main Course',
                'F2' => '120.00',
                'G2' => 'true',
                'H2' => 'false',
                'I2' => 'true',
                'J2' => 'Sample Food Image'
            ];

            foreach ($sampleData as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Auto-size columns
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Save the file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

        } catch (\Exception $e) {
            throw new \Exception('Failed to generate template: ' . $e->getMessage());
        }
    }

    /**
     * Inline update for food prices - ensures data consistency
     */
    public function inlineUpdate(Request $request, $id)
    {
        try {
            // Initialize Firestore client
            $firestore = new FirestoreClient([
                'projectId' => config('firestore.project_id'),
                'keyFilePath' => config('firestore.credentials'),
            ]);

            $collection = $firestore->collection('vendor_products');
            $document = $collection->document($id);
            $snapshot = $document->snapshot();

            if (!$snapshot->exists()) {
                return response()->json(['success' => false, 'message' => 'Food item not found'], 404);
            }

            $currentData = $snapshot->data();
            $field = $request->input('field');
            $value = $request->input('value');

            // Validate field
            if (!in_array($field, ['price', 'disPrice'])) {
                return response()->json(['success' => false, 'message' => 'Invalid field. Only price and disPrice are allowed.'], 400);
            }

            // Enhanced value validation
            if (!is_numeric($value) || $value < 0) {
                return response()->json(['success' => false, 'message' => 'Invalid price value. Price must be a positive number.'], 400);
            }

            // Additional validation for maximum price (prevent extremely high values)
            if ($value > 999999) {
                return response()->json(['success' => false, 'message' => 'Price cannot exceed 999,999'], 400);
            }

            // Prepare update data with proper data types (matching edit page)
            $updateData = [];

            if ($field === 'price') {
                $updateData[] = ['path' => 'price', 'value' => (string) $value]; // Convert to string like edit page

                // If discount price is higher than new price, reset it
                if (isset($currentData['disPrice']) && !empty($currentData['disPrice']) && (float)$currentData['disPrice'] > (float)$value) {
                    $updateData[] = ['path' => 'disPrice', 'value' => ''];
                }
            } elseif ($field === 'disPrice') {
                // If setting discount price to 0 or empty, remove it
                if ($value == 0 || empty($value)) {
                    $updateData[] = ['path' => 'disPrice', 'value' => ''];
                } else {
                    $updateData[] = ['path' => 'disPrice', 'value' => (string) $value]; // Convert to string like edit page

                    // Validate discount price is not higher than original price
                    if ((float)$value > (float)$currentData['price']) {
                        return response()->json(['success' => false, 'message' => 'Discount price cannot be higher than original price'], 400);
                    }
                }
            }

            // Update the document with proper Firestore format
            $document->update($updateData);

            // Prepare response message
            $message = 'Price updated successfully';
            $hasDiscountReset = false;

            // Check if discount was reset
            foreach ($updateData as $update) {
                if ($update['path'] === 'disPrice' && $update['value'] === '') {
                    $hasDiscountReset = true;
                    break;
                }
            }

            if ($field === 'price' && $hasDiscountReset) {
                $message .= ' (discount price was reset as it was higher than the new price)';
            }

            // Convert updateData back to associative array for response
            $responseData = [];
            foreach ($updateData as $update) {
                $responseData[$update['path']] = $update['value'];
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Food inline update failed', [
                'id' => $id,
                'field' => $request->input('field'),
                'value' => $request->input('value'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Update failed. Please try again or contact support if the problem persists.'
            ], 500);
        }
    }

}
