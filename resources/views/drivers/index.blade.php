@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                @if(request()->is('drivers/approved'))
                @php $type = 'approved'; @endphp
                {{trans('lang.approved_drivers')}}
                @elseif(request()->is('drivers/pending'))
                @php $type = 'pending'; @endphp
                {{trans('lang.approval_pending_drivers')}}
                @else
                @php $type = 'all'; @endphp
                {{trans('lang.all_drivers')}}
                @endif
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.driver_table')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/driver.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.driver_table')}}</h3>
                            <span class="counter ml-3 driver_count"></span>
                        </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3">
                                <select class="form-control status_selector filteredRecords">
                                    <option value="" selected>{{trans("lang.status")}}</option>
                                    <option value="active">{{trans("lang.active")}}</option>
                                    <option value="inactive">{{trans("lang.in_active")}}</option>
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
        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.driver_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.drivers_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! route('drivers.create') !!}"><i
                                            class="mdi mdi-plus mr-2"></i>{{trans('lang.drivers_create')}}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="driverTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (($type == "approved" && in_array('approve.driver.delete', json_decode(@session('user_permissions'), true))) || ($type == "pending" && in_array('pending.driver.delete', json_decode(@session('user_permissions'), true))) || ($type == "all" && in_array('driver.delete', json_decode(@session('user_permissions'), true)))) { ?>
                                                <th class="delete-all">
                                                    <input type="checkbox" id="is_active">
                                                    <label class="col-3 control-label" for="is_active">
                                                        <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a>
                                                    </label>
                                                </th>
                                            <?php } ?>
                                            <th>{{trans('lang.user_name')}}</th>
                                            <th>{{trans('lang.email')}}</th>
                                            <th>{{trans('lang.phone_number')}}</th>
                                            <th>{{trans('lang.date')}}</th>
                                            <th>{{trans('lang.document_plural')}}</th>
                                            <th>{{trans('lang.driver_active')}}</th>
                                            <th>{{trans('lang.driver_online')}}</th>
                                            <th>{{trans('lang.wallet_history')}}</th>
                                            <th>{{trans('lang.dashboard_total_orders')}}</th>
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
    var type="{{$type}}";
    var append_list='';
    var placeholderImage='';
    var user_permissions='<?php echo @session("user_permissions") ?>';
    user_permissions=Object.values(JSON.parse(user_permissions));
    var checkDeletePermission=false;
    if(
        (type=='pending'&&$.inArray('pending.driver.delete',user_permissions)>=0)||
        (type=='approved'&&$.inArray('approve.driver.delete',user_permissions)>=0)||
        (type=='all'&&$.inArray('driver.delete',user_permissions)>=0)
    ) {
        checkDeletePermission=true;
    }

    // Load placeholder image from SQL
    placeholderImage = '{{ asset('images/placeholder.png') }}';

    $('.status_selector').select2({
        placeholder: "{{trans('lang.select_status')}}",
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
    $('.filteredRecords').change(async function() {
        $('#driverTable').DataTable().ajax.reload();
    });
    $(document).ready(function() {
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
                { key: 'fullName', header: "{{trans('lang.user_name')}}" },
                { key: 'email', header: "{{trans('lang.email')}}" },
                { key: 'phoneNumber', header: "{{trans('lang.phone_number')}}" },
                { key: 'active', header: "{{trans('lang.driver_active')}}" },
                { key: 'createdAt', header: "{{trans('lang.created_at')}}" },
            ],
            fileName: "{{trans('lang.driver_table')}}",
        };
        const table=$('#driverTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: function(data,callback,settings) {
                const start=data.start;
                const length=data.length;
                const searchValue=data.search.value;

                // Get filter values
                var status=$('.status_selector').val();
                var daterangepicker = $('#daterange').data('daterangepicker');
                var startDate = '';
                var endDate = '';

                if ($('#daterange span').html() != '{{trans("lang.select_range")}}' && daterangepicker) {
                    startDate = moment(daterangepicker.startDate).format('YYYY-MM-DD');
                    endDate = moment(daterangepicker.endDate).format('YYYY-MM-DD');
                }

                // Determine isDocumentVerify filter based on type
                var isDocumentVerify = '';
                if(type == 'pending') {
                    isDocumentVerify = '0';
                } else if(type == 'approved') {
                    isDocumentVerify = '1';
                }

                if(searchValue.length>=3||searchValue.length===0) {
                    $('#data-table_processing').show();
                }

                // AJAX call to SQL backend
                $.ajax({
                    url: '{{route("drivers.data")}}',
                    type: 'GET',
                    data: {
                        draw: data.draw,
                        start: start,
                        length: length,
                        search: { value: searchValue },
                        isActive: status,
                        isDocumentVerify: isDocumentVerify,
                        startDate: startDate,
                        endDate: endDate
                    },
                    success: function(response) {
                        $('#data-table_processing').hide();

                        if(response.stats) {
                            $('.driver_count').text(response.stats.total);
                        }

                        let records=[];
                        response.data.forEach(function(childData) {
                            var id=childData.firebase_id || childData.id;
                            var fullName = (childData.firstName || '') + ' ' + (childData.lastName || '');
                            var route1='{{route("drivers.edit", ":id")}}';
                            route1=route1.replace(':id',id);
                            var driverView='{{route("drivers.view", ":id")}}';
                            driverView=driverView.replace(':id',id);
                            document_list_view="{{route('drivers.document', ':id')}}";
                            document_list_view=document_list_view.replace(':id',id);
                            var trroute2='{{route("orders", ":id")}}';
                            trroute2=trroute2.replace(':id','driverId='+id);
                            var walletTransactions='{{route("users.walletstransaction", ":id")}}';
                            walletTransactions=walletTransactions.replace(':id',id);

                            // Format date from SQL response
                            var createdAt = childData.createdAt || '';

                            var driverImage=childData.profilePictureURL == '' || childData.profilePictureURL == null ? '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">' : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + childData.profilePictureURL + '" alt="image">'
                            var shortedEmail = shortEmail(childData.email || '');

                            records.push([
                                checkDeletePermission? '<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+'for="is_open_'+id+'" ></label></td>':'',
                                driverImage+'<a href="'+driverView+'" class="redirecttopage">'+fullName+'</a>',
                                shortedEmail,
                                childData.phoneNumber? childData.phoneNumber:' ',
                                createdAt,
                                '<a href="'+document_list_view+'"><i class="fa fa-file"></i></a>',
                                childData.active? '<label class="switch"><input type="checkbox" checked id="'+id+'" name="isActive"><span class="slider round"></span></label>':'<label class="switch"><input type="checkbox" id="'+id+'" name="isActive"><span class="slider round"></span></label>',
                                childData.isActive? '<label class="switch"><input type="checkbox" checked id="'+id+'" name="isOnline"><span class="slider round"></span></label>':'<label class="switch"><input type="checkbox" id="'+id+'" name="isOnline"><span class="slider round"></span></label>',
                                '<a href="'+walletTransactions+'">{{trans("lang.wallet_history")}}</a>',
                                '<a href="'+trroute2+'">0</a>',
                                '<span class="action-btn"><a href="'+driverView+'"><i class="mdi mdi-eye"></i></a><a href="'+route1+'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a><?php if (in_array('drivers.edit', json_decode(@session('user_permissions'), true))) { ?> <a id="'+id+'" name="clear-order-request-data" class="clear-order-data-btn" href="javascript:void(0)" title="Clear restaurantorders Request Data"><i class="mdi mdi-refresh"></i></a><?php } ?><?php if (in_array('driver.delete', json_decode(@session('user_permissions'), true))) { ?> <a id="'+id+'" name="driver-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a><?php } ?></span>'
                            ]);
                        });

                        callback({
                            draw: response.draw,
                            recordsTotal: response.recordsTotal,
                            recordsFiltered: response.recordsFiltered,
                            data: records
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data from SQL:",error);
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
            order: (checkDeletePermission)? [4,'desc'] : [3,'desc'],
            columnDefs: [
                {
                    targets: (checkDeletePermission)? 4:3,
                    type: 'date',
                    render: function(data) {
                        return data;
                    }
                },
                {orderable: false,targets: (checkDeletePermission)? [0,5,6,7,8,9,10]:[4,5,6,7,8,9]},
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

                // Add clear all order request data button beside search
                var clearAllButton = '<button id="clearAllOrderRequestData" class="btn btn-warning ml-2 rounded-full"><i class="mdi mdi-refresh mr-1"></i>Clear All restaurantorders Request Data</button>';
                $(".dataTables_filter").append(clearAllButton);

                $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password').val('');
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3;
                }).remove();
            }
        });
        table.columns.adjust().draw();
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
    });
    $(document).on("click","input[name='isOnline']",async function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;
        var switchElement=$(this);

        if(ischeck) {
            // Check if driver exists and is verified via SQL
            $.ajax({
                url: '/drivers/' + id + '/data',
                type: 'GET',
                success: function(response) {
                    if(response.success && response.data) {
                        if(!response.data.isDocumentVerify) {
                            switchElement.prop('checked',false);
                            alert('{{trans("lang.document_verification_is_pending")}}');
                            return false;
                        } else {
                            // Update isActive via SQL
                            $.ajax({
                                url: '/drivers/' + id,
                                type: 'PUT',
                                data: {
                                    isActive: true,
                                    _token: '{{csrf_token()}}'
                                },
                                success: function(response) {
                                    console.log('Driver set to online');
                                }
                            });
                        }
                    }
                }
            });
        } else {
            // Update isActive to false via SQL
            $.ajax({
                url: '/drivers/' + id,
                type: 'PUT',
                data: {
                    isActive: false,
                    _token: '{{csrf_token()}}'
                },
                success: function(response) {
                    console.log('Driver set to offline');
                }
            });
        }
    });
    $(document).on("click","input[name='isActive']",async function(e) {
        jQuery("#data-table_processing").show();
        var ischeck=$(this).is(':checked');
        var id=this.id;

        // Toggle driver active status via SQL
        $.ajax({
            url: '/drivers/' + id + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}'
            },
            success: function(response) {
                if(response.success) {
                    console.log('Driver status toggled successfully');
                    if (typeof logActivity === 'function') {
                        var action = response.active ? 'activated' : 'deactivated';
                        logActivity('drivers', action, action.charAt(0).toUpperCase() + action.slice(1) + ' driver: ' + id);
                    }
                }
                jQuery("#data-table_processing").hide();
            },
            error: function() {
                console.error('Error toggling driver status');
                jQuery("#data-table_processing").hide();
            }
        });
    });
    $("#is_active").click(function() {
        $("#driverTable .is_open").prop('checked',$(this).prop('checked'));
    });
    $("#deleteAll").click(async function() {
        if($('#driverTable .is_open:checked').length) {
            if(confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                var deletePromises = [];

                $('#driverTable .is_open:checked').each(function() {
                    var dataId=$(this).attr('dataId');
                    deletePromises.push(
                        $.ajax({
                            url: '/drivers/' + dataId,
                            type: 'DELETE',
                            data: {
                                _token: '{{csrf_token()}}'
                            }
                        })
                    );
                });

                Promise.all(deletePromises).then(function() {
                    if (typeof logActivity === 'function') {
                        logActivity('drivers', 'bulk_deleted', 'Bulk deleted ' + deletePromises.length + ' drivers');
                    }
                    window.location.reload();
                }).catch(function(error) {
                    console.error('Error deleting drivers:', error);
                    jQuery("#data-table_processing").hide();
                    alert('Error deleting some drivers');
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
    $(document.body).on('click','.redirecttopage',function() {
        var url=$(this).attr('data-url');
        window.location.href=url;
    });
    $(document).on("click","a[name='driver-delete']",async function(e) {
        var id=this.id;

        if(confirm("{{trans('lang.delete_confirmation')}}")) {
            jQuery("#data-table_processing").show();

            $.ajax({
                url: '/drivers/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{csrf_token()}}'
                },
                success: function(response) {
                    if(response.success) {
                        if (typeof logActivity === 'function') {
                            logActivity('drivers', 'deleted', 'Deleted driver: ' + id);
                        }
                        window.location.reload();
                    } else {
                        alert('Error deleting driver: ' + (response.message || 'Unknown error'));
                        jQuery("#data-table_processing").hide();
                    }
                },
                error: function() {
                    alert('Error deleting driver');
                    jQuery("#data-table_processing").hide();
                }
            });
        }
    });
    function searchclear() {
        jQuery("#search").val('');
        searchtext();
    }

    // Handle clear order request data button click
    $(document).on("click", "a[name='clear-order-request-data']", async function(e) {
        e.preventDefault();
        var driverId = this.id;

        // Show confirmation dialog
        if (confirm('Clear orderRequestData for driver?\n\nNote: This only clears the orderRequestData array used for requests. It will NOT delete any orders or change Total Orders.')) {
            jQuery("#data-table_processing").show();

            try {
                // Clear order request data via SQL
                $.ajax({
                    url: '/drivers/' + driverId + '/clear-order-request-sql',
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}'
                    },
                    success: function(response) {
                        if(response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }
                        } else {
                            alert('Error: ' + (response.message || 'Unknown error'));
                        }
                        jQuery("#data-table_processing").hide();
                        $('#driverTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        alert('Error clearing order request data');
                        jQuery("#data-table_processing").hide();
                    }
                });

                if (typeof toastr !== 'undefined') {
                    toastr.success('restaurantorders request data cleared for ' + driverName);
                } else {
                    alert('restaurantorders request data cleared for ' + driverName);
                }

                // Log activity if function exists
                if (typeof logActivity === 'function') {
                    logActivity('drivers', 'clear_order_request_data', 'Cleared order request data for driver: ' + driverName);
                }
            } catch (error) {
                console.warn('Frontend clear failed, falling back to backend route:', error);
                // Fallback to backend route if frontend update fails
                $.ajax({
                    url: '{{ route("drivers.clearOrderRequestData", ":id") }}'.replace(':id', driverId),
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }
                            if (typeof logActivity === 'function') {
                                logActivity('drivers', 'clear_order_request_data', 'Cleared order request data for driver: ' + driverName);
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert('Error: ' + response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error clearing order request data (backend):', error);
                        var errorMessage = 'An error occurred while clearing order request data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    },
                    complete: function() {
                        jQuery("#data-table_processing").hide();
                    }
                });
            }
            jQuery("#data-table_processing").hide();
        }
    });

    // Handle clear all order request data button click
    $(document).on("click", "#clearAllOrderRequestData", async function(e) {
        e.preventDefault();

        // Show confirmation dialog
        if (confirm('Are you sure you want to clear orderRequestData for ALL drivers?\n\nThis action will:\n- Clear orderRequestData array for every driver\n- NOT delete any actual orders\n- NOT change Total Orders counts\n- Cannot be undone\n\nType "YES" to confirm:')) {
            // var userInput = prompt('Type "YES" to confirm clearing all drivers order request data:');
            // if (userInput !== 'YES') {
            //     return;
            // }

            jQuery("#data-table_processing").show();

            try {
                // Make AJAX call to clear all drivers order request data
                $.ajax({
                    url: '{{ route("drivers.clearAllOrderRequestData") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }

                            // Log activity if function exists
                            if (typeof logActivity === 'function') {
                                logActivity('drivers', 'clear_all_order_request_data', 'Cleared order request data for ' + response.cleared_count + ' drivers');
                            }

                            // Reload the table to reflect changes
                            $('#driverTable').DataTable().ajax.reload();
                        } else {
                            // Show error message
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert('Error: ' + response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error clearing all drivers order request data:', error);
                        var errorMessage = 'An error occurred while clearing all drivers order request data.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    },
                    complete: function() {
                        jQuery("#data-table_processing").hide();
                    }
                });
            } catch (error) {
                console.error('Error in clear all order request data:', error);
                jQuery("#data-table_processing").hide();

                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred.');
                } else {
                    alert('An unexpected error occurred.');
                }
            }
        }
    });
</script>
@endsection
