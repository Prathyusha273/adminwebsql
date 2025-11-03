<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;
use App\Models\AppUser;
use App\Models\Vendor;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

	  public function index()
    {

        return view("restaurants.index");
    }

    public function vendors()
    {
        return view("vendors.index");
    }


    public function edit($id)
    {
    	    return view('restaurants.edit')->with('id',$id);
    }

    public function vendorEdit($id)
    {
    	    return view('vendors.edit')->with('id',$id);
    }

    public function vendorSubscriptionPlanHistory($id='')
    {
    	    return view('subscription_plans.history')->with('id',$id);
    }

    public function view($id)
    {
        return view('restaurants.view')->with('id',$id);
    }

    public function plan($id)
    {

        return view("restaurants.plan")->with('id',$id);
    }

    public function payout($id)
    {
        return view('restaurants.payout')->with('id',$id);
    }

    public function foods($id)
    {
        return view('restaurants.foods')->with('id',$id);
    }

    public function orders($id)
    {
        return view('restaurants.orders')->with('id',$id);
    }

    public function reviews($id)
    {
        return view('restaurants.reviews')->with('id',$id);
    }

    public function promos($id)
    {
        return view('restaurants.promos')->with('id',$id);
    }

    public function vendorCreate(){
        return view('vendors.create');
    }

    public function create(){
        return view('restaurants.create');
    }

    public function DocumentList($id){
        return view("vendors.document_list")->with('id',$id);
    }

    public function DocumentUpload($vendorId, $id)
    {
        return view("vendors.document_upload", compact('vendorId', 'id'));
    }
    public function currentSubscriberList($id)
    {
        return view("subscription_plans.current_subscriber", compact( 'id'));
    }

    public function importVendors(Request $request)
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
        $firestore = new FirestoreClient([
            'projectId' => config('firestore.project_id'),
            'keyFilePath' => config('firestore.credentials'),
        ]);
        $collection = $firestore->collection('users');
        $zoneCollection = $firestore->collection('zone');
        $imported = 0;
        $errors = [];
        foreach ($rows as $rowIndex => $row) {
            $rowNum = $rowIndex + 2; // Excel row number
            $data = array_combine($headers, $row);
            // Required fields
            if (empty($data['firstName']) || empty($data['lastName']) || empty($data['email']) || empty($data['password'])) {
                $errors[] = "Row $rowNum: Missing required fields.";
                continue;
            }
            // Email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row $rowNum: Invalid email format.";
                continue;
            }
            // Duplicate email check
            $existing = $collection->where('email', '=', $data['email'])->limit(1)->documents();
            if (!$existing->isEmpty()) {
                $errors[] = "Row $rowNum: Email already exists.";
                continue;
            }
            // Phone number (basic)
            if (!empty($data['phoneNumber']) && !preg_match('/^[+0-9\- ]{7,20}$/', $data['phoneNumber'])) {
                $errors[] = "Row $rowNum: Invalid phone number format.";
                continue;
            }
            // zone name to zoneId lookup
            $zoneId = '';
            if (!empty($data['zone'])) {
                $zoneDocs = $zoneCollection->where('name', '=', $data['zone'])->limit(1)->documents();
                if ($zoneDocs->isEmpty()) {
                    $errors[] = "Row $rowNum: zone '{$data['zone']}' does not exist.";
                    continue;
                } else {
                    $zoneId = $zoneDocs->rows()[0]['id'];
                }
            }
            $vendorData = [
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'active' => strtolower($data['active'] ?? '') === 'true',
                'role' => 'vendor',
                'profilePictureURL' => $data['profilePictureURL'] ?? '',
                'zoneId' => $zoneId,
                'phoneNumber' => $data['phoneNumber'] ?? '',
                'migratedBy' => 'migrate:vendors',
            ];
            if (!empty($data['createdAt'])) {
                try {
                    $vendorData['createdAt'] = new \Google\Cloud\Core\Timestamp(Carbon::parse($data['createdAt']));
                } catch (\Exception $e) {
                    $vendorData['createdAt'] = new \Google\Cloud\Core\Timestamp(now());
                }
            } else {
                $vendorData['createdAt'] = new \Google\Cloud\Core\Timestamp(now());
            }
            $docRef = $collection->add($vendorData);
            $docRef->set(['id' => $docRef->id()], ['merge' => true]);
            $imported++;
            // Send welcome email
            try {
                Mail::to($data['email'])->send(new DynamicEmail([
                    'subject' => 'Welcome to JippyMart!',
                    'body' => "Hi {$data['firstName']},<br><br>Welcome to JippyMart! Your account has been created.<br><br>Email: {$data['email']}<br>Password: (the password you provided)<br><br>Login at: <a href='" . url('/') . "'>JippyMart Admin</a><br><br>Thank you!"
                ]));
            } catch (\Exception $e) {
                $errors[] = "Row $rowNum: Failed to send email (" . $e->getMessage() . ")";
            }
        }
        $msg = "Vendors imported successfully! ($imported rows)";
        if (!empty($errors)) {
            $msg .= "<br>Some issues occurred:<br>" . implode('<br>', $errors);
        }
        if ($imported === 0) {
            return back()->withErrors(['file' => $msg]);
        }
        return back()->with('success', $msg);
    }

    public function downloadVendorsTemplate()
    {
        $filePath = storage_path('app/templates/vendors_import_template.xlsx');
        $templateDir = dirname($filePath);
        
        // Create template directory if it doesn't exist
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }
        
        // Generate template if it doesn't exist
        if (!file_exists($filePath)) {
            $this->generateVendorsTemplate($filePath);
        }
        
        return response()->download($filePath, 'vendors_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="vendors_import_template.xlsx"'
        ]);
    }

    /**
     * Generate Excel template for vendors import
     */
    private function generateVendorsTemplate($filePath)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $headers = [
                'A1' => 'firstName',
                'B1' => 'lastName',
                'C1' => 'email',
                'D1' => 'password',
                'E1' => 'phoneNumber',
                'F1' => 'countryCode',
                'G1' => 'profilePictureURL',
                'H1' => 'zoneId',
                'I1' => 'active'
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }
            
            // Add sample data
            $sampleData = [
                'Restaurant',
                'Owner',
                'owner@restaurant.com',
                'password123',
                '1234567890',
                '+1',
                'https://example.com/profile.jpg',
                'zone_id_123',
                'true'
            ];
            
            $sheet->fromArray([$sampleData], null, 'A2');
            
            // Auto-size columns
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Save the file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);
            
        } catch (\Exception $e) {
            \Log::error('Failed to generate vendors template: ' . $e->getMessage());
            abort(500, 'Failed to generate template');
        }
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        
        // Get options for handling invalid rows
        $skipInvalidRows = $request->input('skip_invalid_rows', false);
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file'));
        $rows = $spreadsheet->getActiveSheet()->toArray();
        
        if (empty($rows) || count($rows) < 2) {
            return back()->withErrors(['file' => 'The uploaded file is empty or missing data.']);
        }
        
        $headers = array_map('trim', array_shift($rows));
        
        // Validate headers
        $requiredHeaders = ['title', 'description', 'latitude', 'longitude', 'location', 'phonenumber', 'countryCode'];
        $missingHeaders = array_diff($requiredHeaders, $headers);
        
        if (!empty($missingHeaders)) {
            return back()->withErrors(['file' => 'Missing required columns: ' . implode(', ', $missingHeaders) . 
                '. Please use the template provided by the "Download Template" button.']);
        }
        
        $firestore = new \Google\Cloud\Firestore\FirestoreClient([
            'projectId' => config('firestore.project_id'),
            'keyFilePath' => config('firestore.credentials'),
        ]);
        $collection = $firestore->collection('vendors');
        
        // Batch processing configuration
        $batchSize = 50; // Process 50 rows at a time
        $totalRows = count($rows);
        $batches = array_chunk($rows, $batchSize);
        
        $created = 0;
        $updated = 0;
        $failed = 0;
        $skippedRows = 0;
        $processedRows = 0;
        
        // Pre-load lookup data for better performance
        $lookupData = $this->preloadLookupData($firestore);
        
        foreach ($batches as $batchIndex => $batch) {
            $batchCreated = 0;
            $batchUpdated = 0;
            $batchFailed = 0;
            
            foreach ($batch as $rowIndex => $row) {
                $globalRowIndex = $batchIndex * $batchSize + $rowIndex;
                $rowNum = $globalRowIndex + 2; // Excel row number
                $data = array_combine($headers, $row);
                
                // Skip completely empty rows
                if ($this->isEmptyRow($row)) {
                    continue;
                }
                
                try {
                    $result = $this->processRestaurantRow($data, $rowNum, $firestore, $collection, $lookupData, $skipInvalidRows);
                    
                    if ($result['success']) {
                        if ($result['action'] === 'created') {
                            $batchCreated++;
                        } else {
                            $batchUpdated++;
                        }
                    } else {
                        if ($result['action'] === 'skipped') {
                            $skippedRows++;
                        } else {
                            $batchFailed++;
                        }
                    }
                } catch (\Exception $e) {
                    $batchFailed++;
                }
                
                $processedRows++;
            }
            
            // Commit batch results
            $created += $batchCreated;
            $updated += $batchUpdated;
            $failed += $batchFailed;
            
            // Log progress for large datasets
            if ($totalRows > 100) {
                \Log::info("Bulk import progress: {$processedRows}/{$totalRows} rows processed");
            }
        }
        
        $msg = "Restaurant created: $created, updated: $updated, failed: $failed";
        if ($skippedRows > 0) {
            $msg .= ", skipped: $skippedRows";
        }
        
        if ($failed > 0) {
            return back()->withErrors(['file' => $msg]);
        }
        return back()->with('success', $msg);
    }
    
    /**
     * Preload lookup data to avoid repeated queries
     */
    private function preloadLookupData($firestore)
    {
        $lookupData = [
            'users' => [],
            'categories' => [],
            'cuisines' => [],
            'zones' => [],
            'existing_restaurants' => [] // Add existing restaurants for duplicate detection
        ];
        
        try {
            // Preload users (limit to avoid memory issues)
            $userDocs = $firestore->collection('users')->limit(1000)->documents();
            foreach ($userDocs as $userDoc) {
                $user = $userDoc->data();
                $lookupData['users'][$userDoc->id()] = $user;
                // Index by email and name for faster lookup
                if (isset($user['email'])) {
                    $lookupData['users']['email_' . strtolower($user['email'])] = $userDoc->id();
                }
                if (isset($user['firstName']) && isset($user['lastName'])) {
                    $lookupData['users']['name_' . strtolower($user['firstName'] . ' ' . $user['lastName'])] = $userDoc->id();
                }
            }
            
            // Preload categories
            $catDocs = $firestore->collection('vendor_categories')->documents();
            foreach ($catDocs as $catDoc) {
                $cat = $catDoc->data();
                $lookupData['categories'][strtolower($cat['title'] ?? '')] = $catDoc->id();
            }
            
            // Preload zones
            $zoneDocs = $firestore->collection('zone')->documents();
            foreach ($zoneDocs as $zoneDoc) {
                $zone = $zoneDoc->data();
                if (isset($zone['name'])) {
                    $lookupData['zones'][strtolower(trim($zone['name']))] = $zoneDoc->id();
                }
            }
            \Log::info("Preloaded " . count($lookupData['zones']) . " zones: " . implode(', ', array_keys($lookupData['zones'])));
            
            // Preload cuisines
            $cuisineDocs = $firestore->collection('vendor_cuisines')->documents();
            foreach ($cuisineDocs as $cuisineDoc) {
                $cuisine = $cuisineDoc->data();
                if (isset($cuisine['title'])) {
                    $lookupData['cuisines'][strtolower(trim($cuisine['title']))] = $cuisineDoc->id();
                }
            }
            \Log::info("Preloaded " . count($lookupData['cuisines']) . " cuisines: " . implode(', ', array_keys($lookupData['cuisines'])));
            
            // Preload existing restaurants for duplicate detection (limit to recent ones)
            $restaurantDocs = $firestore->collection('vendors')
                ->orderBy('createdAt', 'desc')
                ->limit(5000)->documents();
            foreach ($restaurantDocs as $restaurantDoc) {
                $restaurant = $restaurantDoc->data();
                if (isset($restaurant['title']) && isset($restaurant['location'])) {
                    $key = strtolower(trim($restaurant['title'])) . '|' . strtolower(trim($restaurant['location']));
                    $lookupData['existing_restaurants'][$key] = $restaurantDoc->id();
                }
            }
            
        } catch (\Exception $e) {
            \Log::error("Error preloading lookup data: " . $e->getMessage());
        }
        
        return $lookupData;
    }
    
    /**
     * Process a single restaurant row with optimized lookups
     */
    private function processRestaurantRow($data, $rowNum, $firestore, $collection, $lookupData, $skipInvalidRows = false)
    {
        // --- Data Validation ---
        $validationErrors = $this->validateRestaurantData($data, $rowNum);
        if (!empty($validationErrors)) {
            if ($skipInvalidRows) {
                return [
                    'success' => false,
                    'action' => 'skipped',
                    'error' => implode('; ', $validationErrors)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => implode('; ', $validationErrors)
                ];
            }
        }
        
        // --- Duplicate Detection ---
        $duplicateCheck = $this->checkDuplicateRestaurant($data, $lookupData, $rowNum);
        if ($duplicateCheck['isDuplicate']) {
            return [
                'success' => false,
                'error' => $duplicateCheck['error']
            ];
        }
        
        // --- Optimized Author lookup using preloaded data ---
        if (empty($data['author'])) {
            $authorFound = false;
            
            // 1. Lookup by email if authorEmail is provided
            if (!empty($data['authorEmail'])) {
                $emailKey = 'email_' . strtolower(trim($data['authorEmail']));
                if (isset($lookupData['users'][$emailKey])) {
                    $data['author'] = $lookupData['users'][$emailKey];
                    $authorFound = true;
                }
            }
            
            // 2. Lookup by exact authorName
            if (!$authorFound && !empty($data['authorName'])) {
                $nameKey = 'name_' . strtolower(trim($data['authorName']));
                if (isset($lookupData['users'][$nameKey])) {
                    $data['author'] = $lookupData['users'][$nameKey];
                    $authorFound = true;
                }
            }
            
            // 3. Fallback to fuzzy match only if necessary
            if (!$authorFound && !empty($data['authorName'])) {
                $authorFound = $this->fuzzyAuthorLookup($data, $firestore);
            }
            
            if (!$authorFound && (!empty($data['authorName']) || !empty($data['authorEmail']))) {
                return [
                    'success' => false,
                    'error' => "Row $rowNum: author lookup failed for authorName '{$data['authorName']}' or authorEmail '{$data['authorEmail']}'."
                ];
            }
        }
        
        // --- Optimized Category lookup ---
            if (!empty($data['categoryTitle']) && empty($data['categoryID'])) {
                $titles = json_decode($data['categoryTitle'], true);
                if (!is_array($titles)) $titles = explode(',', $data['categoryTitle']);
                $categoryIDs = [];
            
                foreach ($titles as $title) {
                $titleLower = strtolower(trim($title));
                if (isset($lookupData['categories'][$titleLower])) {
                    $categoryIDs[] = $lookupData['categories'][$titleLower];
                } else {
                    // Fallback to fuzzy match
                    $found = $this->fuzzyCategoryLookup($title, $lookupData['categories']);
                    if ($found) {
                        $categoryIDs[] = $found;
                    } else {
                        return [
                            'success' => false,
                            'error' => "Row $rowNum: categoryTitle '$title' not found in vendor_categories."
                        ];
                    }
                }
            }
            $data['categoryID'] = $categoryIDs;
        }
        
        // --- Optimized Zone lookup ---
        if (!empty($data['zoneName']) && empty($data['zoneId'])) {
            $zoneNameLower = strtolower(trim($data['zoneName']));
            if (isset($lookupData['zones'][$zoneNameLower])) {
                $data['zoneId'] = $lookupData['zones'][$zoneNameLower];
            } else {
                // Fallback to fuzzy match
                $found = $this->fuzzyZoneLookup($data['zoneName'], $lookupData['zones']);
                if ($found) {
                    $data['zoneId'] = $found;
                } else {
                    // Debug: Log available zones for troubleshooting
                    $availableZones = array_keys($lookupData['zones']);
                    \Log::warning("Zone lookup failed for '{$data['zoneName']}'. Available zones: " . implode(', ', $availableZones));
                    
                    return [
                        'success' => false,
                        'error' => "Row $rowNum: zoneName '{$data['zoneName']}' not found in zone collection. Available zones: " . implode(', ', array_slice($availableZones, 0, 10))
                    ];
                }
            }
        }
        
        // Validate zoneId if provided directly
        if (!empty($data['zoneId']) && !in_array($data['zoneId'], array_values($lookupData['zones']))) {
            $availableZoneIds = array_values($lookupData['zones']);
            $availableZoneNames = array_keys($lookupData['zones']);
            
            // Check if the value looks like a zone name instead of an ID
            $providedValue = $data['zoneId'];
            $zoneNameLower = strtolower(trim($providedValue));
            
            if (isset($lookupData['zones'][$zoneNameLower])) {
                // The user provided a zone name in the zoneId column - convert it
                $data['zoneId'] = $lookupData['zones'][$zoneNameLower];
            } else {
                return [
                    'success' => false,
                    'error' => "Row $rowNum: zoneId '{$providedValue}' not found in zone collection. " .
                               "If you meant to provide a zone name, use column 'zoneName' instead of 'zoneId'. " .
                               "Available zone names: " . implode(', ', array_slice($availableZoneNames, 0, 10)) . ". " .
                               "Available zone IDs: " . implode(', ', array_slice($availableZoneIds, 0, 5))
                ];
            }
        }
        
        // --- Optimized Vendor Cuisine lookup ---
        if (!empty($data['vendorCuisineTitle']) && empty($data['vendorCuisineID'])) {
            $titleLower = strtolower(trim($data['vendorCuisineTitle']));
            if (isset($lookupData['cuisines'][$titleLower])) {
                $data['vendorCuisineID'] = $lookupData['cuisines'][$titleLower];
            } else {
                // Fallback to fuzzy match
                $found = $this->fuzzyCuisineLookup($data['vendorCuisineTitle'], $lookupData['cuisines']);
                if ($found) {
                    $data['vendorCuisineID'] = $found;
                } else {
                    // Debug: Log available cuisines for troubleshooting
                    $availableCuisines = array_keys($lookupData['cuisines']);
                    \Log::warning("Cuisine lookup failed for '{$data['vendorCuisineTitle']}'. Available cuisines: " . implode(', ', $availableCuisines));
                    
                    return [
                        'success' => false,
                        'error' => "Row $rowNum: vendorCuisineTitle '{$data['vendorCuisineTitle']}' not found in vendor_cuisines. Available cuisines: " . implode(', ', array_slice($availableCuisines, 0, 10))
                    ];
                }
            }
        }
        
        // Validate vendorCuisineID if provided directly
        if (!empty($data['vendorCuisineID']) && !in_array($data['vendorCuisineID'], array_values($lookupData['cuisines']))) {
            $availableCuisineIds = array_values($lookupData['cuisines']);
            $availableCuisineNames = array_keys($lookupData['cuisines']);
            
            // Check if the value looks like a cuisine name instead of an ID
            $providedValue = $data['vendorCuisineID'];
            $cuisineNameLower = strtolower(trim($providedValue));
            
            if (isset($lookupData['cuisines'][$cuisineNameLower])) {
                // The user provided a cuisine name in the vendorCuisineID column - convert it
                $data['vendorCuisineID'] = $lookupData['cuisines'][$cuisineNameLower];
            } else {
                return [
                    'success' => false,
                    'error' => "Row $rowNum: vendorCuisineID '{$providedValue}' not found in vendor_cuisines collection. " .
                               "If you meant to provide a cuisine name, use column 'vendorCuisineTitle' instead of 'vendorCuisineID'. " .
                               "Available cuisine names: " . implode(', ', array_slice($availableCuisineNames, 0, 10)) . ". " .
                               "Available cuisine IDs: " . implode(', ', array_slice($availableCuisineIds, 0, 5))
                ];
            }
        }
        
        // --- Data Type Conversions and Structure Fixes ---
        $data = $this->processDataTypes($data);
        
        // --- Create or Update with Retry Mechanism ---
            if (!empty($data['id'])) {
                // Update
            try {
                return $this->retryFirestoreOperation(function() use ($collection, $data, $rowNum) {
                $docRef = $collection->document($data['id']);
                $snapshot = $docRef->snapshot();
                if (!$snapshot->exists()) {
                        return [
                            'success' => false,
                            'error' => "Row $rowNum: Restaurant with ID {$data['id']} not found."
                        ];
                }
                $updateData = $data;
                unset($updateData['id']);
                
                // Filter out empty keys to prevent "empty field paths" error
                $updateData = array_filter($updateData, function($value, $key) {
                    return !empty($key) && $value !== null && $value !== '';
                }, ARRAY_FILTER_USE_BOTH);
                
                if (!empty($updateData)) {
                    $docRef->update(array_map(
                        fn($k, $v) => ['path' => $k, 'value' => $v],
                        array_keys($updateData), $updateData
                    ));
                }
                    return ['success' => true, 'action' => 'updated'];
                });
                } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => "Row $rowNum: Update failed after retries ({$e->getMessage()})"
                ];
                }
            } else {
                // Create (auto Firestore ID)
                try {
                return $this->retryFirestoreOperation(function() use ($collection, $data) {
                    // Filter out empty values to prevent issues
                    $createData = array_filter($data, function($value, $key) {
                        return !empty($key) && $value !== null && $value !== '';
                    }, ARRAY_FILTER_USE_BOTH);
                    
                    $docRef = $collection->add($createData);
                    $docRef->set(['id' => $docRef->id()], ['merge' => true]);
                    return ['success' => true, 'action' => 'created'];
                });
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => "Row $rowNum: Create failed after retries ({$e->getMessage()})"
                ];
            }
        }
    }
    
    /**
     * Validate restaurant data before processing
     */
    private function validateRestaurantData($data, $rowNum)
    {
        $errors = [];
        
        // Clean and trim data first
        $data = array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
        
        // Required field validation - collect all missing fields for this row
        $requiredFields = ['title', 'description', 'latitude', 'longitude', 'location', 'phonenumber', 'countryCode'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        // If there are missing required fields, create a simple error message
        if (!empty($missingFields)) {
            $errors[] = "Row $rowNum: Missing required fields: " . implode(', ', $missingFields);
            return $errors; // Return early since missing required fields is a critical error
        }
        
        // Email validation
        if (!empty($data['authorEmail']) && !filter_var($data['authorEmail'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row $rowNum: Invalid email format for authorEmail";
        }
        
        // Phone number validation
        if (!empty($data['phonenumber']) && !preg_match('/^[+0-9\- ]{7,20}$/', $data['phonenumber'])) {
            $errors[] = "Row $rowNum: Invalid phone number format (should be 7-20 digits, can include +, -, spaces)";
        }
        
        // URL validation for photo
        if (!empty($data['photo']) && !filter_var($data['photo'], FILTER_VALIDATE_URL)) {
            $errors[] = "Row $rowNum: Invalid photo URL format";
        }
        
        // Coordinate validation
        if (!empty($data['latitude'])) {
            $lat = (float)$data['latitude'];
            if ($lat < -90 || $lat > 90) {
                $errors[] = "Row $rowNum: Latitude must be between -90 and 90 degrees";
            }
        }
        
        if (!empty($data['longitude'])) {
            $lng = (float)$data['longitude'];
            if ($lng < -180 || $lng > 180) {
                $errors[] = "Row $rowNum: Longitude must be between -180 and 180 degrees";
            }
        }
        
        // Boolean field validation
        $booleanFields = ['isOpen', 'enabledDiveInFuture', 'hidephotos', 'specialDiscountEnable'];
        foreach ($booleanFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $value = strtolower($data[$field]);
                if (!in_array($value, ['true', 'false', '1', '0', 'yes', 'no'])) {
                    $errors[] = "Row $rowNum: Invalid boolean value for '$field' (use true/false, 1/0, yes/no)";
                }
            }
        }
        
        // Numeric field validation
        $numericFields = ['restaurantCost'];
        foreach ($numericFields as $field) {
            if (!empty($data[$field]) && !is_numeric($data[$field])) {
                $errors[] = "Row $rowNum: Invalid numeric value for '$field'";
            }
        }
        
        // Time format validation
        if (!empty($data['openDineTime']) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['openDineTime'])) {
            $errors[] = "Row $rowNum: Invalid time format for openDineTime (use HH:MM format)";
        }
        
        if (!empty($data['closeDineTime']) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $data['closeDineTime'])) {
            $errors[] = "Row $rowNum: Invalid time format for closeDineTime (use HH:MM format)";
        }
        
        return $errors;
    }
    
    /**
     * Provide helpful guidance for common validation errors
     */
    private function getValidationGuidance($missingFields)
    {
        $guidance = [];
        
        foreach ($missingFields as $field) {
            switch ($field) {
                case 'title':
                    $guidance[] = "Restaurant name is required";
                    break;
                case 'description':
                    $guidance[] = "Restaurant description is required";
                    break;
                case 'latitude':
                    $guidance[] = "Latitude coordinate is required (use Google Maps to get coordinates)";
                    break;
                case 'longitude':
                    $guidance[] = "Longitude coordinate is required (use Google Maps to get coordinates)";
                    break;
                case 'location':
                    $guidance[] = "Full address is required";
                    break;
                case 'phonenumber':
                    $guidance[] = "Phone number is required (7-20 digits, can include +, -, spaces)";
                    break;
                case 'countryCode':
                    $guidance[] = "Country code is required (e.g., IN for India, US for United States)";
                    break;
            }
        }
        
        return $guidance;
    }
    
    /**
     * Check if a row is completely empty
     */
    private function isEmptyRow($row)
    {
        foreach ($row as $cell) {
            if (!empty(trim($cell))) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check for duplicate restaurants
     */
    private function checkDuplicateRestaurant($data, $lookupData, $rowNum)
    {
        if (empty($data['title']) || empty($data['location'])) {
            return false; // Can't check duplicates without title and location
        }
        
        $key = strtolower(trim($data['title'])) . '|' . strtolower(trim($data['location']));
        
        if (isset($lookupData['existing_restaurants'][$key])) {
            return [
                'isDuplicate' => true,
                'existingId' => $lookupData['existing_restaurants'][$key],
                'error' => "Row $rowNum: Restaurant with title '{$data['title']}' and location '{$data['location']}' already exists (ID: {$lookupData['existing_restaurants'][$key]})"
            ];
        }
        
        return ['isDuplicate' => false];
    }
    
    /**
     * Retry mechanism for Firestore operations
     */
    private function retryFirestoreOperation($operation, $maxRetries = 3, $delay = 1000)
    {
        $attempts = 0;
        $lastException = null;
        
        while ($attempts < $maxRetries) {
            try {
                return $operation();
                } catch (\Exception $e) {
                $lastException = $e;
                $attempts++;
                
                if ($attempts < $maxRetries) {
                    \Log::warning("Firestore operation failed (attempt $attempts/$maxRetries): " . $e->getMessage());
                    usleep($delay * 1000); // Convert to microseconds
                    $delay *= 2; // Exponential backoff
                }
            }
        }
        
        throw $lastException;
    }
    
    /**
     * Optimized fuzzy author lookup
     */
    private function fuzzyAuthorLookup($data, $firestore)
    {
        // Use Firestore query instead of full scan
        $searchTerm = strtolower($data['authorName']);
        $userDocs = $firestore->collection('users')
            ->where('firstName', '>=', $searchTerm)
            ->where('firstName', '<=', $searchTerm . '\uf8ff')
            ->limit(10)->documents();
            
        foreach ($userDocs as $userDoc) {
            $user = $userDoc->data();
            if (
                (isset($user['firstName']) && stripos($user['firstName'], $searchTerm) !== false) ||
                (isset($user['lastName']) && stripos($user['lastName'], $searchTerm) !== false)
            ) {
                $data['author'] = $userDoc->id();
                return true;
            }
        }
        return false;
    }
    
    /**
     * Optimized fuzzy category lookup
     */
    private function fuzzyCategoryLookup($title, $categories)
    {
        $titleLower = strtolower(trim($title));
        foreach ($categories as $catTitle => $catId) {
            if (stripos($catTitle, $titleLower) !== false) {
                return $catId;
            }
        }
        return false;
    }
    
    /**
     * Optimized fuzzy cuisine lookup
     */
    private function fuzzyCuisineLookup($title, $cuisines)
    {
        $titleLower = strtolower(trim($title));
        foreach ($cuisines as $cuisineTitle => $cuisineId) {
            if (stripos($cuisineTitle, $titleLower) !== false) {
                return $cuisineId;
            }
        }
        return false;
    }
    
    /**
     * Optimized fuzzy zone lookup
     */
    private function fuzzyZoneLookup($zoneName, $zones)
    {
        $zoneNameLower = strtolower(trim($zoneName));
        foreach ($zones as $zoneTitle => $zoneId) {
            if (stripos($zoneTitle, $zoneNameLower) !== false) {
                return $zoneId;
            }
        }
        return false;
    }
    
    /**
     * Process data type conversions
     */
    private function processDataTypes($data)
    {
        // Fix categoryID - ensure it's an array
        if (isset($data['categoryID'])) {
            if (is_string($data['categoryID'])) {
                $data['categoryID'] = json_decode($data['categoryID'], true) ?: explode(',', $data['categoryID']);
            }
            if (!is_array($data['categoryID'])) {
                $data['categoryID'] = [$data['categoryID']];
            }
        }
        
        // Fix categoryTitle - ensure it's an array
        if (isset($data['categoryTitle'])) {
            if (is_string($data['categoryTitle'])) {
                $data['categoryTitle'] = json_decode($data['categoryTitle'], true) ?: explode(',', $data['categoryTitle']);
            }
            if (!is_array($data['categoryTitle'])) {
                $data['categoryTitle'] = [$data['categoryTitle']];
            }
        }
        
        // Fix adminCommission - ensure it's an object with proper structure
        if (isset($data['adminCommission'])) {
            if (is_string($data['adminCommission'])) {
                $adminCommission = json_decode($data['adminCommission'], true);
                if ($adminCommission) {
                    $data['adminCommission'] = $adminCommission;
                } else {
                    $data['adminCommission'] = [
                        'commissionType' => 'Percent',
                        'fix_commission' => (int)($data['adminCommission'] ?? 10),
                        'isEnabled' => true
                    ];
                }
            }
            // Ensure required fields exist
            if (!isset($data['adminCommission']['commissionType'])) {
                $data['adminCommission']['commissionType'] = 'Percent';
            }
            if (!isset($data['adminCommission']['fix_commission'])) {
                $data['adminCommission']['fix_commission'] = 10;
            }
            if (!isset($data['adminCommission']['isEnabled'])) {
                $data['adminCommission']['isEnabled'] = true;
            }
        }
        
        // Fix coordinates - create GeoPoint if latitude and longitude are provided
        if (isset($data['latitude']) && isset($data['longitude']) && 
            is_numeric($data['latitude']) && is_numeric($data['longitude'])) {
            $data['coordinates'] = new \Google\Cloud\Core\GeoPoint(
                (float)$data['latitude'], 
                (float)$data['longitude']
            );
        }
        
        // Fix boolean fields
        $booleanFields = ['isOpen', 'enabledDiveInFuture', 'hidephotos', 'specialDiscountEnable'];
        foreach ($booleanFields as $field) {
            if (isset($data[$field])) {
                if (is_string($data[$field])) {
                    $data[$field] = strtolower($data[$field]) === 'true';
                }
            }
        }
        
        // Fix numeric fields
        $numericFields = ['latitude', 'longitude', 'restaurantCost'];
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && is_numeric($data[$field])) {
                $data[$field] = (float)$data[$field];
            }
        }
        
        // Add missing required fields with defaults
        $defaultFields = [
            'hidephotos' => false,
            'createdAt' => new \Google\Cloud\Core\Timestamp(now()),
            'filters' => [
                'Free Wi-Fi' => 'No',
                'Good for Breakfast' => 'No',
                'Good for Dinner' => 'No',
                'Good for Lunch' => 'No',
                'Live Music' => 'No',
                'Outdoor Seating' => 'No',
                'Takes Reservations' => 'No',
                'Vegetarian Friendly' => 'No'
            ],
            'workingHours' => [
                [
                    'day' => 'Monday',
                    'timeslot' => [
                        [
                            'from' => '09:30',
                            'to' => '22:00'
                        ]
                    ]
                ],
                [
                    'day' => 'Tuesday',
                    'timeslot' => [
                        [
                            'from' => '09:30',
                            'to' => '22:00'
                        ]
                    ]
                ],
                [
                    'day' => 'Wednesday',
                    'timeslot' => [
                        [
                            'from' => '09:30',
                            'to' => '22:00'
                        ]
                    ]
                ],
                [
                    'day' => 'Thursday',
                    'timeslot' => [
                        [
                            'from' => '09:30',
                            'to' => '22:00'
                        ]
                    ]
                ],
                [
                    'day' => 'Friday',
                    'timeslot' => [
                        [
                            'from' => '09:30',
                            'to' => '22:00'
                        ]
                    ]
                ],
                [
                    'day' => 'Saturday',
                    'timeslot' => [
                        [
                            'from' => '09:30',
                            'to' => '22:00'
                        ]
                    ]
                ],
                [
                    'day' => 'Sunday',
                    'timeslot' => [
                        [
                            'from' => '09:30',
                            'to' => '22:00'
                        ]
                    ]
                ]
            ],
            'specialDiscount' => [],
            'photos' => [],
            'restaurantMenuPhotos' => []
        ];
        
        foreach ($defaultFields as $field => $defaultValue) {
            if (!isset($data[$field])) {
                $data[$field] = $defaultValue;
            }
        }
        
        return $data;
    }

    public function downloadBulkUpdateTemplate()
    {
        try {
            // Create a new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set up headers with proper column names
            $headers = [
                'id',                       // Optional: Restaurant ID (for updates)
                'title',                    // Required: Restaurant name
                'description',              // Required: Restaurant description
                'latitude',                 // Required: Latitude coordinate (-90 to 90)
                'longitude',                // Required: Longitude coordinate (-180 to 180)
                'location',                 // Required: Address
                'phonenumber',              // Required: Phone number
                'countryCode',              // Required: Country code (e.g., "IN")
                'zoneName',                 // Required: Zone name (will be converted to zoneId)
                'authorName',               // Optional: Vendor name (will be converted to author ID)
                'authorEmail',              // Optional: Vendor email (alternative to authorName)
                'categoryTitle',            // Required: Category names (comma-separated or JSON array)
                'vendorCuisineTitle',       // Required: Vendor cuisine name (will be converted to vendorCuisineID)
                'adminCommission',          // Optional: Commission structure (JSON string)
                'isOpen',                   // Optional: Restaurant open status (true/false)
                'enabledDiveInFuture',      // Optional: Dine-in future enabled (true/false)
                'restaurantCost',           // Optional: Restaurant cost (number)
                'openDineTime',             // Optional: Opening time (HH:MM format)
                'closeDineTime',            // Optional: Closing time (HH:MM format)
                'photo',                    // Optional: Main photo URL
                'hidephotos',               // Optional: Hide photos (true/false)
                'specialDiscountEnable'     // Optional: Special discount enabled (true/false)
            ];
        
            // Set headers
            foreach ($headers as $colIndex => $header) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($column . '1', $header);
                
                // Style headers
                $sheet->getStyle($column . '1')->getFont()->setBold(true);
                $sheet->getStyle($column . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle($column . '1')->getFill()->getStartColor()->setRGB('E6E6FA');
            }
            
            // Add sample data row
            $sampleData = [
                '',                                  // id (leave empty for new restaurants)
                'Sample Restaurant',                 // title
                'A great restaurant with delicious food', // description
                '15.12345',                         // latitude
                '80.12345',                         // longitude
                '123 Main Street, City, State',     // location
                '1234567890',                       // phonenumber
                'IN',                               // countryCode
                'Ongole',                           // zoneName
                'Vendor One',                       // authorName
                'vendor@example.com',               // authorEmail
                'Biryani, Pizza',                   // categoryTitle
                'Indian',                           // vendorCuisineTitle
                '{"commissionType":"Fixed","fix_commission":12,"isEnabled":true}', // adminCommission
                'true',                             // isOpen
                'false',                            // enabledDiveInFuture
                '250',                              // restaurantCost
                '09:30',                            // openDineTime
                '22:00',                            // closeDineTime
                'https://example.com/restaurant-photo.jpg', // photo
                'false',                            // hidephotos
                'false'                             // specialDiscountEnable
            ];
        
        foreach ($sampleData as $colIndex => $value) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($column . '2', $value);
        }
        
        // Add instructions row
        $instructions = [
            'Restaurant name (required)',           // title
            'Restaurant description (required)',    // description
            'Latitude coordinate -90 to 90 (required)', // latitude
            'Longitude coordinate -180 to 180 (required)', // longitude
            'Full address (required)',              // location
            'Phone number 7-20 digits (required)',  // phonenumber
            'Country code like IN, US (required)',  // countryCode
            'Zone name like Ongole, Hyderabad (required)', // zoneName
            'Vendor name (optional)',               // authorName
            'Vendor email (optional)',              // authorEmail
            'Category names separated by comma (required)', // categoryTitle
            'Cuisine name like Indian, Chinese (required)', // vendorCuisineTitle
            'JSON format commission (optional)',    // adminCommission
            'true/false for open status (optional)', // isOpen
            'true/false for dine-in future (optional)', // enabledDiveInFuture
            'Restaurant cost number (optional)',    // restaurantCost
            'Opening time HH:MM format (optional)', // openDineTime
            'Closing time HH:MM format (optional)', // closeDineTime
            'Photo URL (optional)',                 // photo
            'true/false to hide photos (optional)', // hidephotos
            'true/false for special discount (optional)' // specialDiscountEnable
        ];
        
        foreach ($instructions as $colIndex => $instruction) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($column . '3', $instruction);
            
            // Style instructions
            $sheet->getStyle($column . '3')->getFont()->setItalic(true);
            $sheet->getStyle($column . '3')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));
        }
        
        // Add available zones and cuisines info
        try {
            $firestore = new \Google\Cloud\Firestore\FirestoreClient([
                'projectId' => config('firestore.project_id'),
                'keyFilePath' => config('firestore.credentials'),
            ]);
            
            // Get available zones
            $zoneDocs = $firestore->collection('zone')->documents();
            $zones = [];
            foreach ($zoneDocs as $zoneDoc) {
                $zone = $zoneDoc->data();
                if (isset($zone['name'])) {
                    $zones[] = $zone['name'];
                }
            }
            
            // Get available cuisines
            $cuisineDocs = $firestore->collection('vendor_cuisines')->documents();
            $cuisines = [];
            foreach ($cuisineDocs as $cuisineDoc) {
                $cuisine = $cuisineDoc->data();
                if (isset($cuisine['title'])) {
                    $cuisines[] = $cuisine['title'];
                }
            }
            
            // Add available options to the sheet
            $sheet->setCellValue('A5', 'Available Zones:');
            $sheet->setCellValue('A6', implode(', ', $zones));
            $sheet->getStyle('A5')->getFont()->setBold(true);
            $sheet->getStyle('A6')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0066CC'));
            
            $sheet->setCellValue('A8', 'Available Cuisines:');
            $sheet->setCellValue('A9', implode(', ', $cuisines));
            $sheet->getStyle('A8')->getFont()->setBold(true);
            $sheet->getStyle('A9')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0066CC'));
            
        } catch (Exception $e) {
            $sheet->setCellValue('A5', 'Note: Could not load available zones and cuisines');
        }
        
        // Auto-size columns
        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add data validation for boolean fields
        $booleanFields = ['M', 'N', 'O', 'U', 'V']; // isOpen, enabledDiveInFuture, hidephotos, specialDiscountEnable
        foreach ($booleanFields as $column) {
            for ($row = 2; $row <= 1000; $row++) {
                $validation = $sheet->getDataValidation($column . $row);
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setFormula1('"true,false"');
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setPromptTitle('Boolean Value');
                $validation->setPrompt('Please select true or false');
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Invalid Value');
                $validation->setError('Please select true or false only');
            }
        }
        
        // Add data validation for country code
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $sheet->getDataValidation('G' . $row);
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setFormula1('"IN,US,UK,CA,AU,DE,FR,IT,ES,JP,CN,KR,BR,MX,AR,CL,CO,PE,VE,EC,BO,PY,UY,GY,SR,GF,FG,BR,AR,CL,CO,PE,VE,EC,BO,PY,UY,GY,SR,GF,FG"');
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setPromptTitle('Country Code');
            $validation->setPrompt('Please select a country code');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid Country Code');
            $validation->setError('Please select a valid country code');
        }
        
            foreach ($sampleData as $colIndex => $value) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($column . '2', $value);
            }
            
            // Add instructions row
            $instructions = [
                'Restaurant ID (optional - leave empty for new restaurants)', // id
                'Restaurant name (required)',           // title
                'Restaurant description (required)',    // description
                'Latitude coordinate -90 to 90 (required)', // latitude
                'Longitude coordinate -180 to 180 (required)', // longitude
                'Full address (required)',              // location
                'Phone number 7-20 digits (required)',  // phonenumber
                'Country code like IN, US (required)',  // countryCode
                'Zone name like Ongole, Hyderabad (required)', // zoneName
                'Vendor name (optional)',               // authorName
                'Vendor email (optional)',              // authorEmail
                'Category names separated by comma (required)', // categoryTitle
                'Cuisine name like Indian, Chinese (required)', // vendorCuisineTitle
                'JSON format commission (optional)',    // adminCommission
                'true/false for open status (optional)', // isOpen
                'true/false for dine-in future (optional)', // enabledDiveInFuture
                'Restaurant cost number (optional)',    // restaurantCost
                'Opening time HH:MM format (optional)', // openDineTime
                'Closing time HH:MM format (optional)', // closeDineTime
                'Photo URL (optional)',                 // photo
                'true/false to hide photos (optional)', // hidephotos
                'true/false for special discount (optional)' // specialDiscountEnable
            ];
            
            foreach ($instructions as $colIndex => $instruction) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($column . '3', $instruction);
                
                // Style instructions
                $sheet->getStyle($column . '3')->getFont()->setItalic(true);
                $sheet->getStyle($column . '3')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));
            }
            
            // Add available zones and cuisines info (with error handling)
            try {
                $firestore = new \Google\Cloud\Firestore\FirestoreClient([
                    'projectId' => config('firestore.project_id'),
                    'keyFilePath' => config('firestore.credentials'),
                ]);
                
                // Get available zones
                $zoneDocs = $firestore->collection('zone')->documents();
                $zones = [];
                foreach ($zoneDocs as $zoneDoc) {
                    $zone = $zoneDoc->data();
                    if (isset($zone['name'])) {
                        $zones[] = $zone['name'];
                    }
                }
                
                // Get available cuisines
                $cuisineDocs = $firestore->collection('vendor_cuisines')->documents();
                $cuisines = [];
                foreach ($cuisineDocs as $cuisineDoc) {
                    $cuisine = $cuisineDoc->data();
                    if (isset($cuisine['title'])) {
                        $cuisines[] = $cuisine['title'];
                    }
                }
                
                // Add available options to the sheet
                $sheet->setCellValue('A5', 'Available Zones:');
                $sheet->setCellValue('A6', implode(', ', $zones));
                $sheet->getStyle('A5')->getFont()->setBold(true);
                $sheet->getStyle('A6')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0066CC'));
                
                $sheet->setCellValue('A8', 'Available Cuisines:');
                $sheet->setCellValue('A9', implode(', ', $cuisines));
                $sheet->getStyle('A8')->getFont()->setBold(true);
                $sheet->getStyle('A9')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0066CC'));
                
            } catch (\Exception $e) {
                $sheet->setCellValue('A5', 'Note: Could not load available zones and cuisines');
                \Log::error('Error loading zones/cuisines for template: ' . $e->getMessage());
            }
            
            // Auto-size columns
            foreach (range('A', 'W') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Add data validation for boolean fields
            $booleanFields = ['O', 'P', 'U', 'V']; // isOpen, enabledDiveInFuture, hidephotos, specialDiscountEnable
            foreach ($booleanFields as $column) {
                for ($row = 2; $row <= 1000; $row++) {
                    $validation = $sheet->getDataValidation($column . $row);
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setFormula1('"true,false"');
                    $validation->setAllowBlank(false);
                    $validation->setShowDropDown(true);
                    $validation->setPromptTitle('Boolean Value');
                    $validation->setPrompt('Please select true or false');
                    $validation->setShowErrorMessage(true);
                    $validation->setErrorTitle('Invalid Value');
                    $validation->setError('Please select true or false only');
                }
            }
            
            // Add data validation for country code
            for ($row = 2; $row <= 1000; $row++) {
                $validation = $sheet->getDataValidation('H' . $row);
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setFormula1('"IN,US,UK,CA,AU,DE,FR,IT,ES,JP,CN,KR,BR,MX,AR,CL,CO,PE,VE,EC,BO,PY,UY,GY,SR,GF,FG"');
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setPromptTitle('Country Code');
                $validation->setPrompt('Please select a country code');
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Invalid Country Code');
                $validation->setError('Please select a valid country code');
            }
            
            // Create the Excel file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filePath = storage_path('app/templates/restaurants_bulk_update_template.xlsx');
            
            // Ensure directory exists
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $writer->save($filePath);
            
            return response()->download($filePath, 'restaurants_bulk_update_template.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="restaurants_bulk_update_template.xlsx"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error generating restaurant template: ' . $e->getMessage());
            return back()->withErrors(['file' => 'Error generating template: ' . $e->getMessage()]);
        }
    }

    /**
     * Get vendors data for DataTables (SQL-based)
     */
    public function getVendorsData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = $request->input('search.value', '');
            $type = $request->input('type', 'all'); // all, pending, approved
            
            // Build base query - use subquery to get unique vendors by firebase_id (like Firebase did)
            // This ensures we only get one record per unique vendor
            $subQuery = DB::table('users')
                ->select(DB::raw('MAX(id) as max_id'))
                ->where('role', 'vendor')
                ->whereNotNull('firebase_id')
                ->where('firebase_id', '!=', '')
                ->groupBy('firebase_id');
            
            // Apply type filter to subquery
            if ($type === 'pending') {
                $subQuery->where('active', '0');
            } elseif ($type === 'approved') {
                $subQuery->where('active', '1');
            }
            
            // Get the IDs from subquery
            $uniqueIds = $subQuery->pluck('max_id')->toArray();
            
            // Now build main query using those unique IDs
            $query = AppUser::whereIn('id', $uniqueIds);
            
            // Apply additional filters if provided
            if ($request->has('vendor_type') && $request->vendor_type != '') {
                $query->where('vType', $request->vendor_type);
            }
            
            if ($request->has('status') && $request->status != '') {
                $query->where('active', $request->status === 'active' ? '1' : '0');
            }
            
            if ($request->has('zone') && $request->zone != '') {
                $query->where('zoneId', $request->zone);
            }
            
            // Store zone sort preference for later
            $zoneSort = $request->input('zone_sort', '');
            
            // Apply date range filter
            // The createdAt field is stored as JSON string like "2025-10-16T07:13:41.487000Z"
            // We need to strip quotes and compare
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = date('Y-m-d', strtotime($request->start_date));
                $endDate = date('Y-m-d', strtotime($request->end_date));
                
                $query->whereRaw("DATE(REPLACE(REPLACE(createdAt, '\"', ''), 'T', ' ')) BETWEEN ? AND ?", 
                    [$startDate, $endDate]);
            }
            
            // Get total count before applying search
            $totalRecords = $query->count();
            
            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('firstName', 'like', "%{$searchValue}%")
                      ->orWhere('lastName', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%")
                      ->orWhere('phoneNumber', 'like', "%{$searchValue}%")
                      ->orWhere(DB::raw("CONCAT(firstName, ' ', lastName)"), 'like', "%{$searchValue}%");
                });
            }
            
            $filteredRecords = $query->count();
            
            // Apply ordering
            // If zone sort is requested, sort by zone name, otherwise by createdAt descending
            if (!empty($zoneSort)) {
                // Join with zone table to sort by zone name
                $vendors = $query->leftJoin('zone', 'users.zoneId', '=', 'zone.id')
                               ->select('users.*', 'zone.name as zone_name')
                               ->orderBy('zone.name', $zoneSort)
                               ->orderByRaw("REPLACE(REPLACE(users.createdAt, '\"', ''), 'T', ' ') DESC")
                               ->skip($start)
                               ->take($length)
                               ->get();
            } else {
                // Get paginated records - order by parsed createdAt in descending order
                // Remove quotes and convert to proper datetime for sorting
                $vendors = $query->orderByRaw("REPLACE(REPLACE(createdAt, '\"', ''), 'T', ' ') DESC")
                               ->skip($start)
                               ->take($length)
                               ->get();
            }
            
            // Build response data
            $data = [];
            foreach ($vendors as $vendor) {
                // Parse createdAt date from JSON format "2025-10-16T07:13:41.487000Z"
                $createdAtFormatted = '';
                if ($vendor->createdAt) {
                    try {
                        // Remove quotes and parse the ISO date
                        $dateStr = trim($vendor->createdAt, '"');
                        $date = new \DateTime($dateStr);
                        $createdAtFormatted = $date->format('M d, Y h:i A');
                    } catch (\Exception $e) {
                        $createdAtFormatted = $vendor->createdAt;
                    }
                }
                
                // Parse subscription expiry date similarly
                $expiryDateFormatted = '';
                if ($vendor->subscriptionExpiryDate) {
                    try {
                        $dateStr = trim($vendor->subscriptionExpiryDate, '"');
                        $date = new \DateTime($dateStr);
                        $expiryDateFormatted = $date->format('M d, Y');
                    } catch (\Exception $e) {
                        $expiryDateFormatted = $vendor->subscriptionExpiryDate;
                    }
                }
                
                $vendorData = [
                    'id' => $vendor->firebase_id ?? $vendor->_id,
                    'firstName' => $vendor->firstName ?? '',
                    'lastName' => $vendor->lastName ?? '',
                    'fullName' => trim(($vendor->firstName ?? '') . ' ' . ($vendor->lastName ?? '')),
                    'email' => $vendor->email ?? '',
                    'phoneNumber' => $vendor->phoneNumber ?? '',
                    'countryCode' => $vendor->countryCode ?? '',
                    'profilePictureURL' => $vendor->profilePictureURL ?? '',
                    'active' => $vendor->active == '1' || $vendor->active === 'true' || $vendor->active === true,
                    'zoneId' => $vendor->zoneId ?? '',
                    'vType' => $vendor->vType ?? 'restaurant',
                    'vendorID' => $vendor->vendorID ?? '',
                    'subscriptionPlanId' => $vendor->subscriptionPlanId ?? '',
                    'subscription_plan' => $vendor->subscription_plan ? json_decode($vendor->subscription_plan, true) : null,
                    'subscriptionExpiryDate' => $expiryDateFormatted,
                    'subscriptionExpiryDateRaw' => $vendor->subscriptionExpiryDate ?? '',
                    'userBankDetails' => $vendor->userBankDetails ? json_decode($vendor->userBankDetails, true) : null,
                    'createdAt' => $createdAtFormatted,
                    'createdAtRaw' => $vendor->createdAt ?? '',
                    'isDocumentVerify' => $vendor->isDocumentVerify == '1' || $vendor->isDocumentVerify === 'true' || $vendor->isDocumentVerify === true,
                ];
                
                $data[] = $vendorData;
            }
            
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'vendor_count' => $filteredRecords
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching vendors data: ' . $e->getMessage());
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get zones for vendor filter
     */
    public function getZones()
    {
        try {
            // Get all zones first, then filter for publish = 1
            $allZones = DB::table('zone')
                      ->orderBy('name', 'asc')
                      ->get();
            
            \Log::info('Total zones found: ' . $allZones->count());
            
            // Filter for published zones (handle different data types)
            $zones = $allZones->filter(function($zone) {
                return $zone->publish == 1 || 
                       $zone->publish === '1' || 
                       $zone->publish === true || 
                       $zone->publish === 'true';
            })->values();
            
            \Log::info('Published zones: ' . $zones->count());
            
            return response()->json([
                'success' => true,
                'data' => $zones,
                'total_zones' => $allZones->count(),
                'published_zones' => $zones->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching zones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching zones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single zone with area data
     */
    public function getZoneById($id)
    {
        try {
            $zone = DB::table('zone')->where('id', $id)->first();
            
            if (!$zone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone not found'
                ], 404);
            }
            
            // Return zone with area as-is (already JSON string in database)
            return response()->json([
                'id' => $zone->id,
                'name' => $zone->name,
                'latitude' => $zone->latitude,
                'longitude' => $zone->longitude,
                'area' => $zone->area, // JSON string
                'publish' => $zone->publish
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching zone: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single vendor data by ID
     */
    public function getVendorById($id)
    {
        try {
            // Try to find vendor by multiple ID fields
            $vendor = AppUser::where(function($query) use ($id) {
                $query->where('firebase_id', $id)
                      ->orWhere('_id', $id);
                
                // Also try numeric ID if the input is numeric
                if (is_numeric($id)) {
                    $query->orWhere('id', $id);
                }
            })
            ->where('role', 'vendor')
            ->first();
            
            // Log for debugging
            \Log::info('Looking for vendor with ID: ' . $id);
            
            if (!$vendor) {
                \Log::warning('Vendor not found with ID: ' . $id);
                
                // Try to find any vendor to help debug
                $anyVendor = AppUser::where('role', 'vendor')->first();
                if ($anyVendor) {
                    \Log::info('Sample vendor found - firebase_id: ' . ($anyVendor->firebase_id ?? 'NULL') . ', _id: ' . ($anyVendor->_id ?? 'NULL'));
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found with ID: ' . $id
                ], 404);
            }
            
            \Log::info('Vendor found: ' . ($vendor->firstName ?? '') . ' ' . ($vendor->lastName ?? ''));
            
            // Parse dates from JSON format
            $subscriptionExpiryDate = '';
            $subscriptionExpiryDateFormatted = '';
            if ($vendor->subscriptionExpiryDate) {
                try {
                    $dateStr = trim($vendor->subscriptionExpiryDate, '"');
                    $date = new \DateTime($dateStr);
                    $subscriptionExpiryDate = $date->format('Y-m-d\TH:i:s.u\Z');
                    $subscriptionExpiryDateFormatted = $date->format('Y-m-d'); // For date input field
                } catch (\Exception $e) {
                    \Log::warning('Error parsing subscription expiry date: ' . $e->getMessage());
                    $subscriptionExpiryDate = trim($vendor->subscriptionExpiryDate, '"');
                    $subscriptionExpiryDateFormatted = '';
                }
            }
            
            // Parse userBankDetails
            $userBankDetails = null;
            if ($vendor->userBankDetails) {
                try {
                    if (is_string($vendor->userBankDetails)) {
                        $userBankDetails = json_decode($vendor->userBankDetails, true);
                    } else {
                        $userBankDetails = $vendor->userBankDetails;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error parsing bank details: ' . $e->getMessage());
                }
            }
            
            // Parse subscription_plan
            $subscriptionPlan = null;
            if ($vendor->subscription_plan) {
                try {
                    if (is_string($vendor->subscription_plan)) {
                        $subscriptionPlan = json_decode($vendor->subscription_plan, true);
                    } else {
                        $subscriptionPlan = $vendor->subscription_plan;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error parsing subscription plan: ' . $e->getMessage());
                }
            }
            
            $vendorData = [
                'id' => $vendor->firebase_id ?? $vendor->_id ?? $vendor->id,
                'firstName' => $vendor->firstName ?? '',
                'lastName' => $vendor->lastName ?? '',
                'email' => $vendor->email ?? '',
                'phoneNumber' => $vendor->phoneNumber ?? '',
                'countryCode' => $vendor->countryCode ?? '',
                'profilePictureURL' => $vendor->profilePictureURL ?? '',
                'active' => $vendor->active == '1' || $vendor->active === 'true' || $vendor->active === true,
                'zoneId' => $vendor->zoneId ?? '',
                'vType' => $vendor->vType ?? 'restaurant',
                'vendorID' => $vendor->vendorID ?? '',
                'subscriptionPlanId' => $vendor->subscriptionPlanId ?? '',
                'subscription_plan' => $subscriptionPlan,
                'subscriptionExpiryDate' => $subscriptionExpiryDateFormatted, // For date input
                'subscriptionExpiryDateRaw' => $vendor->subscriptionExpiryDate ?? '',
                'userBankDetails' => $userBankDetails,
                'provider' => $vendor->provider ?? 'email',
                'isDocumentVerify' => $vendor->isDocumentVerify == '1' || $vendor->isDocumentVerify === 'true' || $vendor->isDocumentVerify === true,
            ];
            
            \Log::info('Returning vendor data: ' . json_encode($vendorData));
            
            return response()->json([
                'success' => true,
                'data' => $vendorData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching vendor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching vendor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update vendor data
     */
    public function updateVendor(Request $request, $id)
    {
        try {
            // Try to find vendor by multiple ID fields
            $vendor = AppUser::where(function($query) use ($id) {
                $query->where('firebase_id', $id)
                      ->orWhere('_id', $id);
                
                // Also try numeric ID if the input is numeric
                if (is_numeric($id)) {
                    $query->orWhere('id', $id);
                }
            })
            ->where('role', 'vendor')
            ->first();
            
            \Log::info('Updating vendor with ID: ' . $id);
            
            if (!$vendor) {
                \Log::warning('Vendor not found for update with ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found with ID: ' . $id
                ], 404);
            }
            
            // Update vendor fields
            if ($request->has('firstName')) $vendor->firstName = $request->firstName;
            if ($request->has('lastName')) $vendor->lastName = $request->lastName;
            if ($request->has('phoneNumber')) $vendor->phoneNumber = $request->phoneNumber;
            if ($request->has('countryCode')) $vendor->countryCode = $request->countryCode;
            if ($request->has('vType')) $vendor->vType = $request->vType;
            if ($request->has('profilePictureURL')) $vendor->profilePictureURL = $request->profilePictureURL;
            if ($request->has('active')) $vendor->active = $request->active ? '1' : '0';
            if ($request->has('subscriptionPlanId')) $vendor->subscriptionPlanId = $request->subscriptionPlanId;
            if ($request->has('subscription_plan')) $vendor->subscription_plan = json_encode($request->subscription_plan);
            if ($request->has('subscriptionExpiryDate')) {
                // Format date as JSON string like "2025-10-16T07:13:41.487000Z" if it's a regular date
                $expiryDate = $request->subscriptionExpiryDate;
                if (!str_starts_with($expiryDate, '"')) {
                    try {
                        $date = new \DateTime($expiryDate);
                        $expiryDate = '"' . $date->format('Y-m-d\TH:i:s.u\Z') . '"';
                    } catch (\Exception $e) {
                        // Keep as is if parsing fails
                    }
                }
                $vendor->subscriptionExpiryDate = $expiryDate;
            }
            if ($request->has('userBankDetails')) $vendor->userBankDetails = json_encode($request->userBankDetails);
            
            $vendor->save();
            
            // Update vendor business info if vendorID exists
            if ($vendor->vendorID && $request->has('authorName')) {
                $vendorBusiness = Vendor::find($vendor->vendorID);
                if ($vendorBusiness) {
                    $vendorBusiness->authorName = $request->authorName;
                    if ($request->has('profilePictureURL')) {
                        $vendorBusiness->authorProfilePic = $request->profilePictureURL;
                    }
                    if ($request->has('subscriptionExpiryDate')) {
                        $vendorBusiness->subscriptionExpiryDate = $request->subscriptionExpiryDate;
                    }
                    $vendorBusiness->save();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Vendor updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating vendor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating vendor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle vendor active status
     */
    public function toggleVendorStatus($id)
    {
        try {
            // Try to find vendor by multiple ID fields
            $vendor = AppUser::where(function($query) use ($id) {
                $query->where('firebase_id', $id)
                      ->orWhere('_id', $id);
                
                // Also try numeric ID if the input is numeric
                if (is_numeric($id)) {
                    $query->orWhere('id', $id);
                }
            })
            ->where('role', 'vendor')
            ->first();
            
            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found with ID: ' . $id
                ], 404);
            }
            
            $vendor->active = $vendor->active == '1' ? '0' : '1';
            $vendor->save();
            
            return response()->json([
                'success' => true,
                'active' => $vendor->active == '1'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling vendor status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error toggling vendor status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete vendor
     */
    public function deleteVendor($id)
    {
        try {
            // Try to find vendor by multiple ID fields
            $vendor = AppUser::where(function($query) use ($id) {
                $query->where('firebase_id', $id)
                      ->orWhere('_id', $id);
                
                // Also try numeric ID if the input is numeric
                if (is_numeric($id)) {
                    $query->orWhere('id', $id);
                }
            })
            ->where('role', 'vendor')
            ->first();
            
            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found with ID: ' . $id
                ], 404);
            }
            
            // Delete vendor and related data
            // Note: This is a simplified version. You may need to delete related data from other tables
            $vendor->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Vendor deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting vendor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting vendor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription plans
     */
    public function getSubscriptionPlans()
    {
        try {
            $plans = DB::table('subscription_plans')
                      ->where('isEnable', 1)
                      ->orderBy('name', 'asc')
                      ->get();
            
            return response()->json([
                'success' => true,
                'data' => $plans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subscription plans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new vendor
     */
    public function createVendor(Request $request)
    {
        try {
            $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'phoneNumber' => 'required',
                'countryCode' => 'required'
            ]);
            
            // Check if email already exists
            $existingVendor = AppUser::where('email', $request->email)->first();
            if ($existingVendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists'
                ], 400);
            }
            
            // Create vendor
            $vendor = new AppUser();
            $vendor->firstName = $request->firstName;
            $vendor->lastName = $request->lastName;
            $vendor->email = $request->email;
            $vendor->password = Hash::make($request->password);
            $vendor->phoneNumber = $request->phoneNumber;
            $vendor->countryCode = $request->countryCode;
            $vendor->role = 'vendor';
            $vendor->vType = $request->vType ?? 'restaurant';
            $vendor->active = $request->active ?? '0';
            $vendor->profilePictureURL = $request->profilePictureURL ?? '';
            $vendor->provider = 'email';
            $vendor->appIdentifier = 'web';
            $vendor->isDocumentVerify = '0';
            // Store createdAt in same JSON format as Firebase: "2025-10-16T07:13:41.487000Z"
            $vendor->createdAt = '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"';
            
            if ($request->has('subscriptionPlanId')) {
                $vendor->subscriptionPlanId = $request->subscriptionPlanId;
            }
            if ($request->has('subscription_plan')) {
                $vendor->subscription_plan = json_encode($request->subscription_plan);
            }
            if ($request->has('subscriptionExpiryDate')) {
                $vendor->subscriptionExpiryDate = $request->subscriptionExpiryDate;
            }
            
            // Generate unique firebase_id
            $vendor->firebase_id = uniqid();
            $vendor->_id = $vendor->firebase_id;
            
            $vendor->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully',
                'vendor_id' => $vendor->firebase_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating vendor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating vendor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get placeholder image
     */
    public function getPlaceholderImage()
    {
        try {
            // Settings table has Firebase-migrated structure: doc_id, document_name, fields
            $placeholder = DB::table('settings')
                            ->where('document_name', 'placeHolderImage')
                            ->first();
            
            if ($placeholder && !empty($placeholder->fields)) {
                $fieldsData = json_decode($placeholder->fields, true);
                if (isset($fieldsData['image'])) {
                    return response()->json([
                        'success' => true,
                        'image' => $fieldsData['image']
                    ]);
                }
            }
            
            // Return default placeholder if not found
            return response()->json([
                'success' => true,
                'image' => asset('images/placeholder-image.png')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching placeholder image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching placeholder image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new restaurant
     */
    public function createRestaurant(Request $request)
    {
        try {
            $restaurantData = $request->restaurantData;
            $restaurant_id = $request->restaurant_id ?? uniqid('rest_');
            $user_id = $request->user_id;
            
            // Helper function to convert boolean values
            $toBool = function($value) {
                if (is_bool($value)) return $value ? 1 : 0;
                if (is_string($value)) {
                    $value = strtolower($value);
                    return ($value === 'true' || $value === '1') ? 1 : 0;
                }
                return $value ? 1 : 0;
            };
            
            // Helper function to handle JSON encoding
            $toJson = function($value, $default = []) {
                if (!isset($value)) return json_encode($default);
                return is_string($value) ? $value : json_encode($value);
            };
            
            // Create restaurant record
            $restaurant = new Vendor();
            $restaurant->id = $restaurant_id;
            $restaurant->title = $restaurantData['title'] ?? '';
            $restaurant->description = $restaurantData['description'] ?? '';
            $restaurant->latitude = floatval($restaurantData['latitude'] ?? 0);
            $restaurant->longitude = floatval($restaurantData['longitude'] ?? 0);
            $restaurant->location = $restaurantData['location'] ?? '';
            $restaurant->photo = $restaurantData['photo'] ?? '';
            $restaurant->photos = $toJson($restaurantData['photos'] ?? null, []);
            $restaurant->phonenumber = $restaurantData['phonenumber'] ?? '';
            $restaurant->countryCode = $restaurantData['countryCode'] ?? '';
            $restaurant->zoneId = $restaurantData['zoneId'] ?? '';
            $restaurant->author = $restaurantData['author'] ?? '';
            $restaurant->authorName = $restaurantData['authorName'] ?? '';
            $restaurant->authorProfilePic = $restaurantData['authorProfilePic'] ?? '';
            $restaurant->categoryID = $toJson($restaurantData['categoryID'] ?? null, []);
            $restaurant->categoryTitle = $toJson($restaurantData['categoryTitle'] ?? null, []);
            $restaurant->vendorCuisineID = $restaurantData['vendorCuisineID'] ?? '';
            
            // Convert boolean fields to tinyint (0 or 1)
            $restaurant->reststatus = $toBool($restaurantData['reststatus'] ?? 1);
            $restaurant->isOpen = $toBool($restaurantData['isOpen'] ?? 1);
            $restaurant->specialDiscountEnable = $toBool($restaurantData['specialDiscountEnable'] ?? 0);
            $restaurant->enabledDiveInFuture = $toBool($restaurantData['enabledDiveInFuture'] ?? 0);
            
            $restaurant->workingHours = $toJson($restaurantData['workingHours'] ?? null, []);
            $restaurant->filters = $toJson($restaurantData['filters'] ?? null, []);
            $restaurant->adminCommission = $toJson($restaurantData['adminCommission'] ?? null, []);
            $restaurant->specialDiscount = $toJson($restaurantData['specialDiscount'] ?? null, []);
            $restaurant->restaurantCost = $restaurantData['restaurantCost'] ?? '';
            $restaurant->restaurantMenuPhotos = $toJson($restaurantData['restaurantMenuPhotos'] ?? null, []);
            $restaurant->openDineTime = $restaurantData['openDineTime'] ?? '';
            $restaurant->closeDineTime = $restaurantData['closeDineTime'] ?? '';
            $restaurant->subscriptionPlanId = $restaurantData['subscriptionPlanId'] ?? '';
            $restaurant->subscription_plan = isset($restaurantData['subscription_plan']) ? $toJson($restaurantData['subscription_plan'], null) : null;
            $restaurant->subscriptionExpiryDate = $restaurantData['subscriptionExpiryDate'] ?? '';
            $restaurant->subscriptionTotalOrders = $restaurantData['subscriptionTotalOrders'] ?? '';
            $restaurant->createdAt = '"' . gmdate('Y-m-d\TH:i:s.u\Z') . '"';
            
            $restaurant->save();
            
            // Update user vendorID if requested
            if ($request->updateUserVendorID && $user_id && $user_id !== 'admin_created') {
                $user = AppUser::where('firebase_id', $user_id)
                             ->orWhere('_id', $user_id)
                             ->where('role', 'vendor')
                             ->first();
                
                if ($user) {
                    $user->vendorID = $restaurant_id;
                    $user->save();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurant created successfully',
                'restaurant_id' => $restaurant_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating restaurant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating restaurant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get restaurants data for DataTables (SQL-based)
     */
    public function getRestaurantsData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $searchValue = $request->input('search.value', '');
            
            // Build base query - get unique restaurants by id
            $subQuery = DB::table('vendors')
                ->select(DB::raw('MAX(id) as vendor_id'))
                ->groupBy('id');
            
            $vendorIds = $subQuery->pluck('vendor_id')->toArray();
            $query = Vendor::whereIn('id', $vendorIds);
            
            // Apply filters
            if ($request->has('restaurant_type') && $request->restaurant_type != '') {
                if ($request->restaurant_type === 'true') {
                    $query->where('dine_in_active', '!=', '');
                }
            }
            
            if ($request->has('zone') && $request->zone != '') {
                $query->where('zoneId', $request->zone);
            }
            
            if ($request->has('vType') && $request->vType != '') {
                $query->where('vType', $request->vType);
            }
            
            // Apply date range filter
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = date('Y-m-d', strtotime($request->start_date));
                $endDate = date('Y-m-d', strtotime($request->end_date));
                $query->whereRaw("DATE(REPLACE(REPLACE(createdAt, '\"', ''), 'T', ' ')) BETWEEN ? AND ?", 
                    [$startDate, $endDate]);
            }
            
            $totalRecords = $query->count();
            
            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('title', 'like', "%{$searchValue}%")
                      ->orWhere('location', 'like', "%{$searchValue}%")
                      ->orWhere('phonenumber', 'like', "%{$searchValue}%")
                      ->orWhere('description', 'like', "%{$searchValue}%");
                });
            }
            
            $filteredRecords = $query->count();
            
            // Get counts for statistics
            $totalRestaurants = Vendor::count();
            $activeRestaurants = Vendor::where('reststatus', 1)->count();
            $inactiveRestaurants = Vendor::where('reststatus', 0)->count();
            
            // Get new joined (last 30 days)
            $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
            $newJoined = Vendor::whereRaw("DATE(REPLACE(REPLACE(createdAt, '\"', ''), 'T', ' ')) >= ?", [$thirtyDaysAgo])->count();
            
            // Apply ordering - descending by createdAt
            $restaurants = $query->orderByRaw("REPLACE(REPLACE(createdAt, '\"', ''), 'T', ' ') DESC")
                               ->skip($start)
                               ->take($length)
                               ->get();
            
            // Build response data
            $data = [];
            foreach ($restaurants as $restaurant) {
                // Parse createdAt date
                $createdAtFormatted = '';
                if ($restaurant->createdAt) {
                    try {
                        $dateStr = trim($restaurant->createdAt, '"');
                        $date = new \DateTime($dateStr);
                        $createdAtFormatted = $date->format('M d, Y h:i A');
                    } catch (\Exception $e) {
                        $createdAtFormatted = $restaurant->createdAt;
                    }
                }
                
                $restaurantData = [
                    'id' => $restaurant->id,
                    'title' => $restaurant->title ?? '',
                    'description' => $restaurant->description ?? '',
                    'location' => $restaurant->location ?? '',
                    'latitude' => $restaurant->latitude ?? 0,
                    'longitude' => $restaurant->longitude ?? 0,
                    'photo' => $restaurant->photo ?? '',
                    'photos' => $restaurant->photos ? json_decode($restaurant->photos, true) : [],
                    'phonenumber' => $restaurant->phonenumber ?? '',
                    'zoneId' => $restaurant->zoneId ?? '',
                    'author' => $restaurant->author ?? '',
                    'authorName' => $restaurant->authorName ?? '',
                    'authorProfilePic' => $restaurant->authorProfilePic ?? '',
                    'categoryID' => $restaurant->categoryID ? json_decode($restaurant->categoryID, true) : [],
                    'categoryTitle' => $restaurant->categoryTitle ? json_decode($restaurant->categoryTitle, true) : [],
                    'reststatus' => $restaurant->reststatus == 1 || $restaurant->reststatus === 'true' || $restaurant->reststatus === true,
                    'isOpen' => $restaurant->isOpen == 1 || $restaurant->isOpen === 'true' || $restaurant->isOpen === true,
                    'reviewsCount' => $restaurant->reviewsCount ?? 0,
                    'reviewsSum' => $restaurant->reviewsSum ?? 0,
                    'createdAt' => $createdAtFormatted,
                    'createdAtRaw' => $restaurant->createdAt ?? '',
                    'vType' => $restaurant->vType ?? 'restaurant',
                    'walletAmount' => $restaurant->walletAmount ?? 0,
                ];
                
                $data[] = $restaurantData;
            }
            
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'stats' => [
                    'total' => $totalRestaurants,
                    'active' => $activeRestaurants,
                    'inactive' => $inactiveRestaurants,
                    'new_joined' => $newJoined
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching restaurants data: ' . $e->getMessage());
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get single restaurant data by ID
     */
    public function getRestaurantById($id)
    {
        try {
            // Try to find by string ID column first (Firebase-style ID), then by numeric primary key
            $restaurant = Vendor::where('id', $id)->first();
            
            if (!$restaurant && is_numeric($id)) {
                // Fallback to numeric primary key if not found by string ID
                $restaurant = Vendor::find($id);
            }
            
            \Log::info('Looking for restaurant with ID: ' . $id);
            
            if (!$restaurant) {
                \Log::warning('Restaurant not found with ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found with ID: ' . $id
                ], 404);
            }
            
            \Log::info('Restaurant found: ' . ($restaurant->title ?? ''));
            
            // Parse and format data
            $restaurantData = [
                'id' => $restaurant->id,
                'title' => $restaurant->title ?? '',
                'description' => $restaurant->description ?? '',
                'location' => $restaurant->location ?? '',
                'latitude' => $restaurant->latitude ?? 0,
                'longitude' => $restaurant->longitude ?? 0,
                'photo' => $restaurant->photo ?? '',
                'photos' => $restaurant->photos ? json_decode($restaurant->photos, true) : [],
                'phonenumber' => $restaurant->phonenumber ?? '',
                'zoneId' => $restaurant->zoneId ?? '',
                'author' => $restaurant->author ?? '',
                'authorName' => $restaurant->authorName ?? '',
                'authorProfilePic' => $restaurant->authorProfilePic ?? '',
                'categoryID' => $restaurant->categoryID ? json_decode($restaurant->categoryID, true) : [],
                'categoryTitle' => $restaurant->categoryTitle ? json_decode($restaurant->categoryTitle, true) : [],
                'cuisineID' => $restaurant->cuisineID ?? '',
                'cuisineTitle' => $restaurant->cuisineTitle ?? '',
                'vendorCuisineID' => $restaurant->vendorCuisineID ?? '',
                'reststatus' => $restaurant->reststatus == 1 || $restaurant->reststatus === 'true' || $restaurant->reststatus === true,
                'isOpen' => $restaurant->isOpen == 1 || $restaurant->isOpen === 'true' || $restaurant->isOpen === true,
                'reviewsCount' => $restaurant->reviewsCount ?? 0,
                'reviewsSum' => $restaurant->reviewsSum ?? 0,
                'workingHours' => $restaurant->workingHours ? json_decode($restaurant->workingHours, true) : [],
                'filters' => $restaurant->filters ? json_decode($restaurant->filters, true) : [],
                'createdAt' => $restaurant->createdAt ?? '',
                'vType' => $restaurant->vType ?? 'restaurant',
                'walletAmount' => $restaurant->walletAmount ?? 0,
                'adminCommission' => $restaurant->adminCommission ? json_decode($restaurant->adminCommission, true) : null,
                'DeliveryCharge' => $restaurant->DeliveryCharge ?? false,
                'specialDiscount' => $restaurant->specialDiscount ? json_decode($restaurant->specialDiscount, true) : [],
                'specialDiscountEnable' => $restaurant->specialDiscountEnable ?? false,
                'hidephotos' => $restaurant->hidephotos ?? false,
                'restaurantCost' => $restaurant->restaurantCost ?? '',
                'restaurantMenuPhotos' => $restaurant->restaurantMenuPhotos ? json_decode($restaurant->restaurantMenuPhotos, true) : [],
                'openDineTime' => $restaurant->openDineTime ?? '',
                'closeDineTime' => $restaurant->closeDineTime ?? '',
                'dine_in_active' => $restaurant->dine_in_active ?? '',
                'enabledDiveInFuture' => $restaurant->enabledDiveInFuture ?? false,
                'subscription_plan' => $restaurant->subscription_plan ? json_decode($restaurant->subscription_plan, true) : null,
                'subscriptionPlanId' => $restaurant->subscriptionPlanId ?? '',
                'subscriptionExpiryDate' => $restaurant->subscriptionExpiryDate ?? '',
                'subscriptionTotalOrders' => $restaurant->subscriptionTotalOrders ?? '',
                'coordinates' => [
                    'latitude' => $restaurant->latitude ?? 0,
                    'longitude' => $restaurant->longitude ?? 0
                ],
                'isActive' => $restaurant->isActive ?? true,
                'opentime' => $restaurant->opentime ?? '',
                'closetime' => $restaurant->closetime ?? ''
            ];
            
            return response()->json([
                'success' => true,
                'data' => $restaurantData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching restaurant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching restaurant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update restaurant data
     */
    public function updateRestaurant(Request $request, $id)
    {
        try {
            // Try to find by string ID column first, then by numeric primary key
            $restaurant = Vendor::where('id', $id)->first();
            
            if (!$restaurant && is_numeric($id)) {
                $restaurant = Vendor::find($id);
            }
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found with ID: ' . $id
                ], 404);
            }
            
            // Helper function to convert boolean values
            $toBool = function($value) {
                if (is_bool($value)) return $value ? 1 : 0;
                if (is_string($value)) {
                    $value = strtolower($value);
                    return ($value === 'true' || $value === '1') ? 1 : 0;
                }
                return $value ? 1 : 0;
            };
            
            // Helper function to handle JSON encoding
            $toJson = function($value, $default = []) {
                if (!isset($value)) return json_encode($default);
                return is_string($value) ? $value : json_encode($value);
            };
            
            // Update restaurant fields
            if ($request->has('title')) $restaurant->title = $request->title;
            if ($request->has('description')) $restaurant->description = $request->description;
            if ($request->has('location')) $restaurant->location = $request->location;
            if ($request->has('latitude')) $restaurant->latitude = floatval($request->latitude);
            if ($request->has('longitude')) $restaurant->longitude = floatval($request->longitude);
            if ($request->has('photo')) $restaurant->photo = $request->photo;
            if ($request->has('photos')) $restaurant->photos = $toJson($request->photos, []);
            if ($request->has('phonenumber')) $restaurant->phonenumber = $request->phonenumber;
            if ($request->has('zoneId')) $restaurant->zoneId = $request->zoneId;
            if ($request->has('categoryID')) $restaurant->categoryID = $toJson($request->categoryID, []);
            if ($request->has('categoryTitle')) $restaurant->categoryTitle = $toJson($request->categoryTitle, []);
            if ($request->has('cuisineID')) $restaurant->cuisineID = $request->cuisineID;
            if ($request->has('vendorCuisineID')) $restaurant->vendorCuisineID = $request->vendorCuisineID;
            
            // Convert boolean fields properly
            if ($request->has('reststatus')) $restaurant->reststatus = $toBool($request->reststatus);
            if ($request->has('isOpen')) $restaurant->isOpen = $toBool($request->isOpen);
            if ($request->has('specialDiscountEnable')) $restaurant->specialDiscountEnable = $toBool($request->specialDiscountEnable);
            if ($request->has('enabledDiveInFuture')) $restaurant->enabledDiveInFuture = $toBool($request->enabledDiveInFuture);
            
            if ($request->has('workingHours')) $restaurant->workingHours = $toJson($request->workingHours, []);
            if ($request->has('filters')) $restaurant->filters = $toJson($request->filters, []);
            if ($request->has('vType')) $restaurant->vType = $request->vType;
            if ($request->has('adminCommission')) $restaurant->adminCommission = $toJson($request->adminCommission, []);
            if ($request->has('specialDiscount')) $restaurant->specialDiscount = $toJson($request->specialDiscount, []);
            if ($request->has('restaurantCost')) $restaurant->restaurantCost = $request->restaurantCost;
            if ($request->has('restaurantMenuPhotos')) $restaurant->restaurantMenuPhotos = $toJson($request->restaurantMenuPhotos, []);
            if ($request->has('openDineTime')) $restaurant->openDineTime = $request->openDineTime;
            if ($request->has('closeDineTime')) $restaurant->closeDineTime = $request->closeDineTime;
            
            $restaurant->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurant updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating restaurant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating restaurant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle restaurant status
     */
    public function toggleRestaurantStatus($id)
    {
        try {
            // Try to find by string ID column first, then by numeric primary key
            $restaurant = Vendor::where('id', $id)->first();
            
            if (!$restaurant && is_numeric($id)) {
                $restaurant = Vendor::find($id);
            }
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found with ID: ' . $id
                ], 404);
            }
            
            $restaurant->reststatus = $restaurant->reststatus == 1 ? 0 : 1;
            $restaurant->save();
            
            return response()->json([
                'success' => true,
                'reststatus' => $restaurant->reststatus == 1
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling restaurant status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error toggling restaurant status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle restaurant open/close status
     */
    public function toggleRestaurantOpenStatus($id)
    {
        try {
            // Try to find by string ID column first, then by numeric primary key
            $restaurant = Vendor::where('id', $id)->first();
            
            if (!$restaurant && is_numeric($id)) {
                $restaurant = Vendor::find($id);
            }
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found with ID: ' . $id
                ], 404);
            }
            
            $restaurant->isOpen = $restaurant->isOpen == 1 ? 0 : 1;
            $restaurant->save();
            
            return response()->json([
                'success' => true,
                'isOpen' => $restaurant->isOpen == 1
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling restaurant open status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error toggling restaurant open status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete restaurant
     */
    public function deleteRestaurant($id)
    {
        try {
            // Try to find by string ID column first, then by numeric primary key
            $restaurant = Vendor::where('id', $id)->first();
            
            if (!$restaurant && is_numeric($id)) {
                $restaurant = Vendor::find($id);
            }
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found with ID: ' . $id
                ], 404);
            }
            
            $restaurant->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurant deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting restaurant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting restaurant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for restaurant
     */
    public function getCategories()
    {
        try {
            $categories = DB::table('vendor_categories')
                          ->orderBy('title', 'asc')
                          ->select('id', 'title', 'photo')
                          ->get();
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cuisines for restaurant
     */
    public function getCuisines()
    {
        try {
            $cuisines = DB::table('vendor_cuisines')
                        ->orderBy('title', 'asc')
                        ->select('id', 'title', 'photo')
                        ->get();
            
            return response()->json([
                'success' => true,
                'data' => $cuisines
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cuisines: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug: Check if vendor exists with given ID
     */
    public function debugVendor($id)
    {
        try {
            // Check all possible matches
            $byFirebaseId = AppUser::where('firebase_id', $id)->where('role', 'vendor')->first();
            $byUnderscoreId = AppUser::where('_id', $id)->where('role', 'vendor')->first();
            $byNumericId = is_numeric($id) ? AppUser::where('id', $id)->where('role', 'vendor')->first() : null;
            
            // Get sample vendors
            $sampleVendors = AppUser::where('role', 'vendor')->limit(5)->get(['id', 'firebase_id', '_id', 'firstName', 'lastName', 'email']);
            
            return response()->json([
                'search_id' => $id,
                'found_by_firebase_id' => $byFirebaseId ? true : false,
                'found_by_underscore_id' => $byUnderscoreId ? true : false,
                'found_by_numeric_id' => $byNumericId ? true : false,
                'vendor_firebase_id' => $byFirebaseId ? [
                    'firebase_id' => $byFirebaseId->firebase_id,
                    '_id' => $byFirebaseId->_id,
                    'name' => $byFirebaseId->firstName . ' ' . $byFirebaseId->lastName
                ] : null,
                'vendor_underscore_id' => $byUnderscoreId ? [
                    'firebase_id' => $byUnderscoreId->firebase_id,
                    '_id' => $byUnderscoreId->_id,
                    'name' => $byUnderscoreId->firstName . ' ' . $byUnderscoreId->lastName
                ] : null,
                'sample_vendors' => $sampleVendors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get restaurant statistics for view page
     */
    public function getRestaurantStats($id)
    {
        try {
            // Get total orders
            $totalOrders = DB::table('restaurant_orders')
                ->where('vendorID', $id)
                ->count();

            // Get total earnings (from completed orders)
            $completedOrders = DB::table('restaurant_orders')
                ->where('vendorID', $id)
                ->where('status', 'Order Completed')
                ->get();

            $totalEarnings = 0;
            foreach ($completedOrders as $order) {
                $discount = $order->discount ?? 0;
                $deliveryCharge = $order->deliveryCharge ?? 0;
                $tip = $order->tip ?? 0;
                $taxAmount = $order->tax ?? 0;
                
                $orderTotal = ($discount + $deliveryCharge + $tip + $taxAmount);
                $totalEarnings += $orderTotal;
            }

            // Get total payments (from payouts)
            $totalPayments = DB::table('payouts')
                ->where('vendorID', $id)
                ->where('paidDate', '!=', null)
                ->sum('amount');

            // Get remaining balance
            $remainingBalance = $totalEarnings - $totalPayments;

            return response()->json([
                'success' => true,
                'totalOrders' => $totalOrders,
                'totalEarnings' => $totalEarnings,
                'totalPayments' => $totalPayments,
                'remainingBalance' => $remainingBalance
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching restaurant stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching restaurant stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user by ID for restaurant view
     */
    public function getUserById($id)
    {
        try {
            $user = DB::table('users')
                ->where('id', $id)
                ->orWhere('firebase_id', $id)
                ->orWhere('_id', $id)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add wallet amount
     */
    public function addWalletAmount(Request $request)
    {
        try {
            $userId = $request->user_id;
            $amount = floatval($request->amount);
            $note = $request->note ?? '';

            // Get user
            $user = DB::table('users')
                ->where('id', $userId)
                ->orWhere('firebase_id', $userId)
                ->orWhere('_id', $userId)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Calculate new wallet amount
            $currentWalletAmount = floatval($user->wallet_amount ?? 0);
            $newWalletAmount = $currentWalletAmount + $amount;

            // Update user wallet
            DB::table('users')
                ->where('id', $user->id)
                ->update(['wallet_amount' => $newWalletAmount]);

            // Create wallet transaction
            $walletId = 'wallet_' . time() . '_' . uniqid();
            DB::table('wallet')->insert([
                'id' => $walletId,
                'user_id' => $user->id,
                'amount' => $amount,
                'date' => now(),
                'isTopUp' => 1,
                'order_id' => '',
                'payment_method' => 'Wallet',
                'payment_status' => 'success',
                'note' => $note,
                'transactionUser' => 'vendor',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet amount added successfully',
                'newWalletAmount' => $newWalletAmount,
                'transaction_id' => $walletId,
                'user' => [
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding wallet amount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding wallet amount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet balance
     */
    public function getWalletBalance($userId)
    {
        try {
            $user = DB::table('users')
                ->where('id', $userId)
                ->orWhere('firebase_id', $userId)
                ->orWhere('_id', $userId)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $walletBalance = floatval($user->wallet_amount ?? 0);

            return response()->json([
                'success' => true,
                'wallet_balance' => $walletBalance
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching wallet balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching wallet balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription history for user
     */
    public function getSubscriptionHistory($userId)
    {
        try {
            $history = DB::table('subscription_history')
                ->where('user_id', $userId)
                ->orderBy('createdAt', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching subscription history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subscription history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get email template by type
     */
    public function getEmailTemplate($type)
    {
        try {
            $template = DB::table('email_templates')
                ->where('type', $type)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email template not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'template' => $template
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching email template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching email template: ' . $e->getMessage()
            ], 500);
        }
    }
}
