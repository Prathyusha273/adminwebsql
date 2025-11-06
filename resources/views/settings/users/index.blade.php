@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.user_plural')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.user_table')}}</li>
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
                                <span class="icon mr-3"><img src="{{ asset('images/users.png') }}"></span>
                                <h3 class="mb-0">{{trans('lang.user_plural')}}</h3>
                                <span class="counter ml-3 total_count"></span>
                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="select-box pl-3">
                                    <select class="form-control status_selector">
                                        <option value="">{{trans("lang.status")}}</option>
                                        <option value="active">{{trans("lang.active")}}</option>
                                        <option value="inactive">{{trans("lang.in_active")}}</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <select class="form-control zone_selector">
                                        <option value="" disabled selected>{{trans('lang.select_zone')}}</option>
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
                                <h3 class="text-dark-2 mb-2 h4">Bulk Import Users</h3>
                                <p class="mb-0 text-dark-2">Upload Excel file to import multiple users at once</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a href="{{ route('users.download-template') }}"
                                       class="btn btn-outline-primary rounded-full">
                                        <i class="mdi mdi-download mr-2"></i>Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="importFile" class="control-label">Select Excel File
                                                (.xls/.xlsx)</label>
                                            <input type="file" name="file" id="importFile" accept=".xls,.xlsx"
                                                   class="form-control" required>
                                            <div class="form-text text-muted">
                                                <i class="mdi mdi-information-outline mr-1"></i>
                                                File should contain: firstName, lastName, email, password, active, role,
                                                profilePictureURL, createdAt
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary rounded-full">
                                            <i class="mdi mdi-upload mr-2"></i>Import Users
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
                                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.user_table')}}</h3>
                                    <p class="mb-0 text-dark-2">{{trans('lang.users_table_text')}}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                        <a class="btn-primary btn rounded-full" href="{!! route('users.create') !!}"><i
                                                class="mdi mdi-plus mr-2"></i>{{trans('lang.user_create')}}</a>
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
                                            <?php if (in_array('user.delete', json_decode(@session('user_permissions')
                                            ,true))) { ?>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                                                                   class="do_not_delete"
                                                                                                   href="javascript:void(0)"><i
                                                            class="mdi mdi-delete"></i> {{trans('lang.all')}}
                                                    </a></label></th>
                                            <?php } ?>
                                            <th>{{trans('lang.user_info')}}</th>
                                            <th>{{trans('lang.email')}}</th>
                                            <th>{{trans('lang.phone_number')}}</th>
                                            <th>{{trans('lang.zone')}}</th>
                                            <th>{{trans('lang.date')}}</th>
                                            <th>{{trans('lang.active')}}</th>
                                            {{-- <th>{{trans('lang.wallet_transaction')}}</th> --}}
                                            <!-- <th >{{trans('lang.role')}}</th> -->
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
        // SQL mode: fetch via API instead of Firebase
        var apiBase = '{{ url('/api') }}';
        var placeholderImage = '{{ asset('images/placeholder.png') }}';
        var user_permissions = '<?php echo @session("user_permissions") ?>';
        user_permissions = Object.values(JSON.parse(user_permissions));
        var checkDeletePermission = false;
        if ($.inArray('user.delete', user_permissions) >= 0) {
            checkDeletePermission = true;
        }
        var zoneIdToName = {};
        var zonesLoaded = false;

        // Format date time helper function
        function formatDateTime(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '-';

                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const month = months[date.getMonth()];
                const day = String(date.getDate()).padStart(2, '0');
                const year = date.getFullYear();
                let hours = date.getHours();
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;

                return `${month} ${day}, ${year} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
            } catch (e) {
                console.error('Error formatting date:', e);
                return '-';
            }
        }

        // Load zones from SQL
        var loadZonesPromise = new Promise(function(resolve){
            console.log('🔄 Loading zones from SQL...');
            $.ajax({
                url: '{{ route("zone.data") }}',
                method: 'GET',
                success: function(response) {
                    console.log('📊 Zones API response:', response);
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(zone) {
                            zoneIdToName[zone.id] = zone.name;
                            // Add zone to selector
                            $('.zone_selector').append(
                                $('<option></option>').val(zone.id).text(zone.name)
                            );
                        });
                        console.log('✅ Zones loaded from SQL (' + response.data.length + ' zones):', zoneIdToName);
                    } else {
                        console.warn('⚠️ No zones found in database');
                    }
                    zonesLoaded = true;
                    resolve(zoneIdToName);
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error loading zones:', error);
                    console.error('Response:', xhr.responseText);
                    zonesLoaded = true;
                    resolve(zoneIdToName);
                }
            });
        });

        // Initialize select2 for all selectors
        $('.status_selector').select2({
            placeholder: '{{trans("lang.status")}}',
            minimumResultsForSearch: Infinity,
            allowClear: true
        });
        $('.zone_selector').select2({
            placeholder: "{{trans('lang.select_zone')}}",
            minimumResultsForSearch: Infinity,
            allowClear: true
        });

        // Handle select2 unselecting
        $('select').on("select2:unselecting", function (e) {
            var self = $(this);
            setTimeout(function () {
                self.select2('close');
            }, 0);
        });

        // Main filter handler - triggers when ANY select changes
        $('select').change(async function() {
            var status = $('.status_selector').val();
            var zoneValue = $('.zone_selector').val();
            var daterangepicker = $('#daterange').data('daterangepicker');

            console.log('Filter change triggered:');
            console.log('- Status Value:', status);
            console.log('- Zone Value:', zoneValue);

            // No-op; filters are sent to server via DataTables ajax

            // Note: Zone filter is NOT applied in Firestore query
            // It will be applied client-side because zoneId is nested in shippingAddress

            // Apply date filter
            if ($('#daterange span').html() != '{{trans("lang.select_range")}}' && daterangepicker) {
                // handled server-side
            }

            // Apply status filter
            // handled server-side

            // Reload the table with new filters
            $('#userTable').DataTable().ajax.reload();
        });

        function setDate() {
            $('#daterange span').html('{{trans("lang.select_range")}}');
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
            }, function (start, end) {
                $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('select').trigger('change');
            });
            $('#daterange').on('apply.daterangepicker', function (ev, picker) {
                $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
                $('select').trigger('change');
            });
            $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
                $('#daterange span').html('{{trans("lang.select_range")}}');
                $('select').trigger('change');
            });
        }

        setDate();
        $(document).ready(function () {
            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
                window.location.href = url;
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

            // Wait for zones to load before initializing DataTable
            loadZonesPromise.then(function() {
                console.log('🚀 Zones loaded, initializing DataTable with zone mapping:', zoneIdToName);

                var fieldConfig = {
                    columns: [
                        {key: 'fullName', header: "{{trans('lang.user_info')}}"},
                        {key: 'email', header: "{{trans('lang.email')}}"},
                        {key: 'phoneNumber', header: "{{trans('lang.phone_number')}}"},
                        {key: 'zone', header: "{{trans('lang.zone')}}"},
                        {key: 'active', header: "{{trans('lang.active')}}"},
                        {key: 'createdAt', header: "{{trans('lang.created_at')}}"},
                    ],
                    fileName: "{{trans('lang.user_table')}}",
                };
                const table = $('#userTable').DataTable({
                    pageLength: 10,
                    processing: false, // Show processing indicator
                    serverSide: true, // Enable server-side processing
                    responsive: true,
                    ajax: function (data, callback, settings) {
                        const start = data.start;
                        const length = data.length;
                        const searchValue = data.search.value.toLowerCase();
                        const status = $('.status_selector').val();
                        const zoneValue = $('.zone_selector').val();
                        const daterangepicker = $('#daterange').data('daterangepicker');
                        let from = '', to = '';
                        if ($('#daterange span').html() != '{{trans("lang.select_range")}}' && daterangepicker) {
                            from = daterangepicker.startDate.format('YYYY-MM-DD HH:mm:ss');
                            to = daterangepicker.endDate.format('YYYY-MM-DD HH:mm:ss');
                        }
                        $('#data-table_processing').show();
                        $.ajax({
                            url: apiBase + '/app-users',
                            method: 'GET',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            data: {
                                page: Math.floor(start / length) + 1,
                                limit: length,
                                search: searchValue,
                                status: status,
                                zoneId: zoneValue,
                                from: from,
                                to: to,
                                role: 'customer'
                            }
                        }).done(function (resp) {
                            console.log('📊 Users API response:', resp);
                            const items = resp.data || [];
                            const total = (resp.meta && resp.meta.total) ? resp.meta.total : items.length;
                            $('.total_count').text(total);
                            console.log('👥 Loading ' + items.length + ' users');
                            let records = [];
                            items.forEach(function (childData) {
                                console.log('User data:', {
                                    id: childData.id,
                                    name: childData.fullName,
                                    zoneId: childData.zoneId,
                                    zoneIdType: typeof childData.zoneId
                                });
                                var id = childData.id;
                                var route1 = '{{route("users.edit",":id")}}'.replace(':id', id);
                                var user_view = '{{route("users.view",":id")}}'.replace(':id', id);
                                var vendorImage = childData.profilePictureURL == '' || childData.profilePictureURL == null ? '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">' : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + childData.profilePictureURL + '" alt="image">'

                                // Display zone name based on zoneId
                                var zoneName = '';
                                if (childData.zoneId && childData.zoneId !== '' && childData.zoneId !== null) {
                                    // Check if zone exists in mapping
                                    if (zoneIdToName[childData.zoneId]) {
                                        zoneName = '<span class="badge badge-info py-2 px-3">' + zoneIdToName[childData.zoneId] + '</span>';
                                        console.log('✅ Zone found for user ' + id + ':', zoneIdToName[childData.zoneId]);
                                    } else {
                                        // Zone ID exists but not found in zones table
                                        zoneName = '<span class="badge badge-warning py-2 px-3" style="color: #666;">Zone Not Found (ID: ' + childData.zoneId + ')</span>';
                                        console.warn('⚠️ Zone ID "' + childData.zoneId + '" not found in zones table for user ' + id);
                                    }
                                } else {
                                    // No zone ID assigned
                                    zoneName = '<span style="color: #999; font-style: italic;">null</span>';
                                    console.log('ℹ️ No zone assigned for user ' + id);
                                }
                                // Format date with the new format: Oct 06, 2025 07:24 AM
                                var createdAt = formatDateTime(childData.createdAt) || '-';
                                records.push([
                                    checkDeletePermission ? '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label" for="is_open_' + id + '" ></label></td>' : '',
                                    vendorImage + '<a href="' + user_view + '" class="redirecttopage">' + (childData.fullName || '') + '</a>',
                                    childData.email ? childData.email : ' ',
                                    childData.phoneNumber ? childData.phoneNumber : ' ',
                                    zoneName,
                                    createdAt,
                                    childData.active ? '<label class="switch"><input type="checkbox" checked id="' + id + '" name="isActive"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + id + '" name="isActive"><span class="slider round"></span></label>',
                                    '<span class="action-btn"><a href="' + user_view + '"><i class="mdi mdi-eye"></i></a><a href="' + route1 + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a><?php if (in_array('user.delete', json_decode(@session('user_permissions'), true))){ ?> <a id="' + id + '" class="delete-btn" name="user-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></td><?php } ?></span>'
                                ]);
                            });
                            $('#data-table_processing').hide();
                            callback({
                                draw: data.draw,
                                recordsTotal: total,
                                recordsFiltered: total,
                                filteredData: items,
                                data: records
                            });
                        }).fail(function () {
                            $('#data-table_processing').hide();
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                filteredData: [],
                                data: []
                            });
                        });
                    },
                    order: [checkDeletePermission ? 5 : 4, 'desc'],
                    columnDefs: [
                        {
                            targets: (checkDeletePermission) ? 5 : 4,
                            type: 'date',
                            render: function (data) {
                                return data;
                            }
                        },
                        {orderable: false, targets: (checkDeletePermission) ? [0, 6, 7] : [5, 6]},
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
                                        exportData(dt, 'excel', fieldConfig);
                                    }
                                },
                                {
                                    extend: 'pdfHtml5',
                                    text: 'Export PDF',
                                    action: function (e, dt, button, config) {
                                        exportData(dt, 'pdf', fieldConfig);
                                    }
                                },
                                {
                                    extend: 'csvHtml5',
                                    text: 'Export CSV',
                                    action: function (e, dt, button, config) {
                                        exportData(dt, 'csv', fieldConfig);
                                    }
                                }
                            ]
                        }
                    ],
                    initComplete: function () {
                        $(".dataTables_filter").append($(".dt-buttons").detach());
                        $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete', 'new-password').val('');
                        $('.dataTables_filter label').contents().filter(function () {
                            return this.nodeType === 3;
                        }).remove();
                    }
                });
                table.columns.adjust().draw();

                function debounce(func, wait) {
                    let timeout;
                    const context = this;
                    return function (...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }

                $('#search-input').on('input', debounce(function () {
                    const searchValue = $(this).val();
                    if (searchValue.length >= 3) {
                        $('#data-table_processing').show();
                        table.search(searchValue).draw();
                    } else if (searchValue.length === 0) {
                        $('#data-table_processing').show();
                        table.search('').draw();
                    }
                }, 300));
            }); // Close loadZonesPromise.then()
        });
        $("#is_active").click(function () {
            $("#userTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(async function () {
            if ($('#userTable .is_open:checked').length) {
                if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                    jQuery("#data-table_processing").show();
                    var selectedUsers = [];
                    // Get selected user IDs for logging
                    $('#userTable .is_open:checked').each(function() {
                        var dataId = $(this).attr('dataId');
                        selectedUsers.push('User ID: ' + dataId);
                    });

                    $('#userTable .is_open:checked').each(async function () {
                        var dataId = $(this).attr('dataId');
                        await deleteDocumentWithImage('users', dataId, 'profilePictureURL');
                        const getStoreName = deleteUserData(dataId);
                        console.log('✅ Bulk user deletion completed, now logging activity...');
                        try {
                            if (typeof logActivity === 'function') {
                                console.log('🔍 Calling logActivity for bulk user deletion...');
                                await logActivity('users', 'bulk_deleted', 'Bulk deleted users: ' + selectedUsers.join(', '));
                                console.log('✅ Activity logging completed successfully');
                            } else {
                                console.error('❌ logActivity function is not available');
                            }
                        } catch (error) {
                            console.error('❌ Error calling logActivity:', error);
                        }
                        setTimeout(function () {
                            window.location.reload();
                        }, 7000);
                    });
                }
            } else {
                alert("{{trans('lang.select_delete_alert')}}");
            }
        });

        async function deleteUserData(userId) {
            // Delete user via SQL API
            try {
                const response = await DB.delete(`/users/${userId}`);
                console.log('✅ User deleted successfully');
                return true;
            } catch (error) {
                console.error('❌ Error deleting user:', error);
                return false;
            }

        }

        $(document).on("click", "a[name='user-delete']", async function (e) {
            var id = this.id;
            if (!confirm("{{trans('lang.delete_alert')}}")) return;
            $('#data-table_processing').show();
            $.ajax({
                url: apiBase + '/app-users/' + id,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).done(function(){
                $('#data-table_processing').hide();
                window.location.href = '{{ url()->current() }}';
            }).fail(function(){
                $('#data-table_processing').hide();
                alert('Failed to delete user');
            });
        });
        $(document).on("click", "input[name='isActive']", async function (e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            $.ajax({
                url: apiBase + '/app-users/' + id + '/active',
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { active: ischeck ? 'true' : 'false' }
            }).fail(function(){
                alert('Failed to update status');
            });
        });
    </script>
@endsection
