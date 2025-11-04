@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.tax_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.tax_plural')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/tax.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.tax_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.tax_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.taxes_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <a class="btn-primary btn rounded-full" href="{!! route('tax.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.tax_create')}}</a>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="taxTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (in_array('tax.delete', json_decode(@session('user_permissions'),true))) { ?>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)">
                                        <i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                    <?php } ?>
                                    </th>
                                    <th>{{trans('lang.title')}}</th>
                                    <th>{{trans('lang.country')}}</th>
                                    <th>{{trans('lang.type')}}</th>
                                    <th>{{trans('lang.tax_value')}}</th>
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
    if ($.inArray('tax.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    var deleteMsg = "{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg = "{{trans('lang.selected_delete_alert')}}";

    $(document).ready(function () {
        // Initialize DataTable with server-side processing from MySQL
        var tableConfig = {
            pageLength: 10,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('tax.data') }}',
                type: 'GET',
                error: function(xhr, error, code) {
                    console.error('DataTables Ajax Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            },
            order: [[1, 'asc']], // Sort by title
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}",
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            }
        };

        // Set column orderable based on permissions
        if (checkDeletePermission) {
            tableConfig.columnDefs = [
                {orderable: false, targets: [0, 5, 6]}
            ];
        } else {
            tableConfig.columnDefs = [
                {orderable: false, targets: [4, 5]}
            ];
        }

        var table = $('#taxTable').DataTable(tableConfig);

        // Update count when table redraws
        table.on('draw.dt', function() {
            var info = table.page.info();
            $('.total_count').text(info.recordsTotal);
        });
    });

    // Select all checkboxes
    $("#is_active").click(function () {
        $("#taxTable .is_open").prop('checked', $(this).prop('checked'));
    });

    // Delete selected (bulk delete)
    $("#deleteAll").click(function () {
        if ($('#taxTable .is_open:checked').length) {
            if (confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                var ids = [];
                var taxNames = [];

                $('#taxTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    var taxName = $(this).closest('tr').find('td').eq(1).find('a').text();
                    ids.push(dataId);
                    taxNames.push(taxName);
                });

                // Call bulk delete API
                $.ajax({
                    url: '{{ route('tax.bulkDelete') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: ids
                    },
                    success: async function(response) {
                        jQuery("#overlay").hide();
                        if (response.success) {
                            // Log the activity
                            if (typeof logActivity === 'function') {
                                await logActivity('tax_settings', 'bulk_deleted', 'Bulk deleted taxes: ' + taxNames.join(', '));
                            }
                            // Reload DataTables instead of full page reload
                            $('#taxTable').DataTable().ajax.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        jQuery("#overlay").hide();
                        var errorMsg = xhr.responseJSON?.message || error || 'Unknown error';
                        alert('Error deleting taxes: ' + errorMsg);
                        console.error('Bulk delete error:', xhr.responseText);
                    }
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    // Toggle enable/disable status
    $(document).on("click", "input[name='isSwitch']", function (e) {
        var checkbox = $(this);
        var ischeck = checkbox.is(':checked');
        var id = this.id;
        var taxName = checkbox.closest('tr').find('td').eq(1).find('a').text();

        $.ajax({
            url: '{{ url('tax') }}/' + id + '/toggle',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                enable: ischeck ? 1 : 0
            },
            success: async function(response) {
                if (response.success) {
                    // Log the activity
                    if (typeof logActivity === 'function') {
                        var action = ischeck ? 'enabled' : 'disabled';
                        await logActivity('tax_settings', action, (ischeck ? 'Enabled' : 'Disabled') + ' tax: ' + taxName);
                    }
                    console.log('Tax status updated successfully');
                } else {
                    alert('Error: ' + response.message);
                    // Revert checkbox
                    checkbox.prop('checked', !ischeck);
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = xhr.responseJSON?.message || error || 'Unknown error';
                alert('Error updating tax status: ' + errorMsg);
                console.error('Toggle error:', xhr.responseText);
                // Revert checkbox
                checkbox.prop('checked', !ischeck);
            }
        });
    });

    // Single delete
    $(document).on("click", "a[name='tax-delete']", function (e) {
        e.preventDefault();
        if (confirm(deleteMsg)) {
            var id = this.id;
            var taxName = $(this).closest('tr').find('td').eq(1).find('a').text();
            jQuery("#overlay").show();

            $.ajax({
                url: '{{ url('tax') }}/' + id + '/delete',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: async function(response) {
                    jQuery("#overlay").hide();
                    if (response.success) {
                        // Log the activity
                        if (typeof logActivity === 'function') {
                            await logActivity('tax_settings', 'deleted', 'Deleted tax: ' + taxName);
                        }
                        // Reload DataTables to show updated data
                        $('#taxTable').DataTable().ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    jQuery("#overlay").hide();
                    var errorMsg = xhr.responseJSON?.message || error || 'Unknown error';
                    alert('Error deleting tax: ' + errorMsg);
                    console.error('Delete error:', xhr.responseText);
                }
            });
        }
    });
</script>
@endsection
