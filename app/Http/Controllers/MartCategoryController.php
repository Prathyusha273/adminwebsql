<?php

namespace App\Http\Controllers;

use App\Models\MartCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MartCategoryController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view("martCategories.index");
    }

    public function edit($id)
    {
        return view('martCategories.edit')->with('id', $id);
    }

    public function create()
    {
        return view('martCategories.create');
    }

    /**
     * Get all mart categories for DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        \Log::info('=== Mart Categories getData called ===');
        \Log::info('Request params:', $request->all());
        
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = strtolower($request->input('search.value', ''));
        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderDirection = $request->input('order.0.dir', 'asc');
        
        \Log::info("Parsed - start: $start, length: $length, search: $searchValue");
        
        $user_permissions = json_decode(session('user_permissions') ?? '[]', true);
        $checkDeletePermission = is_array($user_permissions) && in_array('mart-categories.delete', $user_permissions);
        
        $orderableColumns = $checkDeletePermission 
            ? ['', 'title', 'section', 'subcategories_count', 'totalProducts', '', ''] 
            : ['title', 'section', 'subcategories_count', 'totalProducts', '', ''];
        
        $orderByField = $orderableColumns[$orderColumnIndex] ?? 'title';
        
        // Build query
        $query = MartCategory::query();
        
        // Apply search filter
        if (!empty($searchValue) && strlen($searchValue) >= 3) {
            $query->where(function($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                  ->orWhere('section', 'like', "%{$searchValue}%")
                  ->orWhere('subcategories_count', 'like', "%{$searchValue}%");
            });
        }
        
        // Get total count
        $totalRecords = $query->count();
        
        \Log::info("Total records found: $totalRecords");
        
        // Apply ordering
        if (!empty($orderByField) && $orderByField !== '') {
            $query->orderBy($orderByField, $orderDirection);
        }
        
        // Apply pagination
        $categories = $query->skip($start)->take($length)->get();
        
        \Log::info("Categories retrieved: " . $categories->count());
        
        // Get subcategory counts and mart items counts
        $records = [];
        foreach ($categories as $category) {
            // Get mart items count
            $totalProducts = DB::table('mart_items')
                ->where('categoryID', $category->id)
                ->count();
            
            $records[] = [
                'id' => $category->id,
                'title' => $category->title,
                'description' => $category->description ?? '',
                'photo' => $category->photo ?? '',
                'section' => $category->section ?? 'General',
                'category_order' => $category->category_order ?? 1,
                'publish' => $category->publish ? true : false,
                'show_in_homepage' => $category->show_in_homepage ? true : false,
                'subcategories_count' => $category->subcategories_count ?? 0,
                'has_subcategories' => $category->has_subcategories ? true : false,
                'totalProducts' => $totalProducts,
                'review_attributes' => $category->review_attributes ? json_decode($category->review_attributes, true) : []
            ];
        }
        
        \Log::info("Returning " . count($records) . " records");
        
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $records
        ]);
    }

    /**
     * Get single mart category by ID
     */
    public function getCategory($id)
    {
        $category = MartCategory::find($id);
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        
        return response()->json([
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description ?? '',
            'photo' => $category->photo ?? '',
            'section' => $category->section ?? 'General',
            'category_order' => $category->category_order ?? 1,
            'section_order' => $category->section_order ?? 1,
            'publish' => $category->publish ? true : false,
            'show_in_homepage' => $category->show_in_homepage ? true : false,
            'review_attributes' => $category->review_attributes ? json_decode($category->review_attributes, true) : [],
            'mart_id' => $category->mart_id ?? '',
            'has_subcategories' => $category->has_subcategories ? true : false,
            'subcategories_count' => $category->subcategories_count ?? 0
        ]);
    }

    /**
     * Store new mart category
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'nullable|string',
            'description' => 'nullable|string',
            'section' => 'nullable|string',
            'category_order' => 'nullable|integer',
            'publish' => 'nullable|in:0,1,true,false',
            'show_in_homepage' => 'nullable|in:0,1,true,false',
            'review_attributes' => 'nullable|array'
        ]);

        // Check if already 5 categories with show_in_homepage
        if ($request->input('show_in_homepage')) {
            $count = MartCategory::where('show_in_homepage', true)->count();
            if ($count >= 5) {
                return response()->json(['error' => 'Already 5 mart categories are active for show in homepage'], 400);
            }
        }

        $id = uniqid();
        
        $category = MartCategory::create([
            'id' => $id,
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'photo' => $request->input('photo', ''),
            'section' => $request->input('section', 'General'),
            'category_order' => $request->input('category_order', 1),
            'section_order' => $request->input('category_order', 1),
            'publish' => filter_var($request->input('publish', false), FILTER_VALIDATE_BOOLEAN),
            'show_in_homepage' => filter_var($request->input('show_in_homepage', false), FILTER_VALIDATE_BOOLEAN),
            'review_attributes' => json_encode($request->input('review_attributes', [])),
            'has_subcategories' => false,
            'subcategories_count' => 0,
            'mart_id' => ''
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mart category created successfully',
            'id' => $category->id
        ]);
    }

    /**
     * Update existing mart category
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'nullable|string',
            'description' => 'nullable|string',
            'section' => 'nullable|string',
            'category_order' => 'nullable|integer',
            'publish' => 'nullable|in:0,1,true,false',
            'show_in_homepage' => 'nullable|in:0,1,true,false',
            'review_attributes' => 'nullable|array'
        ]);

        $category = MartCategory::find($id);
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Check if already 5 categories with show_in_homepage (excluding current)
        if ($request->input('show_in_homepage')) {
            $count = MartCategory::where('show_in_homepage', true)
                ->where('id', '!=', $id)
                ->count();
            if ($count >= 5) {
                return response()->json(['error' => 'Already 5 mart categories are active for show in homepage'], 400);
            }
        }

        $category->update([
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'photo' => $request->input('photo', ''),
            'section' => $request->input('section', 'General'),
            'category_order' => $request->input('category_order', 1),
            'section_order' => $request->input('category_order', 1),
            'publish' => filter_var($request->input('publish', false), FILTER_VALIDATE_BOOLEAN),
            'show_in_homepage' => filter_var($request->input('show_in_homepage', false), FILTER_VALIDATE_BOOLEAN),
            'review_attributes' => json_encode($request->input('review_attributes', []))
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mart category updated successfully'
        ]);
    }

    /**
     * Delete mart category
     */
    public function destroy($id)
    {
        $category = MartCategory::find($id);
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mart category deleted successfully'
        ]);
    }

    /**
     * Delete multiple categories
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['error' => 'No categories selected'], 400);
        }

        MartCategory::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categories deleted successfully'
        ]);
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $id)
    {
        $category = MartCategory::find($id);
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->update([
            'publish' => filter_var($request->input('publish', false), FILTER_VALIDATE_BOOLEAN)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Publish status updated successfully'
        ]);
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
            
            // Process review attributes
            $reviewAttributes = [];
            if (!empty($data['review_attributes'])) {
                $reviewAttributeInputs = array_filter(array_map('trim', explode(',', $data['review_attributes'])));
                foreach ($reviewAttributeInputs as $input) {
                    $reviewAttributes[] = $input;
                }
            }
            
            // Create mart category
            MartCategory::create([
                'id' => uniqid(),
                'title' => trim($data['title']),
                'description' => trim($data['description'] ?? ''),
                'photo' => $data['photo'] ?? '',
                'section' => $data['section'] ?? 'General',
                'category_order' => intval($data['category_order'] ?? 1),
                'section_order' => intval($data['category_order'] ?? 1),
                'publish' => strtolower($data['publish'] ?? 'false') === 'true',
                'show_in_homepage' => strtolower($data['show_in_homepage'] ?? 'false') === 'true',
                'mart_id' => $data['mart_id'] ?? '',
                'has_subcategories' => false,
                'subcategories_count' => 0,
                'review_attributes' => json_encode($reviewAttributes),
                'migratedBy' => 'bulk_import',
            ]);
            
            $imported++;
        }
        
        if ($imported === 0) {
            return back()->withErrors(['file' => 'No valid rows were found to import.']);
        }
        
        $message = "Mart Categories imported successfully! ($imported rows)";
        if (!empty($errors)) {
            $message .= "\n\nWarnings:\n" . implode("\n", $errors);
        }
        
        return back()->with('success', $message);
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/mart_categories_import_template.xlsx');

        // Create template directory if it doesn't exist
        $templateDir = dirname($filePath);
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        // Generate template if it doesn't exist
        if (!file_exists($filePath)) {
            $this->generateTemplate($filePath);
        }
        
        return response()->download($filePath, 'mart_categories_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="mart_categories_import_template.xlsx"'
        ]);
    }

    /**
     * Generate Excel template for mart categories import
     */
    private function generateTemplate($filePath)
    {
        try {
            // Create new spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            // Remove default worksheet and create a new one
            $spreadsheet->removeSheetByIndex(0);
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Mart Categories Import');
            
            // Set headers with proper formatting
            $headers = [
                'A1' => 'title',
                'B1' => 'description',
                'C1' => 'photo',
                'D1' => 'section',
                'E1' => 'category_order',
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
                'A2' => 'Sample Category 1',
                'B2' => 'This is a sample category description',
                'C2' => 'https://example.com/image.jpg',
                'D2' => 'Essentials & Daily Needs',
                'E2' => '1',
                'F2' => 'true',
                'G2' => 'true',
                'H2' => '',
                'I2' => 'quality,value,service',
                'A3' => 'Sample Category 2',
                'B3' => 'Another sample category description',
                'C3' => 'https://example.com/image2.jpg',
                'D3' => 'Health & Wellness',
                'E3' => '2',
                'F3' => 'false',
                'G3' => 'false',
                'H3' => '',
                'I3' => 'freshness,organic'
            ];

            foreach ($sampleData as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(15);
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
