@extends('layouts.app')
@section('content')
    <style>
        .editable-price {
            transition: all 0.2s ease;
            border-radius: 3px;
            padding: 2px 4px;
        }
        .editable-price:hover {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .editable-price.text-success {
            background-color: #d4edda !important;
            border-color: #c3e6cb !important;
        }
        .editable-price.text-danger {
            background-color: #f8d7da !important;
            border-color: #f5c6cb !important;
        }
        .editable-price input {
            border: 2px solid #007bff;
            border-radius: 3px;
            padding: 2px 4px;
            font-size: inherit;
        }

        .options-info {
            text-align: center;
        }

        .options-info .badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .options-info small {
            font-size: 10px;
        }

        /* Delete button styling */
        .delete-btn {
            color: #dc3545 !important;
            margin-left: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn:hover {
            color: #c82333 !important;
            text-decoration: none;
        }

        .delete-btn i {
            font-size: 16px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor restaurantTitle">Mart Items</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">Mart Items</li>
                </ol>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="container-fluid">
            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">
                                <span class="icon mr-3"><img src="{{ asset('images/food.png') }}"></span>
                                <h3 class="mb-0">{{trans('lang.mart_item_table')}}</h3>
                                <span class="counter ml-3 food_count"></span>
                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="select-box pl-3">
                                    <select class="form-control food_type_selector">
                                        <option value=""  selected>{{trans("lang.type")}}</option>
                                        <option value="veg">{{trans("lang.veg")}}</option>
                                        <option value="non-veg">{{trans("lang.non_veg")}}</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <select class="form-control restaurant_selector">
                                        <option value=""  selected>Mart</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <select class="form-control category_selector">
                                        <option value=""  selected>Mart Categories</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <select class="form-control feature_selector">
                                        <option value=""  selected>Item Features</option>
                                        <option value="spotlight">Spotlight</option>
                                        <option value="steal_of_moment">Steal of Moment</option>
                                        <option value="featured">Featured</option>
                                        <option value="trending">Trending</option>
                                        <option value="new">New Arrival</option>
                                        <option value="best_seller">Best Seller</option>
                                        <option value="seasonal">Seasonal</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <select class="form-control brand_selector">
                                        <option value=""  selected>Brands</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Import Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">Bulk Import Mart Items</h3>
                                <p class="mb-0 text-dark-2">Upload Excel file to import multiple mart items at once</p>
                                <small class="text-info">
                                    <i class="mdi mdi-lightbulb-outline mr-1"></i>
                                    <strong>Tip:</strong> You can use vendor names and category names instead of IDs for easier data entry!
                                </small>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a href="{{ route('mart-items.download-template') }}" class="btn btn-outline-primary rounded-full">
                                        <i class="mdi mdi-download mr-2"></i>Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('mart-items.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="importFile" class="control-label">Select Excel File (.xls/.xlsx)</label>
                                            <input type="file" name="file" id="importFile" accept=".xls,.xlsx" class="form-control" required>
                                            <div class="form-text text-muted">
                                                <i class="mdi mdi-information-outline mr-1"></i>
                                                File should contain: name, price, description, vendorID/vendorName, categoryID/categoryName, subcategoryID/subcategoryName, section, disPrice, publish, nonveg, isAvailable, photo, and optional item features
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary rounded-full">
                                            <i class="mdi mdi-upload mr-2"></i>Import Mart Items
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-list">
                <div class="row">
                    <div class="col-12">
                        <?php if ($id != '') { ?>
                        <div class="menu-tab">
                            <ul>
                                <li>
                                    <a href="{{route('restaurants.view', $id)}}">{{trans('lang.tab_basic')}}</a>
                                </li>
                                <li class="active">
                                    <a href="{{route('marts.mart-items', $id)}}">Mart Items</a>
                                </li>
                                <li>
                                    <a href="{{route('restaurants.orders', $id)}}">{{trans('lang.tab_orders')}}</a>
                                </li>
                                <li>
                                    <a href="{{route('restaurants.coupons', $id)}}">{{trans('lang.tab_promos')}}</a>
                                <li>
                                    <a href="{{route('restaurants.payout', $id)}}">{{trans('lang.tab_payouts')}}</a>
                                </li>
                                <li>
                                    <a
                                        href="{{route('payoutRequests.restaurants.view', $id)}}">{{trans('lang.tab_payout_request')}}</a>
                                </li>
                                <li>
                                    <a href="{{route('restaurants.booktable', $id)}}">{{trans('lang.dine_in_future')}}</a>
                                </li>
                                <li id="restaurant_wallet"></li>
                                <li id="subscription_plan"></li>
                            </ul>
                        </div>
                        <?php } ?>
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <div class="card-header-title">
                                    <h3 class="text-dark-2 mb-2 h4">Mart Items Table</h3>
                                    <p class="mb-0 text-dark-2">Manage all mart items in the system</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                        <?php if ($id != '') { ?>
                                        <a class="btn-primary btn rounded-full"
                                           href="{!! route('mart-items.create') !!}/{{$id}}"><i
                                                class="mdi mdi-plus mr-2"></i>Create Mart Item</a>
                                        <?php } else { ?>
                                        <a class="btn-primary btn rounded-full" href="{!! route('mart-items.create') !!}"><i
                                                class="mdi mdi-plus mr-2"></i>Create Mart Item</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="foodTable"
                                           class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                           cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <?php if (in_array('mart-items.delete', json_decode(@session('user_permissions'), true))) { ?>
                                            <th class="delete-all"><input type="checkbox" id="select-all">
                                                <label class="col-3 control-label" for="select-all">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                            class="mdi mdi-delete"></i> {{trans('lang.all')}}</a>
                                                </label>
                                            </th>
                                            <?php } ?>
                                            <th>Item Name</th>
                                            <th>Price</th>
                                            <th>Discount Price</th>
                                            <?php if ($id == '') { ?>
                                            <th>Mart</th>
                                            <?php } ?>
                                            <th>Mart Categories</th>
                                            <th>Brand</th>
                                            <th>Options</th>
                                            <th>Published</th>
                                            <th>Available</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('scripts')
    <script type="text/javascript">
        // Global variables
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var placeholderImage = '';
        var user_permissions = '<?php echo @session("user_permissions") ?>';
        user_permissions = Object.values(JSON.parse(user_permissions));
        var checkDeletePermission = false;
        
        console.log('🔍 User permissions:', user_permissions);
        if($.inArray('mart-items.delete', user_permissions) >= 0) {
            checkDeletePermission = true;
            console.log('✅ Delete permission granted');
        } else {
            console.log('❌ Delete permission not found');
        }

        var restaurantID = "{{$id}}";
        
        console.log('🔍 Restaurant ID from URL:', restaurantID);
        
        // URL parameters
        const urlParams = new URLSearchParams(location.search);
        var categoryID = '';
        for(const [key, value] of urlParams) {
            if(key == 'categoryID') {
                categoryID = value;
            }
        }

        // Filter state - IMPORTANT: Don't filter by non-existent vendor
        window.selectedVendor = '';  // Always start with no vendor filter
        window.selectedCategory = categoryID || '';
        window.selectedBrand = '';
        window.selectedFoodType = '';
        window.selectedFeature = '';
        
        console.log('🎯 Initial filters:', {
            vendor: window.selectedVendor,
            category: window.selectedCategory,
            brand: window.selectedBrand,
            foodType: window.selectedFoodType,
            feature: window.selectedFeature
        });

        // Set defaults first
        currentCurrency = '₹';
        currencyAtRight = false;
        decimal_degits = 0;
        placeholderImage = '/images/placeholder.png';

        // Load currency settings (async, but with defaults already set)
        $.ajax({
            url: '{{ route("mart-items.currency-settings") }}',
            method: 'GET',
            success: function(data) {
                currentCurrency = data.symbol || '₹';
                currencyAtRight = data.symbolAtRight || false;
                decimal_degits = data.decimal_degits || 0;
                console.log('✅ Currency loaded:', currentCurrency);
            },
            error: function() {
                console.warn('⚠️ Using default currency');
            }
        });

        // Load placeholder image (async, but with default already set)
        $.ajax({
            url: '{{ route("mart-items.placeholder-image") }}',
            method: 'GET',
            success: function(data) {
                if (data.image) {
                    placeholderImage = data.image;
                    console.log('✅ Placeholder loaded:', placeholderImage);
                }
            },
            error: function() {
                console.warn('⚠️ Using default placeholder');
            }
        });

        // Load categories
        $.ajax({
            url: '{{ route("mart-items.categories") }}',
            method: 'GET',
            success: function(categories) {
                console.log('📦 Categories response:', categories);
                if (Array.isArray(categories)) {
                    categories.forEach(function(category) {
                        $('.category_selector').append($("<option></option>")
                            .attr("value", category.id)
                            .text(category.title));
                    });
                    console.log('✅ Loaded ' + categories.length + ' categories');
                } else {
                    console.error('❌ Categories response is not an array:', categories);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error fetching categories:', error);
                console.error('Response:', xhr.responseText);
            }
        });

        // Load brands
        $.ajax({
            url: '{{ route("mart-items.brands") }}',
            method: 'GET',
            success: function(brands) {
                console.log('📦 Brands response:', brands);
                if (Array.isArray(brands)) {
                    brands.forEach(function(brand) {
                        $('.brand_selector').append($("<option></option>")
                            .attr("value", brand.id)
                            .text(brand.name));
                    });
                    console.log('✅ Loaded ' + brands.length + ' brands');
                } else {
                    console.error('❌ Brands response is not an array:', brands);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error fetching brands:', error);
                console.error('Response:', xhr.responseText);
            }
        });

        // Load vendors (mart vendors only)
        $.ajax({
            url: '{{ route("mart-items.vendors") }}',
            method: 'GET',
            success: function(vendors) {
                console.log('📦 Vendors response:', vendors);
                if (Array.isArray(vendors)) {
                    console.log('🔍 Found ' + vendors.length + ' mart vendors');
                    vendors.forEach(function(vendor) {
                        console.log('📋 Mart Vendor:', vendor.title, 'ID:', vendor.id);
                        if (vendor.title != '' && vendor.title != null) {
                            $('.restaurant_selector').append($("<option></option>")
                                .attr("value", vendor.id)
                                .text(vendor.title));
                        }
                    });
                    console.log('✅ Loaded ' + vendors.length + ' vendors');
                } else {
                    console.error('❌ Vendors response is not an array:', vendors);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error fetching vendors:', error);
                console.error('Response:', xhr.responseText);
            }
        });

        <?php if ($id != '') { ?>
        // Load vendor data for tabs (only if valid vendor ID)
        console.log('🏪 Attempting to load vendor data for ID:', '<?php echo $id; ?>');
        
        $.ajax({
            url: '{{ route("vendors.getById", ":id") }}'.replace(':id', '<?php echo $id; ?>'),
            method: 'GET',
            success: function(vendorData) {
                console.log('✅ Vendor data loaded:', vendorData);
                
                if (vendorData && vendorData.vType && (vendorData.vType.toLowerCase() === 'mart' || vendorData.vType === 'Mart')) {
                    // Valid mart vendor - apply filter
                    window.selectedVendor = '<?php echo $id; ?>';
                    console.log('✅ Valid mart vendor found, applying filter:', window.selectedVendor);
                    
                    walletRoute = "{{route('users.walletstransaction', ':id')}}";
                    walletRoute = walletRoute.replace(":id", vendorData.author);
                    $('#restaurant_wallet').append('<a href="' + walletRoute + '">{{trans("lang.wallet_transaction")}}</a>');
                    $('#subscription_plan').append('<a href="' + "{{route('vendor.subscriptionPlanHistory', ':id')}}".replace(':id', vendorData.author) + '">' + '{{trans('lang.subscription_history')}}' + '</a>');
                    
                    // Update page title
                    $('.restaurantTitle').html('{{trans("lang.mart_item_plural")}} - ' + vendorData.title);
                    
                    // Reload table with vendor filter
                    if (typeof $('#foodTable').DataTable === 'function') {
                        $('#foodTable').DataTable().ajax.reload();
                    }
                } else {
                    console.warn('⚠️ Not a mart vendor or vendor not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading vendor:', error, 'Status:', xhr.status);
                console.warn('⚠️ Vendor not found, showing all items instead');
                // Don't apply vendor filter if vendor doesn't exist
                window.selectedVendor = '';
            }
        });
        <?php } ?>
        // Filter change handler
        $('select').change(function() {
            window.selectedVendor = $('.restaurant_selector').val() || '';
            window.selectedFoodType = $('.food_type_selector').val() || '';
            window.selectedCategory = $('.category_selector').val() || '';
            window.selectedFeature = $('.feature_selector').val() || '';
            window.selectedBrand = $('.brand_selector').val() || '';
            
            $('#foodTable').DataTable().ajax.reload();
        });
        $(document).ready(function() {
            console.log('✅ Document ready!');
            console.log('✅ jQuery version:', $.fn.jquery);
            console.log('✅ DataTable available:', typeof $.fn.DataTable);
            console.log('✅ Food table element exists:', $('#foodTable').length > 0);
            
            $('.restaurant_selector').select2({
                placeholder: "Mart",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('.food_type_selector').select2({
                placeholder: "{{trans('lang.type')}}",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('.category_selector').select2({
                placeholder: "Mart Categories",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('.feature_selector').select2({
                placeholder: "Item Features",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('.brand_selector').select2({
                placeholder: "Brands",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('select').on("select2:unselecting", function(e) {
                var self = $(this);
                setTimeout(function() {
                    self.select2('close');
                }, 0);
            });
            $('#category_search_dropdown').hide();
            $(document.body).on('click','.redirecttopage',function() {
                var url=$(this).attr('data-url');
                window.location.href=url;
            });
            jQuery("#data-table_processing").show();
            $(document).on('click', '.dt-button-collection .dt-button', function () {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            });
            $(document).on('click', function (event) {
                if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                    $('.dt-button-collection').hide();
                    $('.dt-button-background').hide();
                }
            });
            var fieldConfig = {
                columns: [
                    { key: 'id', header: "Product ID" },
                    { key: 'foodName', header: "Item Name" },
                    { key: 'vendorID', header: "Mart ID" },
                    { key: 'restaurant', header: "Mart Name" },
                    { key: 'category', header: "Mart Categories" },
                    { key: 'brand', header: "Brand" },
                    { key: 'price', header: "Price" },
                    { key: 'disPrice', header: "Discount Price" },
                ],
                fileName: "Mart Items",
            };
            
            console.log('🎯 About to initialize DataTable on #foodTable');
            console.log('🎯 Table element:', $('#foodTable'));
            console.log('🎯 Current filters:', {
                vendor: window.selectedVendor,
                category: window.selectedCategory
            });
            
            const table=$('#foodTable').DataTable({
                pageLength: 10, // Number of rows per page
                processing: false, // Show processing indicator
                serverSide: true, // Enable server-side processing
                responsive: true,
                ajax: function(data, callback, settings) {
                    console.log('🔄 DataTable AJAX function called');
                    console.log('📊 DataTable data:', data);
                    
                    $('#data-table_processing').show();
                    
                    // Build AJAX request
                    var apiUrl = '{{ route("mart-items.data") }}';
                    console.log('🔗 API URL:', apiUrl);
                    
                    var requestData = {
                        draw: data.draw,
                        start: data.start,
                        length: data.length,
                        'search[value]': data.search.value,
                        'order[0][column]': data.order[0].column,
                        'order[0][dir]': data.order[0].dir,
                        vendor_id: window.selectedVendor,
                        category_id: window.selectedCategory,
                        brand_id: window.selectedBrand,
                        food_type: window.selectedFoodType,
                        feature: window.selectedFeature
                    };
                    
                    console.log('📤 Request data being sent:', requestData);
                    
                    $.ajax({
                        url: apiUrl,
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: requestData,
                        success: function(response) {
                            console.log('📦 DataTable response:', response);
                            $('#data-table_processing').hide();
                            
                            // Check if response has error
                            if (response.error) {
                                console.error('❌ API Error:', response.error);
                                alert('Error loading data: ' + response.error);
                                $('.food_count').text(0);
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    data: []
                                });
                                return;
                            }
                            
                            $('.food_count').text(response.recordsTotal || 0);
                            
                            // Build HTML for each row
                            var records = [];
                            if (Array.isArray(response.data)) {
                                console.log('✅ Response data is an array with', response.data.length, 'items');
                                
                                response.data.forEach(function(item, index) {
                                    console.log('🔨 Building HTML for item', index + 1, ':', item.name);
                                    try {
                                        var html = buildHTML(item);
                                        console.log('✅ HTML built for', item.name, '- columns:', html.length);
                                        records.push(html);
                                    } catch(e) {
                                        console.error('❌ Error building HTML for item:', item.name, e);
                                    }
                                });
                                console.log('✅ Built ' + records.length + ' table rows total');
                            } else {
                                console.error('❌ Response data is not an array:', response.data);
                            }
                            
                            console.log('📤 Calling DataTable callback with', records.length, 'records');
                            
                            callback({
                                draw: response.draw,
                                recordsTotal: response.recordsTotal || 0,
                                recordsFiltered: response.recordsFiltered || 0,
                                data: records
                            });
                            
                            console.log('✅ DataTable callback completed');
                        },
                        error: function(xhr, error, thrown) {
                            console.error("❌ Error fetching data:", error);
                            console.error("Response:", xhr.responseText);
                            console.error("Status:", xhr.status);
                            $('#data-table_processing').hide();
                            $('.food_count').text(0);
                            
                            // Show user-friendly error
                            if (xhr.status === 500) {
                                alert('Server error loading items. Please check the logs.');
                            } else if (xhr.status === 404) {
                                alert('API endpoint not found. Please check routes.');
                            }
                            
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: []
                            });
                        }
                    });
                },
                order: (checkDeletePermission)? [1,'asc']:[0,'asc'],
                columnDefs: [
                    {
                        orderable: false,
                        targets: (restaurantID=='')? ((checkDeletePermission)? [0,7,8]:[6,7]):((checkDeletePermission)? [0,6,7]:[5,7])
                    },
                    {
                        type: 'formatted-num',
                        targets: (checkDeletePermission)? [2,3]:[3,4]
                    }
                ],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}",
                    "processing": "" // Remove default loader
                },
                dom: 'lfrtipB',
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="mdi mdi-cloud-download"></i> Export as',
                        className: 'btn btn-info',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: 'Export Excel',
                                action: function (e, dt, button, config) {
                                    exportData(dt, 'excel',fieldConfig);
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: 'Export PDF',
                                action: function (e, dt, button, config) {
                                    exportData(dt, 'pdf',fieldConfig);
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                text: 'Export CSV',
                                action: function (e, dt, button, config) {
                                    exportData(dt, 'csv',fieldConfig);
                                }
                            }
                        ]
                    }
                ],
                initComplete: function() {
                    $(".dataTables_filter").append($(".dt-buttons").detach());
                    $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password').val('');
                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
            });
            $('#search-input').on('input',debounce(function() {
                const searchValue=$(this).val();
                if(searchValue.length>=3) {
                    $('#data-table_processing').show();
                    table.search(searchValue).draw();
                } else if(searchValue.length===0) {
                    $('#data-table_processing').show();
                    table.search('').draw();
                }
            },300));
        });
        function debounce(func,wait) {
            let timeout;
            const context=this;
            return function(...args) {
                clearTimeout(timeout);
                timeout=setTimeout(() => func.apply(context,args),wait);
            };
        }
        function buildHTML(val) {
            var html=[];
            var imageHtml='';
            var id=val.id;
            var route1='{{route("mart-items.edit", ":id")}}';
            route1=route1.replace(':id',id);
            <?php if ($id != '') { ?>
                route1=route1+'?eid={{$id}}';
            <?php } ?>
            if(val.photo!=''&&val.photo!=null) {
                imageHtml='<img onerror="this.onerror=null;this.src=\''+placeholderImage+'\'" class="rounded" width="100%" style="width:70px;height:70px;" src="'+val.photo+'" alt="image">';
            } else {
                imageHtml='<img width="100%" style="width:70px;height:70px;" src="'+placeholderImage+'" alt="image">';
            }
            if(checkDeletePermission) {
                html.push('<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" name="record" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+
                    'for="is_open_'+id+'" ></label></td>');
            }
            html.push(imageHtml+'<a href="'+route1+'" >'+val.name+'</a>');
            // Original price column - editable
            if(val.disPrice && val.disPrice != '' && val.disPrice != 0 && val.disPrice != val.price) {
                // Has discount - show original price with strikethrough
                if(currencyAtRight) {
                    html.push('<span class="editable-price text-muted" style="text-decoration: line-through; cursor: pointer;" data-id="'+val.id+'" data-field="price" data-value="'+val.price+'">'+parseFloat(val.price).toFixed(decimal_degits)+''+currentCurrency+'</span>');
                } else {
                    html.push('<span class="editable-price text-muted" style="text-decoration: line-through; cursor: pointer;" data-id="'+val.id+'" data-field="price" data-value="'+val.price+'">'+currentCurrency+''+parseFloat(val.price).toFixed(decimal_degits)+'</span>');
                }
                // Show discount price in green - editable
                if(currencyAtRight) {
                    html.push('<span class="editable-price text-green" style="cursor: pointer;" data-id="'+val.id+'" data-field="disPrice" data-value="'+val.disPrice+'">'+parseFloat(val.disPrice).toFixed(decimal_degits)+''+currentCurrency+'</span>');
                } else {
                    html.push('<span class="editable-price text-green" style="cursor: pointer;" data-id="'+val.id+'" data-field="disPrice" data-value="'+val.disPrice+'">'+currentCurrency+''+parseFloat(val.disPrice).toFixed(decimal_degits)+'</span>');
                }
            } else {
                // No discount - show regular price - editable
                if(currencyAtRight) {
                    html.push('<span class="editable-price text-green" style="cursor: pointer;" data-id="'+val.id+'" data-field="price" data-value="'+val.price+'">'+parseFloat(val.price).toFixed(decimal_degits)+''+currentCurrency+'</span>');
                } else {
                    html.push('<span class="editable-price text-green" style="cursor: pointer;" data-id="'+val.id+'" data-field="price" data-value="'+val.price+'">'+currentCurrency+''+parseFloat(val.price).toFixed(decimal_degits)+'</span>');
                }
                // Empty cell where discount price would be - editable
                html.push('<span class="editable-price text-muted" style="cursor: pointer;" data-id="'+val.id+'" data-field="disPrice" data-value="0">-</span>');
            }
            <?php if ($id == '') { ?>
            var restaurantroute='{{route("restaurants.view", ":id")}}';
            restaurantroute=restaurantroute.replace(':id',val.vendorID);
            html.push('<a href="'+restaurantroute+'">'+(val.vendorTitle || '')+'</a>');
            <?php } ?>
            var caregoryroute='{{route("categories.edit", ":id")}}';
            caregoryroute=caregoryroute.replace(':id',val.categoryID);
            html.push('<a href="'+caregoryroute+'">'+(val.categoryTitle || '')+'</a>');
            
            // Add brand display
            if(val.brandTitle && val.brandTitle !== '') {
                html.push('<span class="badge badge-info">'+val.brandTitle+'</span>');
            } else {
                html.push('<span class="text-muted">No Brand</span>');
            }

            // Enhanced Options column
            if(val.has_options && val.options && val.options.length > 0) {
                const optionsCount = val.options_count || val.options.length;
                const priceRange = val.price_range || `₹${val.min_price || 0} - ₹${val.max_price || 0}`;
                const bestValue = val.best_value_option ? 'Best Value' : '';
                const savings = val.savings_percentage ? `${val.savings_percentage.toFixed(1)}% off` : '';

                html.push(`<div class="options-info">
                    <span class="badge badge-info">${optionsCount} Options</span>
                    <br><small class="text-muted">${priceRange}</small>
                    ${bestValue ? `<br><small class="text-success">${bestValue}</small>` : ''}
                    ${savings ? `<br><small class="text-danger">${savings}</small>` : ''}
                </div>`);
            } else {
                html.push('<span class="text-muted">No Options</span>');
            }
            if(val.publish) {
                html.push('<label class="switch"><input type="checkbox" checked id="'+val.id+'" name="isActive"><span class="slider round"></span></label>');
            } else {
                html.push('<label class="switch"><input type="checkbox" id="'+val.id+'" name="isActive"><span class="slider round"></span></label>');
            }
            // Add isAvailable toggle
            if(val.isAvailable) {
                html.push('<label class="switch"><input type="checkbox" checked id="isAvailable_'+val.id+'" name="isAvailable"><span class="slider round"></span></label>');
            } else {
                html.push('<label class="switch"><input type="checkbox" id="isAvailable_'+val.id+'" name="isAvailable"><span class="slider round"></span></label>');
            }
            var actionHtml='';
            actionHtml+='<span class="action-btn"><a href="'+route1+'" class="link-td"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>';
            if(checkDeletePermission) {
                actionHtml+='<a id="'+val.id+'" name="food-delete" href="javascript:void(0)" class="delete-btn" title="Delete Item"><i class="mdi mdi-delete"></i></a>';
            }
            actionHtml+='</span>';
            html.push(actionHtml);
            return html;
        }
        async function checkIfImageExists(url,callback) {
            const img=new Image();
            img.src=url;
            if(img.complete) {
                callback(true);
            } else {
                img.onload=() => {
                    callback(true);
                };
                img.onerror=() => {
                    callback(false);
                };
            }
        }
        $(document).on("click","input[name='isActive']",function(e) {
            var $checkbox = $(this);
            var ischeck = $checkbox.is(':checked');
            var id = this.id;
            
            $.ajax({
                url: '{{ route("mart-items.toggle-publish", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    publish: ischeck,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('✅ Publish status updated successfully');
                    } else {
                        console.error('❌ Failed to update publish status');
                        $checkbox.prop('checked', !ischeck);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error updating publish status:', error);
                    $checkbox.prop('checked', !ischeck);
                }
            });
        });
        // Add isAvailable toggle logic
        $(document).on("click","input[name='isAvailable']",function(e) {
            var $checkbox = $(this);
            var ischeck = $checkbox.is(':checked');
            var id = this.id.replace('isAvailable_','');
            
            $.ajax({
                url: '{{ route("mart-items.toggle-availability", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    isAvailable: ischeck,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('✅ Availability status updated successfully');
                    } else {
                        console.error('❌ Failed to update availability status');
                        $checkbox.prop('checked', !ischeck);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error updating availability status:', error);
                    $checkbox.prop('checked', !ischeck);
                }
            });
        });
        // Delete single item
        $(document).on("click","a[name='food-delete']",function(e) {
            e.preventDefault();
            var id = this.id;
            
            if (!confirm('Are you sure you want to delete this mart item? This action cannot be undone.')) {
                return;
            }
            
            $.ajax({
                url: '{{ route("mart-items.delete", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('✅ Item deleted successfully');
                        window.location.reload();
                    } else {
                        console.error('❌ Failed to delete item');
                        alert('Failed to delete item: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error deleting item:', error);
                    alert('Error deleting item. Please try again.');
                }
            });
        });

        // Select/Deselect all checkboxes
        $('#select-all').change(function() {
            var isChecked=$(this).prop('checked');
            $('input[type="checkbox"][name="record"]').prop('checked',isChecked);
        });
        
        // Bulk delete
        $('#deleteAll').click(function() {
            if (!confirm("{{trans('lang.selected_delete_alert')}}")) {
                return;
            }
            
            var ids = [];
            $('input[type="checkbox"][name="record"]:checked').each(function() {
                ids.push($(this).attr('dataId'));
            });
            
            if (ids.length === 0) {
                alert('Please select at least one item to delete');
                return;
            }
            
            jQuery("#data-table_processing").show();
            
            $.ajax({
                url: '{{ route("mart-items.bulk-delete") }}',
                method: 'POST',
                data: {
                    ids: ids,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    jQuery("#data-table_processing").hide();
                    if (response.success) {
                        console.log('✅ Items deleted successfully');
                        window.location.reload();
                    } else {
                        console.error('❌ Failed to delete items');
                        alert('Failed to delete items: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    jQuery("#data-table_processing").hide();
                    console.error('❌ Error deleting items:', error);
                    alert('Error deleting items. Please try again.');
                }
            });
        });

        // Inline editing functionality for prices - using backend validation
        $(document).on('click', '.editable-price', function() {
            var $this = $(this);
            var currentValue = $this.data('value');
            var field = $this.data('field');
            var id = $this.data('id');

            // Create input field
            var input = $('<input>', {
                type: 'number',
                step: '0.01',
                min: '0',
                class: 'form-control form-control-sm',
                value: currentValue,
                style: 'width: 80px; display: inline-block;'
            });

            // Replace span with input
            $this.hide();
            $this.after(input);
            input.focus();

            // Handle save on enter or blur
            function saveValue() {
                var newValue = parseFloat(input.val());
                if (isNaN(newValue) || newValue < 0) {
                    newValue = 0;
                }

                // Remove input and show span
                input.remove();
                $this.show();

                // Show loading indicator
                $this.addClass('text-info');
                $this.text('Updating...');

                // Send AJAX request to backend for proper validation and data consistency
                $.ajax({
                    url: '{{ route("mart-items.inlineUpdate", ":id") }}'.replace(':id', id),
                    method: 'PATCH',
                    data: {
                        field: field,
                        value: newValue,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the data attribute
                            $this.data('value', newValue);

                            // Update the display
                            var displayValue = newValue.toFixed(decimal_degits);
                            if (currencyAtRight) {
                                $this.text(displayValue + currentCurrency);
                            } else {
                                $this.text(currentCurrency + displayValue);
                            }

                            // Show success indicator
                            $this.removeClass('text-info').addClass('text-success');
                            setTimeout(function() {
                                $this.removeClass('text-success');
                            }, 1000);

                            // If there's a message about discount price being reset, show it
                            if (response.message && response.message.includes('discount price was reset')) {
                                // Find and update the discount price cell if it exists
                                var discountCell = $this.closest('tr').find('.editable-price[data-field="disPrice"]');
                                if (discountCell.length > 0) {
                                    discountCell.data('value', 0);
                                    discountCell.text('-');
                                    discountCell.removeClass('text-green').addClass('text-muted');
                                }
                            }
                        } else {
                            // Show error message
                            alert('Update failed: ' + response.message);
                            // Revert to original value
                            var originalValue = currentValue;
                            var displayValue = originalValue.toFixed(decimal_degits);
                            if (currencyAtRight) {
                                $this.text(displayValue + currentCurrency);
                            } else {
                                $this.text(currentCurrency + displayValue);
                            }
                            $this.removeClass('text-info');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Update failed';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);

                        // Revert to original value
                        var originalValue = currentValue;
                        var displayValue = originalValue.toFixed(decimal_degits);
                        if (currencyAtRight) {
                            $this.text(displayValue + currentCurrency);
                        } else {
                            $this.text(currentCurrency + displayValue);
                        }
                        $this.removeClass('text-info');
                    }
                });
            }

            input.on('blur', saveValue);
            input.on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    saveValue();
                }
            });

            // Handle escape key
            input.on('keydown', function(e) {
                if (e.which === 27) { // Escape key
                    input.remove();
                    $this.show();
                }
            });
        });

    </script>
@endsection
