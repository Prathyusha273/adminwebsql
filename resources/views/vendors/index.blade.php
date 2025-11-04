@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                @if(request()->is('vendors/approved'))
                @php $type = 'approved'; @endphp
                {{trans('lang.approved_vendors')}}
                @elseif(request()->is('vendors/pending'))
                @php $type = 'pending'; @endphp
                {{trans('lang.approval_pending_vendors')}}
                @else
                @php $type = 'all'; @endphp
                {{trans('lang.all_vendors')}}
                @endif
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.vendor_list')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/vendor.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.vendor_list')}}</h3>
                            <span class="counter ml-3 vendor_count"></span>
                        </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3">
                                <select class="form-control vendor_type_selector filteredRecords">
                                    <option value="" selected>{{trans("lang.vendor_type")}}</option>
                                    <option value="restaurant">{{trans("lang.restaurant")}}</option>
                                    <option value="mart">{{trans("lang.mart")}}</option>
                                </select>
                            </div>
                            <div class="select-box pl-3">
                                <select class="form-control status_selector filteredRecords">
                                    <option value="" selected>{{trans("lang.status")}}</option>
                                    <option value="active">{{trans("lang.active")}}</option>
                                    <option value="inactive">{{trans("lang.in_active")}}</option>
                                </select>
                            </div>
                            <div class="select-box pl-3" style="display:block !important;">
                                <select class="form-control zone_selector filteredRecords" style="display:block !important;">
                                    <option value="" selected>{{trans("lang.select_zone")}}</option>
                                </select>
                            </div>
                            <div class="select-box pl-3" style="display:none !important;">
                                <select class="form-control zone_sort_selector filteredRecords" style="display:block !important;">
                                    <option value="" selected>{{trans("lang.sort_by_zone")}}</option>
                                    <option value="asc">{{trans("lang.zone_asc")}}</option>
                                    <option value="desc">{{trans("lang.zone_desc")}}</option>
                                </select>
                            </div>
                            <div class="select-box pl-3">
                            <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card border">
            <div class="card-header d-flex justify-content-between align-items-center border-0">
                <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">Bulk Import Vendors</h3>
                    <p class="mb-0 text-dark-2">Upload Excel file to import multiple vendors at once</p>
                </div>
                <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <a href="{{ route('vendors.download-template') }}" class="btn btn-outline-primary rounded-full">
                            <i class="mdi mdi-download mr-2"></i>Download Template
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('vendors.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="importFile" class="control-label">Select Excel File (.xls/.xlsx)</label>
                                <input type="file" name="file" id="importFile" accept=".xls,.xlsx" class="form-control" required>
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-information-outline mr-1"></i>
                                    File should contain: firstName, lastName, email, password, active, profilePictureURL, zoneId, phoneNumber, createdAt
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary rounded-full">
                                <i class="mdi mdi-upload mr-2"></i>Import Vendors
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
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.vendor_list')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.vendors_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! route('vendors.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.create_vendor')}}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="userTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (
                                                ($type == "approved" && in_array('approve.vendors.delete', json_decode(@session('user_permissions'), true))) ||
                                                ($type == "pending" && in_array('pending.vendors.delete', json_decode(@session('user_permissions'), true))) ||
                                                ($type == "all" && in_array('vendors.delete', json_decode(@session('user_permissions'), true)))
                                            ) { ?>
                                                <th class="delete-all"><input type="checkbox" id="is_active">
                                                    <label class="col-3 control-label" for="is_active">
                                                        <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a>
                                                    </label>
                                                </th>
                                            <?php } ?>
                                            <th>{{trans('lang.vendor_info')}}</th>
                                            <th>{{trans('lang.email')}}</th>
                                            <th>{{trans('lang.phone_number')}}</th>
                                            <th>{{trans('lang.zone')}}</th>
                                            <th>{{trans('lang.vendor_type')}}</th>
                                            <th>{{trans('lang.current_plan')}}</th>
                                            <th>{{trans('lang.expiry_date')}}</th>
                                            <th>{{trans('lang.date')}}</th>
                                            <th>{{trans('lang.document_plural')}}</th>
                                            <th>{{trans('lang.active')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                    </tbody>
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
    var type="{{$type}}";
    var user_permissions='<?php echo @session("user_permissions") ?>';
    user_permissions=Object.values(JSON.parse(user_permissions));
    var checkDeletePermission=false;
    if(
        (type=='pending'&&$.inArray('pending.vendors.delete',user_permissions)>=0)||
        (type=='approved'&&$.inArray('approve.vendors.delete',user_permissions)>=0)||
        (type=='all'&&$.inArray('vendors.delete',user_permissions)>=0)
    ) {
        checkDeletePermission=true;
    }

    var placeholderImage='';
    var zoneIdToName = {};

    // Load placeholder image from SQL
    placeholderImage = '{{ asset('images/placeholder.png') }}';

    // Load zones from SQL
    console.log('🌍 Loading zones from SQL...');
    $.ajax({
        url: '{{ route("vendors.zones") }}',
        method: 'GET',
        success: function(response) {
            console.log('✅ Zones loaded from SQL:', response.data.length);
            if (response.success && response.data) {
                response.data.forEach(function(zone) {
                    $('.zone_selector').append($("<option></option>")
                        .attr("value", zone.id)
                        .text(zone.name));
                    zoneIdToName[zone.id] = zone.name;
                    console.log('📍 Zone:', zone.name, 'ID:', zone.id);
                });
                window.zoneIdToName = zoneIdToName;
                console.log('🗺️ Zone map created:', zoneIdToName);

                // Initialize DataTable after zones are loaded
                console.log('🚀 Initializing vendor DataTable...');
                initializeVendorDataTable();
            }
        },
        error: function(error) {
            console.error('❌ Error fetching zones:', error);
            // Still initialize DataTable even if zones fail
            console.log('🚀 Initializing vendor DataTable (without zones)...');
            initializeVendorDataTable();
        }
    });

    // Move DataTable initialization into a function
    function initializeVendorDataTable() {
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
                { key: 'fullName', header: "{{trans('lang.vendor_info')}}" },
                { key: 'email', header: "{{trans('lang.email')}}" },
                { key: 'phoneNumber', header: "{{trans('lang.phone_number')}}" },
                { key: 'activePlanName', header: "{{trans('lang.active_subscription_plan')}}" },
                { key: 'subscriptionExpiryDate', header: "{{trans('lang.plan_expire_at')}}" },
                { key: 'createdAt', header: "{{trans('lang.created_at')}}" },
            ],
            fileName: "{{trans('lang.vendor_list')}}",
        };

        try {
            const table=$('#userTable').DataTable({
                pageLength: 10,
                processing: false,
                serverSide: true,
                responsive: true,
                searching: true,
                info: true,
                paging: true,
                ajax: function(data,callback,settings) {
                    const start=data.start;
                    const length=data.length;
                    const searchValue=data.search.value.toLowerCase();

                    if(searchValue.length>=3||searchValue.length===0) {
                        $('#data-table_processing').show();
                    }

                    // Build request data
                    var requestData = {
                        start: start,
                        length: length,
                        draw: data.draw,
                        search: { value: searchValue },
                        type: type,
                        vendor_type: window.selectedVendorType || '',
                        status: $('.status_selector').val() || '',
                        zone: window.selectedZone || '',
                        zone_sort: window.selectedZoneSort || ''
                    };

                    // Add date range if selected
                    var daterangepicker = $('#daterange').data('daterangepicker');
                    if ($('#daterange span').html() != '{{trans("lang.select_range")}}' && daterangepicker) {
                        requestData.start_date = daterangepicker.startDate.format('YYYY-MM-DD');
                        requestData.end_date = daterangepicker.endDate.format('YYYY-MM-DD');
                    }

                    // Fetch vendors from SQL database
                    console.log('🔍 Starting vendor data fetch from SQL...');
                    $.ajax({
                        url: '{{ route("vendors.data") }}',
                        method: 'GET',
                        data: requestData,
                        success: function(response) {
                            console.log('📊 Found', response.data.length, 'vendors from SQL');

                            if(response.data.length === 0) {
                                $('.vendor_count').text(0);
                                console.log("No vendors found in SQL database.");
                                $('#data-table_processing').hide();
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    data: []
                                });
                                return;
                            }

                            let records = [];

                            // Process each vendor
                            response.data.forEach(function(vendor) {
                                var rowData = buildHTML(vendor);
                                records.push(rowData);
                            });

                            $('.vendor_count').text(response.vendor_count);
                            $('#data-table_processing').hide();

                            callback({
                                draw: data.draw,
                                recordsTotal: response.recordsTotal,
                                recordsFiltered: response.recordsFiltered,
                                data: records
                            });
                        },
                        error: function(error) {
                            console.error("❌ Error fetching data from SQL:", error);
                            $('#data-table_processing').hide();
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: []
                            });
                        }
                    });
                },
                order: [],
                columnDefs: [
                    {
                        targets: '_all',
                        defaultContent: '',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            if (data === undefined || data === null) {
                                return '';
                            }
                            return data;
                        }
                    }
                ],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}",
                    "processing": ""
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
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable error:', error);
                    console.error('Error details:', thrown);
                    $('#data-table_processing').hide();
                }
            });
        } catch (error) {
            console.error('❌ DataTable initialization error:', error);
            $('#data-table_processing').hide();
            $('#userTable').html('<div class="alert alert-danger">Error loading vendor data. Please refresh the page.</div>');
        }

        function debounce(func,wait) {
            let timeout;
            const context=this;
            return function(...args) {
                clearTimeout(timeout);
                timeout=setTimeout(() => func.apply(context,args),wait);
            };
        }

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
    }

    $('.vendor_type_selector').select2({
        placeholder: '{{trans("lang.vendor_type")}}',
        minimumResultsForSearch: Infinity,
        allowClear: true
    });
    $('.status_selector').select2({
        placeholder: '{{trans("lang.status")}}',
        minimumResultsForSearch: Infinity,
        allowClear: true
    });

    $('.zone_selector').select2({
        placeholder: '{{trans("lang.select_zone")}}',
        minimumResultsForSearch: Infinity,
        allowClear: true
    });
    $('.zone_sort_selector').select2({
        placeholder: '{{trans("lang.sort_by_zone")}}',
        minimumResultsForSearch: Infinity,
        allowClear: true
    });
    $('select').on("select2:unselecting", function(e) {
        var self = $(this);
        setTimeout(function() {
            self.select2('close');
        }, 0);
    });

    function setDate() {
        $('#daterange span').html('{{trans("lang.select_range")}}');
        $('#daterange').daterangepicker({
            autoUpdateInput: false,
        }, function (start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('.filteredRecords').trigger('change');
        });
        $('#daterange').on('apply.daterangepicker', function (ev, picker) {
            $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
            $('.filteredRecords').trigger('change');
        });
        $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
            $('#daterange span').html('{{trans("lang.select_range")}}');
            $('.filteredRecords').trigger('change');
        });
    }
    setDate();

    // Initialize filter variables
    window.selectedVendorType = '';
    window.selectedZone = '';
    window.selectedZoneSort = '';

    $('.filteredRecords').change(async function() {
        var status = $('.status_selector').val();
        var vendorType = $('.vendor_type_selector').val();
        var zone = $('.zone_selector').val();
        var zoneSort = $('.zone_sort_selector').val();

        console.log('🔍 Filter change detected:');
        console.log('  Status:', status);
        console.log('  Vendor Type:', vendorType);
        console.log('  Zone:', zone);
        console.log('  Zone Sort:', zoneSort);

        // Store filters for use in ajax function
        window.selectedVendorType = vendorType;
        window.selectedZone = zone;
        window.selectedZoneSort = zoneSort;

        // Reload the DataTable with new filters
        $('#userTable').DataTable().ajax.reload();
    });

    function buildHTML(val) {
        var html=[];
        var id=val.id;
        var route1='{{route("vendor.edit", ":id")}}';
        route1=route1.replace(':id',id);
        var trroute1='{{route("users.walletstransaction", ":id")}}';
        trroute1=trroute1.replace(':id',id);

        // Column 0: Delete checkbox (if permission exists)
        if(checkDeletePermission) {
            html.push('<input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'" data-vendorid="'+val.vendorID+'"><label class="col-3 control-label" for="is_open_'+id+'"></label>');
        }

        // Column 1: Vendor Info (image + name)
        var imageHtml = '';
        if(!val.profilePictureURL || val.profilePictureURL=='') {
            imageHtml='<img width="100%" style="width:70px;height:70px;" src="'+placeholderImage+'" alt="image">';
        } else {
           imageHtml='<img onerror="this.onerror=null;this.src=\''+placeholderImage+'\'" class="rounded" width="100%" style="width:70px;height:70px;" src="'+val.profilePictureURL+'" alt="image">';
        }

        if(val.fullName) {
            html.push(imageHtml+'<a  href="'+route1+'">'+val.fullName+'</a>');
        } else {
            html.push(imageHtml);
        }

        // Column 2: Email
        html.push(val.email || "");

        // Column 3: Phone Number
        html.push(val.phoneNumber || "");

        // Column 4: Zone
        if(val.zoneId && window.zoneIdToName && window.zoneIdToName[val.zoneId]) {
            html.push(window.zoneIdToName[val.zoneId]);
        } else {
            html.push('<span class="text-muted">No Zone</span>');
        }

        // Column 5: Vendor Type
        if(val.vType) {
            html.push(val.vType.charAt(0).toUpperCase() + val.vType.slice(1));
        } else {
            html.push('<span class="text-primary">Restaurant</span>');
        }

        // Column 6: Current Plan
        if(val.subscription_plan && val.subscription_plan.name) {
            html.push(val.subscription_plan.name);
        } else {
            html.push('');
        }

        // Column 7: Expiry Date
        if(val.subscriptionExpiryDate) {
            html.push(val.subscriptionExpiryDate);
        } else {
            html.push('');
        }

        // Column 8: Date
        if(val.createdAt) {
            html.push('<span class="dt-time">'+val.createdAt+'</span>');
        } else {
            html.push('');
        }

        // Column 9: Documents
        var document_list_view="{{route('vendors.document', ':id')}}";
        document_list_view=document_list_view.replace(':id',val.id);
        html.push('<a href="'+document_list_view+'"><i class="fa fa-file"></i></a>');

        // Column 10: Active
        if(val.active) {
            html.push('<label class="switch"><input type="checkbox" checked id="'+val.id+'" name="isActive"><span class="slider round"></span></label>');
        } else {
            html.push('<label class="switch"><input type="checkbox" id="'+val.id+'" name="isActive"><span class="slider round"></span></label>');
        }

        // Column 11: Actions
        var action='<span class="action-btn">';
        var planRoute="{{route('vendor.subscriptionPlanHistory',':id')}}";
        planRoute=planRoute.replace(':id',val.id);
        if(val.subscription_plan) {
            action+='<a id="'+val.id+'"  href="'+planRoute+'"><i class="mdi mdi-crown"></i></a>';
        }
        action+='<a id="'+val.id+'"  href="'+route1+'"><i class="mdi mdi-lead-pencil"></i></a>';
        if(checkDeletePermission) {
            action=action+'<a id="'+val.id+'" data-vendorid="'+val.vendorID+'" class="delete-btn" name="vendor-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        action=action+'</span>';
        html.push(action);

        // Ensure we always return exactly the right number of columns
        const expectedColumns = checkDeletePermission ? 12 : 11;
        if (html.length !== expectedColumns) {
            console.error('❌ Column count mismatch! Expected:', expectedColumns, 'Got:', html.length);
            while (html.length < expectedColumns) {
                html.push('');
            }
            if (html.length > expectedColumns) {
                html = html.slice(0, expectedColumns);
            }
        }

        return html;
    }

    $("#is_active").click(function() {
        $("#userTable .is_open").prop('checked',$(this).prop('checked'));
    });

    $("#deleteAll").click(async function() {
        if($('#userTable .is_open:checked').length) {
            if(confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();

                $('#userTable .is_open:checked').each(function() {
                    var dataId=$(this).attr('dataId');

                    // Delete vendor via AJAX
                    $.ajax({
                        url: '/vendors/' + dataId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ Vendor deleted successfully');
                        },
                        error: function(error) {
                            console.error('❌ Error deleting vendor:', error);
                        }
                    });
                });

                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    $(document).on("click","a[name='vendor-delete']",async function(e) {
        var id=this.id;

        if(confirm("Are you sure you want to delete this vendor?")) {
            jQuery("#data-table_processing").show();

            $.ajax({
                url: '/vendors/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('✅ Vendor deleted successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(error) {
                    console.error('❌ Error deleting vendor:', error);
                    jQuery("#data-table_processing").hide();
                    alert('Error deleting vendor. Please try again.');
                }
            });
        }
    });

    $(document).on("click","input[name='isActive']",async function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;

        jQuery("#data-table_processing").show();

        $.ajax({
            url: '/vendors/' + id + '/toggle-status',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('✅ Vendor status updated successfully');
                jQuery("#data-table_processing").hide();
            },
            error: function(error) {
                console.error('❌ Error updating vendor status:', error);
                jQuery("#data-table_processing").hide();
                // Revert checkbox state
                $(this).prop('checked', !ischeck);
            }
        });
    });
</script>
@endsection

