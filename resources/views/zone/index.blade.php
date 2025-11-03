@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.zone_plural')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.zone_table')}}</li>
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
                                <span class="icon mr-3"><img src="{{ asset('images/zone.png') }}"></span>
                                <h3 class="mb-0">{{trans('lang.zone_table')}}</h3>
                                <span class="counter ml-3 zone_count"></span>
                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="select-box pl-3">

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
                                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.zone_table')}}</h3>
                                    <p class="mb-0 text-dark-2">{{trans('lang.zone_table_text')}}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                        <a class="btn-primary btn rounded-full" href="{!! route('zone.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.zone_create')}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="example24" class="display nowrap table table-hover table-striped table-bordered table table-striped"cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <?php if (in_array('zone.delete', json_decode(@session('user_permissions'),true))) { ?>
                                            <th class="delete-all"><input type="checkbox" id="is_active">
                                                <label class="col-3 control-label" for="is_active">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a>
                                                </label>
                                            </th>
                                            <?php } ?>
                                            <th>{{trans('lang.zone_name')}}</th>
                                            <th>{{trans('lang.status')}}</th>
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
        var user_permissions = '<?php echo @session("user_permissions")?>';
        user_permissions = Object.values(JSON.parse(user_permissions));
        var checkDeletePermission = false;
        if ($.inArray('zone.delete', user_permissions) >= 0) {
            checkDeletePermission = true;
        }

        $(document).ready(function () {
            jQuery("#data-table_processing").show();
            
            // Fetch zones data from SQL database
            $.ajax({
                url: "{{ route('zone.data') }}",
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var zones = response.data;
                        $('.zone_count').text(zones.length);
                        
                        var html = '';
                        zones.forEach(function(zone) {
                            html += buildHTML(zone);
                        });
                        
                        $('#append_list1').html(html);
                        
                        // Initialize DataTable
                        if (checkDeletePermission) {
                            $('#example24').DataTable({
                                order: [[1, 'asc']],
                                columnDefs: [
                                    {orderable: false, targets: [0, 2, 3]},
                                ],
                                "language": {
                                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                                    "emptyTable": "{{trans('lang.no_record_found')}}"
                                },
                                responsive: true
                            });
                        } else {
                            $('#example24').DataTable({
                                order: [[0, 'asc']],
                                columnDefs: [
                                    {orderable: false, targets: [1, 2]},
                                ],
                                "language": {
                                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                                    "emptyTable": "{{trans('lang.no_record_found')}}"
                                },
                                responsive: true
                            });
                        }
                    }
                    jQuery("#data-table_processing").hide();
                },
                error: function(xhr) {
                    console.error('Error loading zones:', xhr);
                    jQuery("#data-table_processing").hide();
                    alert('Error loading zones data');
                }
            });
        });

        function buildHTML(zone) {
            var html = '';
            html += '<tr>';
            var route1 = '{{route("zone.edit",":id")}}';
            route1 = route1.replace(':id', zone.id);
            
            if (checkDeletePermission) {
                html += '<td class="delete-all"><input type="checkbox" id="is_open_' + zone.id + '" class="is_open" dataId="' + zone.id + '"><label class="col-3 control-label" for="is_open_' + zone.id + '" ></label></td>';
            }
            
            html += '<td><a href="' + route1 + '">' + zone.name + '</a></td>';
            
            if (zone.publish) {
                html += '<td><label class="switch"><input type="checkbox" checked id="' + zone.id + '" name="isSwitch"><span class="slider round"></span></label></td>';
            } else {
                html += '<td><label class="switch"><input type="checkbox" id="' + zone.id + '" name="isSwitch"><span class="slider round"></span></label></td>';
            }
            
            html += '<td class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>';
            
            if (checkDeletePermission) {
                html += '<a id="' + zone.id + '" name="zone-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
            }
            
            html += '</td>';
            html += '</tr>';
            return html;
        }

        $("#is_active").click(function () {
            $("#example24 .is_open").prop('checked', $(this).prop('checked'));
        });

        $("#deleteAll").click(function () {
            if ($('#example24 .is_open:checked').length) {
                if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                    jQuery("#overlay").show();
                    
                    var ids = [];
                    $('#example24 .is_open:checked').each(function () {
                        ids.push($(this).attr('dataId'));
                    });
                    
                    $.ajax({
                        url: "{{ route('zone.delete-multiple') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            ids: ids
                        },
                        success: function(response) {
                            jQuery("#overlay").hide();
                            if (response.success) {
                                window.location.reload();
                            } else {
                                alert(response.message || 'Error deleting zones');
                            }
                        },
                        error: function(xhr) {
                            jQuery("#overlay").hide();
                            alert('Error deleting zones');
                        }
                    });
                }
            } else {
                alert("{{trans('lang.select_delete_alert')}}");
            }
        });

        $(document).on("change", "input[name='isSwitch']", function (e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            var $checkbox = $(this);
            var originalState = !ischeck;
            
            // Disable checkbox during request
            $checkbox.prop('disabled', true);
            
            // Build URL using route helper
            var url = "{{ route('zone.toggle-status', ':id') }}";
            url = url.replace(':id', id);
            
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    publish: ischeck
                },
                success: function(response) {
                    $checkbox.prop('disabled', false);
                    if (!response.success) {
                        // Revert checkbox state on error
                        $checkbox.prop('checked', originalState);
                        alert(response.message || 'Error updating zone status');
                    }
                },
                error: function(xhr) {
                    $checkbox.prop('disabled', false);
                    // Revert checkbox state on error
                    $checkbox.prop('checked', originalState);
                    console.error('Error toggling zone status:', xhr);
                    if (xhr.status === 405) {
                        alert('Method not allowed. Please refresh the page and try again.');
                    } else {
                        alert('Error updating zone status');
                    }
                }
            });
        });

        $(document).on("click", "a[name='zone-delete']", function (e) {
            if (!confirm("{{trans('lang.delete_alert')}}")) {
                return;
            }
            
            var id = this.id;
            jQuery("#overlay").show();
            
            $.ajax({
                url: "{{ url('/zone') }}/" + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    jQuery("#overlay").hide();
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert(response.message || 'Error deleting zone');
                    }
                },
                error: function(xhr) {
                    jQuery("#overlay").hide();
                    alert('Error deleting zone');
                }
            });
        });
    </script>
@endsection
