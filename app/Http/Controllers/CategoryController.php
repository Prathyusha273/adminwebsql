<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\VendorCategory;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

	  public function index()
    {
        return view("categories.index");

    }

     public function edit($id)
    {
        $category = VendorCategory::find($id);
        
        // If not found in vendor_categories, check if it's a mart category
        if (!$category) {
            \Log::warning("Category not found in vendor_categories: $id");
            
            // Check if this is a mart category
            $martCategory = DB::table('mart_categories')->where('id', $id)->first();
            if ($martCategory) {
                \Log::info("Found in mart_categories, redirecting...");
                // Redirect to mart-categories edit page
                return redirect()->route('mart-categories.edit', $id)
                    ->with('message', 'This is a Mart Category. Redirected to correct page.');
            }
            
            // Not found in either table
            abort(404, 'Category not found in either vendor_categories or mart_categories');
        }
        
        return view('categories.edit', ['id' => $id, 'category' => $category]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function data(Request $request)
    {
        $userPermissions = json_decode(@session('user_permissions'), true) ?: [];
        $canDelete = in_array('category.delete', $userPermissions);

        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = strtolower((string) data_get($request->input('search'), 'value', ''));
        $order = $request->input('order.0', ['column' => 0, 'dir' => 'asc']);
        $orderColumnIndex = (int) data_get($order, 'column', 0);
        $orderDir = data_get($order, 'dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $orderableColumns = $canDelete ? ['', 'title', 'totalProducts', '', ''] : ['title', 'totalProducts', '', ''];
        $orderBy = $orderableColumns[$orderColumnIndex] ?? 'title';

        $baseQuery = DB::table('vendor_categories');
        $total = $baseQuery->count();

        $filteredQuery = DB::table('vendor_categories');
        if ($search !== '') {
            $filteredQuery->where(function($q) use ($search){
                $q->where('title','like','%'.$search.'%')
                  ->orWhere('description','like','%'.$search.'%');
            });
        }

        if (in_array($orderBy, ['title'])) {
            $filteredQuery->orderBy($orderBy, $orderDir);
        } else {
            $filteredQuery->orderBy('title','asc');
        }

        $pageRows = $filteredQuery->offset($start)->limit($length)->get();
        $filtered = ($search==='') ? $total : (clone $filteredQuery)->count();

        $placeholder = asset('images/placeholder-image.png');
        $data = [];
        foreach ($pageRows as $row) {
            // Count foods for this category (supports exact and JSON-like storage)
            $foodsCount = DB::table('vendor_products')
                ->where(function($qq) use ($row){
                    $qq->where('categoryID', $row->id)
                       ->orWhere('categoryID','like','%"'.$row->id.'"%')
                       ->orWhere('categoryID','like','%'.$row->id.'%');
                })
                ->count();
            $imageHtml = '<img alt="" width="100%" style="width:70px;height:70px;" src="'.($row->photo ?: $placeholder).'" onerror="this.onerror=null;this.src=\''.$placeholder.'\'" alt="image">';
            $editUrl = route('categories.edit', $row->id);
            $titleHtml = $imageHtml.'<a href="'.$editUrl.'">'.e($row->title).'</a>';
            $totalProductsLink = '<a href="'.url('foods?categoryID='.$row->id).'">'.$foodsCount.'</a>';
            $publishHtml = $row->publish ? '<label class="switch"><input type="checkbox" checked data-id="'.$row->id.'" class="toggle-publish"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" data-id="'.$row->id.'" class="toggle-publish"><span class="slider round"></span></label>';
            $actionsHtml = '<span class="action-btn"><a href="'.$editUrl.'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>'.($canDelete ? ' <a href="'.route('categories.delete',$row->id).'" class="delete-btn"><i class="mdi mdi-delete"></i></a>' : '').'</span>';

            $rowArr = $canDelete ? [
                '<td class="delete-all"><input type="checkbox" class="is_open" dataId="'.$row->id.'"><label class="col-3 control-label"></label></td>',
                $titleHtml,
                $totalProductsLink,
                $publishHtml,
                $actionsHtml,
            ] : [
                $titleHtml,
                $totalProductsLink,
                $publishHtml,
                $actionsHtml,
            ];
            $data[] = $rowArr;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'nullable|image',
        ]);

        $id = (string) Str::uuid();
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('public/uploads/categories');
            $photoUrl = Storage::url($photoUrl);
        }

        VendorCategory::create([
            'id' => $id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'photo' => $photoUrl,
            'review_attributes' => json_encode($request->input('review_attributes', [])),
            'publish' => $request->boolean('item_publish') || $request->boolean('publish') ? 1 : 0,
            'show_in_homepage' => $request->boolean('show_in_homepage') ? 1 : 0,
        ]);

        return response()->json(['success' => true, 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $category = VendorCategory::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'nullable|image',
        ]);

        $photoUrl = $category->photo;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('public/uploads/categories');
            $photoUrl = Storage::url($photoUrl);
        }

        $category->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'photo' => $photoUrl,
            'review_attributes' => json_encode($request->input('review_attributes', [])),
            'publish' => $request->boolean('item_publish') || $request->boolean('publish') ? 1 : 0,
            'show_in_homepage' => $request->boolean('show_in_homepage') ? 1 : 0,
        ]);

        return response()->json(['success' => true]);
    }

    public function togglePublish(Request $request, $id)
    {
        $category = VendorCategory::findOrFail($id);
        $category->publish = $request->boolean('publish') ? 1 : 0;
        $category->save();
        return response()->json(['success' => true]);
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
        foreach ($rows as $row) {
            $data = array_combine($headers, $row);
            if (empty($data['title'])) {
                continue;
            }
            VendorCategory::create([
                'id' => (string) Str::uuid(),
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'photo' => $data['photo'] ?? '',
                'publish' => strtolower($data['publish'] ?? '') === 'true' ? 1 : 0,
                'show_in_homepage' => strtolower($data['show_in_homepage'] ?? '') === 'true' ? 1 : 0,
                'restaurant_id' => $data['restaurant_id'] ?? '',
                'review_attributes' => $data['review_attributes'] ?? '',
                'migratedBy' => 'migrate:categories',
            ]);
            $imported++;
        }
        if ($imported === 0) {
            return back()->withErrors(['file' => 'No valid rows were found to import.']);
        }
        return back()->with('success', "Categories imported successfully! ($imported rows)");
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/categories_import_template.xlsx');
        $templateDir = dirname($filePath);

        // Create template directory if it doesn't exist
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        // Generate template if it doesn't exist
        if (!file_exists($filePath)) {
            $this->generateTemplate($filePath);
        }

        return response()->download($filePath, 'categories_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="categories_import_template.xlsx"'
        ]);
    }

    public function delete($id)
    {
        try {
            $cat = VendorCategory::find($id);
            if(!$cat){
                return redirect()->back()->with('error','Category not found.');
            }
            $cat->delete();
            return redirect()->back()->with('success','Category deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error deleting category: '.$e->getMessage());
        }
    }

    /**
     * Generate Excel template for category import
     */
    private function generateTemplate($filePath)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = [
                'A1' => 'title',
                'B1' => 'description',
                'C1' => 'photo',
                'D1' => 'publish',
                'E1' => 'show_in_homepage',
                'F1' => 'restaurant_id',
                'G1' => 'review_attributes'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            // Add sample data
            $sampleData = [
                'Fast Food',
                'Quick and delicious meals',
                'https://example.com/images/fast-food.jpg',
                'true',
                'true',
                '',
                'Taste,Quality,Service'
            ];

            $sheet->fromArray([$sampleData], null, 'A2');

            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Save the file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

        } catch (\Exception $e) {
            \Log::error('Failed to generate categories template: ' . $e->getMessage());
            abort(500, 'Failed to generate template');
        }
    }
}


