<?php

namespace App\Http\Controllers;

use App\Models\MartSubcategory;
use App\Models\MartCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MartSubcategoryController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display sub-categories for a specific parent category
     */
    public function index($categoryId)
    {
        return view("martSubcategories.index")->with('categoryId', $categoryId);
    }

    /**
     * Show the form for creating a new sub-category
     */
    public function create($categoryId)
    {
        return view('martSubcategories.create')->with('categoryId', $categoryId);
    }

    /**
     * Show the form for editing a sub-category
     */
    public function edit($id)
    {
        return view('martSubcategories.edit')->with('id', $id);
    }

    /**
     * Get all sub-categories for a specific parent category (AJAX)
     */
    public function getData(Request $request, $categoryId)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = strtolower($request->input('search.value', ''));
        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderDirection = $request->input('order.0.dir', 'asc');
        
        $user_permissions = json_decode(session('user_permissions'), true) ?? [];
        $checkDeletePermission = in_array('mart-subcategories.delete', $user_permissions);
        
        $orderableColumns = $checkDeletePermission 
            ? ['', 'title', 'subcategory_order', 'totalProducts', '', ''] 
            : ['title', 'subcategory_order', 'totalProducts', '', ''];
        
        $orderByField = $orderableColumns[$orderColumnIndex] ?? 'title';
        
        // Build query
        $query = MartSubcategory::where('parent_category_id', $categoryId);
        
        // Apply search filter
        if (!empty($searchValue) && strlen($searchValue) >= 3) {
            $query->where(function($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%")
                  ->orWhere('subcategory_order', 'like', "%{$searchValue}%");
            });
        }
        
        // Get total count
        $totalRecords = $query->count();
        
        // Apply ordering
        if (!empty($orderByField) && $orderByField !== '') {
            $query->orderBy($orderByField, $orderDirection);
        }
        
        // Apply pagination
        $subcategories = $query->skip($start)->take($length)->get();
        
        // Get mart items counts
        $records = [];
        foreach ($subcategories as $subcategory) {
            // Get mart items count
            $totalProducts = DB::table('mart_items')
                ->where('subcategoryID', $subcategory->id)
                ->count();
            
            $records[] = [
                'id' => $subcategory->id,
                'title' => $subcategory->title,
                'description' => $subcategory->description ?? '',
                'photo' => $subcategory->photo ?? '',
                'subcategory_order' => $subcategory->subcategory_order ?? 1,
                'publish' => $subcategory->publish ? true : false,
                'show_in_homepage' => $subcategory->show_in_homepage ? true : false,
                'totalProducts' => $totalProducts,
                'review_attributes' => $subcategory->review_attributes ? json_decode($subcategory->review_attributes, true) : [],
                'parent_category_id' => $subcategory->parent_category_id,
                'parent_category_title' => $subcategory->parent_category_title,
                'section' => $subcategory->section
            ];
        }
        
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $records
        ]);
    }

    /**
     * Get single sub-category by ID
     */
    public function getSubcategory($id)
    {
        $subcategory = MartSubcategory::find($id);
        
        if (!$subcategory) {
            return response()->json(['error' => 'Sub-category not found'], 404);
        }
        
        return response()->json([
            'id' => $subcategory->id,
            'title' => $subcategory->title,
            'description' => $subcategory->description ?? '',
            'photo' => $subcategory->photo ?? '',
            'subcategory_order' => $subcategory->subcategory_order ?? 1,
            'publish' => $subcategory->publish ? true : false,
            'show_in_homepage' => $subcategory->show_in_homepage ? true : false,
            'review_attributes' => $subcategory->review_attributes ? json_decode($subcategory->review_attributes, true) : [],
            'parent_category_id' => $subcategory->parent_category_id,
            'parent_category_title' => $subcategory->parent_category_title,
            'section' => $subcategory->section,
            'section_order' => $subcategory->section_order,
            'category_order' => $subcategory->category_order,
            'mart_id' => $subcategory->mart_id
        ]);
    }

    /**
     * Get parent category info
     */
    public function getParentCategory($categoryId)
    {
        $category = MartCategory::find($categoryId);
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        
        return response()->json([
            'id' => $category->id,
            'title' => $category->title,
            'section' => $category->section ?? 'General'
        ]);
    }

    /**
     * Store new sub-category
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'nullable|string',
            'description' => 'nullable|string',
            'parent_category_id' => 'required|string',
            'subcategory_order' => 'nullable|integer',
            'publish' => 'nullable|in:0,1,true,false',
            'show_in_homepage' => 'nullable|in:0,1,true,false',
            'review_attributes' => 'nullable|array'
        ]);

        // Get parent category info
        $parentCategory = MartCategory::find($request->input('parent_category_id'));
        if (!$parentCategory) {
            return response()->json(['error' => 'Parent category not found'], 404);
        }

        $id = uniqid();
        
        $subcategory = MartSubcategory::create([
            'id' => $id,
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'photo' => $request->input('photo', ''),
            'parent_category_id' => $request->input('parent_category_id'),
            'parent_category_title' => $parentCategory->title,
            'section' => $parentCategory->section ?? 'General',
            'section_order' => 1,
            'category_order' => 1,
            'subcategory_order' => $request->input('subcategory_order', 1),
            'publish' => filter_var($request->input('publish', false), FILTER_VALIDATE_BOOLEAN),
            'show_in_homepage' => filter_var($request->input('show_in_homepage', false), FILTER_VALIDATE_BOOLEAN),
            'review_attributes' => json_encode($request->input('review_attributes', [])),
            'mart_id' => ''
        ]);

        // Update parent category subcategory count
        $this->updateParentCategoryCount($request->input('parent_category_id'));

        return response()->json([
            'success' => true,
            'message' => 'Mart sub-category created successfully',
            'id' => $subcategory->id
        ]);
    }

    /**
     * Update existing sub-category
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'nullable|string',
            'description' => 'nullable|string',
            'subcategory_order' => 'nullable|integer',
            'publish' => 'nullable|in:0,1,true,false',
            'show_in_homepage' => 'nullable|in:0,1,true,false',
            'review_attributes' => 'nullable|array'
        ]);

        $subcategory = MartSubcategory::find($id);
        
        if (!$subcategory) {
            return response()->json(['error' => 'Sub-category not found'], 404);
        }

        $subcategory->update([
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'photo' => $request->input('photo', ''),
            'subcategory_order' => $request->input('subcategory_order', 1),
            'publish' => filter_var($request->input('publish', false), FILTER_VALIDATE_BOOLEAN),
            'show_in_homepage' => filter_var($request->input('show_in_homepage', false), FILTER_VALIDATE_BOOLEAN),
            'review_attributes' => json_encode($request->input('review_attributes', []))
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mart sub-category updated successfully'
        ]);
    }

    /**
     * Delete sub-category
     */
    public function destroy($id)
    {
        $subcategory = MartSubcategory::find($id);
        
        if (!$subcategory) {
            return response()->json(['error' => 'Sub-category not found'], 404);
        }

        $parentCategoryId = $subcategory->parent_category_id;
        $subcategory->delete();

        // Update parent category count
        $this->updateParentCategoryCount($parentCategoryId);

        return response()->json([
            'success' => true,
            'message' => 'Mart sub-category deleted successfully'
        ]);
    }

    /**
     * Delete multiple sub-categories
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['error' => 'No sub-categories selected'], 400);
        }

        // Get unique parent category IDs before deletion
        $parentCategoryIds = MartSubcategory::whereIn('id', $ids)
            ->pluck('parent_category_id')
            ->unique()
            ->toArray();

        MartSubcategory::whereIn('id', $ids)->delete();

        // Update all affected parent categories
        foreach ($parentCategoryIds as $parentId) {
            $this->updateParentCategoryCount($parentId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sub-categories deleted successfully'
        ]);
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $id)
    {
        $subcategory = MartSubcategory::find($id);
        
        if (!$subcategory) {
            return response()->json(['error' => 'Sub-category not found'], 404);
        }

        $subcategory->update([
            'publish' => filter_var($request->input('publish', false), FILTER_VALIDATE_BOOLEAN)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Publish status updated successfully'
        ]);
    }

    /**
     * Update parent category subcategory count
     */
    private function updateParentCategoryCount($parentCategoryId)
    {
        $count = MartSubcategory::where('parent_category_id', $parentCategoryId)->count();
        
        MartCategory::where('id', $parentCategoryId)->update([
            'subcategories_count' => $count,
            'has_subcategories' => $count > 0
        ]);
    }

    /**
     * Bulk import sub-categories from Excel file
     */
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
        
        $imported = 0;
        $errors = [];
        
        foreach ($rows as $index => $row) {
            $data = array_combine($headers, $row);
            $rowNumber = $index + 2;
            
            // Validate required fields
            if (empty($data['title'])) {
                $errors[] = "Row $rowNumber: Title is required";
                continue;
            }
            
            // Process parent category
            $parentCategoryId = $this->resolveParentCategoryId($data['parent_category_id'] ?? '');
            if (!$parentCategoryId) {
                $errors[] = "Row $rowNumber: Parent category '{$data['parent_category_id']}' not found";
                continue;
            }
            
            // Get parent category info
            $parentCategory = MartCategory::find($parentCategoryId);
            if (!$parentCategory) {
                $errors[] = "Row $rowNumber: Parent category data not found";
                continue;
            }
            
            // Process review attributes
            $reviewAttributes = [];
            if (!empty($data['review_attributes'])) {
                $reviewAttributeInputs = array_filter(array_map('trim', explode(',', $data['review_attributes'])));
                foreach ($reviewAttributeInputs as $input) {
                    $reviewAttributes[] = $input;
                }
            }
            
            // Create sub-category
            MartSubcategory::create([
                'id' => uniqid(),
                'title' => trim($data['title']),
                'description' => trim($data['description'] ?? ''),
                'photo' => $data['photo'] ?? '',
                'parent_category_id' => $parentCategoryId,
                'parent_category_title' => $parentCategory->title,
                'section' => $parentCategory->section ?? 'General',
                'section_order' => 1,
                'category_order' => 1,
                'subcategory_order' => intval($data['subcategory_order'] ?? 1),
                'mart_id' => $data['mart_id'] ?? '',
                'review_attributes' => json_encode($reviewAttributes),
                'publish' => strtolower($data['publish'] ?? 'false') === 'true',
                'show_in_homepage' => strtolower($data['show_in_homepage'] ?? 'false') === 'true',
                'migratedBy' => 'bulk_import',
            ]);
            
            // Update parent category count
            $this->updateParentCategoryCount($parentCategoryId);
            
            $imported++;
        }
        
        if ($imported === 0) {
            return back()->withErrors(['file' => 'No valid rows were found to import.']);
        }
        
        $message = "Mart Sub-Categories imported successfully! ($imported rows)";
        if (!empty($errors)) {
            $message .= "\n\nWarnings:\n" . implode("\n", $errors);
        }
        
        return back()->with('success', $message);
    }

    /**
     * Download import template for sub-categories
     */
    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/mart_subcategories_import_template.xlsx');

        // Create template directory if it doesn't exist
        $templateDir = dirname($filePath);
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        // Generate template if it doesn't exist
        if (!file_exists($filePath)) {
            $this->generateTemplate($filePath);
        }
        
        return response()->download($filePath, 'mart_subcategories_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="mart_subcategories_import_template.xlsx"'
        ]);
    }

    /**
     * Resolve parent category ID from input (can be ID or name)
     */
    private function resolveParentCategoryId($input)
    {
        if (empty($input)) {
            return null;
        }

        // First try as direct ID
        $category = MartCategory::find($input);
        if ($category) {
            return $input;
        }

        // If not found as ID, try name lookup
        $category = MartCategory::where('title', trim($input))->first();
        if ($category) {
            return $category->id;
        }

        return null;
    }

    /**
     * Generate Excel template for mart sub-categories import
     */
    private function generateTemplate($filePath)
    {
        try {
            // Create new spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            // Remove default worksheet and create a new one
            $spreadsheet->removeSheetByIndex(0);
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Mart Sub-Categories Import');
            
            // Set headers
            $headers = [
                'A1' => 'title',
                'B1' => 'description',
                'C1' => 'photo',
                'D1' => 'subcategory_order',
                'E1' => 'parent_category_id',
                'F1' => 'publish',
                'G1' => 'show_in_homepage',
                'H1' => 'mart_id',
                'I1' => 'review_attributes'
            ];

            // Set header values with bold formatting
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            // Add sample data rows
            $sampleData = [
                'A2' => 'Sample Sub-Category 1',
                'B2' => 'Sample description for sub-category 1',
                'C2' => 'https://example.com/image.jpg',
                'D2' => '1',
                'E2' => 'Groceries',
                'F2' => 'true',
                'G2' => 'false',
                'H2' => '',
                'I2' => 'quality,freshness',
                'A3' => 'Sample Sub-Category 2',
                'B3' => 'Sample description for sub-category 2',
                'C3' => 'https://example.com/image2.jpg',
                'D3' => '2',
                'E3' => 'Medicine',
                'F3' => 'true',
                'G3' => 'false',
                'H3' => '',
                'I3' => 'quality,freshness'
            ];

            foreach ($sampleData as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(25);
            $sheet->getColumnDimension('F')->setWidth(10);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(25);

            // Add borders to header row
            $sheet->getStyle('A1:I1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->setIncludeCharts(false);
            
            // Ensure directory exists
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Save the file
            $writer->save($filePath);
            
            // Verify file was created
            if (!file_exists($filePath) || filesize($filePath) < 1000) {
                throw new \Exception('Generated file is too small or corrupted');
            }

        } catch (\Exception $e) {
            // Clean up any partial file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            throw new \Exception('Failed to generate template: ' . $e->getMessage());
        }
    }
}
