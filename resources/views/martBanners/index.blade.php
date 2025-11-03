@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Mart Banner Items</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Mart Banner Items</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/banner.png') }}"></span>
                        <h3 class="mb-0">Mart Banner Items</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">Mart Banner Items</h3>
                    <p class="mb-0 text-dark-2">Manage mart banner items for the application</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <a class="btn-primary btn rounded-full" href="{!! route('mart.banners.create') !!}"><i class="mdi mdi-plus mr-2"></i>Create New Banner</a>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="martBannersTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <?php if (in_array('mart_banners.delete', json_decode(@session('user_permissions'), true))) { ?>
                                        <th class="delete-all">
                                            <input type="checkbox" id="select-all">
                                            <label class="col-3 control-label" for="select-all">
                                                <a id="deleteAll" class="do_not_delete" href="javascript:void(0)">
                                                    <i class="mdi mdi-delete"></i> All
                                                </a>
                                            </label>
                                        </th>
                                        <?php } ?>
                                        <th>Title</th>
                                        <th>Position</th>
                                        <th>Zone</th>
                                        <th>Order</th>
                                        <th>Publish</th>
                                        <th>Actions</th>
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
<!-- Load toastr library -->
<script src="{{ asset('js/toastr.js') }}"></script>

<script type="text/javascript">
    var placeholderImage = '{{ asset('images/default_image.png') }}';
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('mart_banners.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    $(document).ready(function() {
        jQuery("#data-table_processing").show();
        const table = $('#martBannersTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function (data, callback) {
                const params = { start: data.start, length: data.length, draw: data.draw, search: data.search.value };
                $.get('{{ route('mart.banners.data') }}', params, function(json){
                    $('.total_count').text(json.recordsTotal || 0);
                    var rows = [];
                    var canDelete = (checkDeletePermission);
                    (json.data || []).forEach(function(item){
                        var route1 = '{{ route('mart.banners.edit', ':id') }}'.replace(':id', item.id);
                        var imageHtml = (!item.photo) ? '<img alt="" style="width:70px;height:70px;" src="'+placeholderImage+'" />'
                                                       : '<img onerror="this.onerror=null;this.src=\''+placeholderImage+'\'" style="width:70px;height:70px;" src="'+item.photo+'" />';
                        var publishHtml = item.is_publish ? '<label class="switch"><input type="checkbox" checked data-id="'+item.id+'" class="toggle-publish"><span class="slider round"></span></label>'
                                                           : '<label class="switch"><input type="checkbox" data-id="'+item.id+'" class="toggle-publish"><span class="slider round"></span></label>';
                        var actionsHtml = '<span class="action-btn"><a href="'+route1+'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>';
                        if (canDelete) { actionsHtml += ' <a href="javascript:void(0)" data-id="'+item.id+'" class="delete-banner"><i class="mdi mdi-delete"></i></a>'; }
                        actionsHtml += '</span>';
                        var zoneBadge = item.zoneTitle ? '<span class="badge badge-info">'+item.zoneTitle+'</span>' : '<span class="text-muted">No Zone</span>';
                        var checkboxHtml = canDelete ? '<td class="delete-all"><input type="checkbox" class="is_open" dataId="'+item.id+'"><label class="col-3 control-label"></label></td>' : '';
                        if (canDelete) {
                            rows.push([checkboxHtml, imageHtml + '<a href="'+route1+'">'+(item.title||'')+'</a>', item.position||'', zoneBadge, (item.set_order||0), publishHtml, actionsHtml]);
                        } else {
                            rows.push([imageHtml + '<a href="'+route1+'">'+(item.title||'')+'</a>', item.position||'', zoneBadge, (item.set_order||0), publishHtml, actionsHtml]);
                        }
                    });
                    callback({ draw: json.draw, recordsTotal: json.recordsTotal, recordsFiltered: json.recordsFiltered, data: rows });
                    jQuery('#data-table_processing').hide();
                }).fail(function(xhr){
                    jQuery('#data-table_processing').hide();
                    callback({ draw: data.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                    alert('Failed to load banners ('+xhr.status+'): '+xhr.statusText);
                });
            },
            order: (checkDeletePermission) ? [1, 'asc'] : [0, 'asc'],
            columnDefs: [
                { targets: (checkDeletePermission) ? [0, 5, 6] : [4, 5], orderable: false }
            ],
            language: {
                zeroRecords: 'No records found',
                emptyTable: 'No records found',
                "processing": '',
            },
        });
        table.columns.adjust().draw();
    })

    // Toggle publish
    $('#martBannersTable').on('change', '.toggle-publish', function(){
        var id = $(this).data('id');
        var $cb = $(this);
        var intended = $cb.is(':checked');
        $cb.prop('disabled', true);
        $.post({ url: '{{ url('mart-banners') }}' + '/' + id + '/toggle-publish', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(resp){ $cb.prop('checked', !!resp.is_publish); })
            .fail(function(xhr){ $cb.prop('checked', !intended); alert('Failed to update ('+xhr.status+'): '+xhr.statusText); })
            .always(function(){ $cb.prop('disabled', false); });
    });

    // Handle select all
    $('#select-all').on('change', function() {
        $('.select-item').prop('checked', $(this).is(':checked'));
    });

    // Bulk delete
    $(document).on('click', '#deleteAll', function(){
        var ids = []; $('#martBannersTable .is_open:checked').each(function(){ ids.push($(this).attr('dataId')); });
        if (ids.length === 0) { alert('Please select items to delete'); return; }
        if (!confirm('Are you sure you want to delete selected items?')) return;
        $.post({ url: '{{ route('mart.banners.bulkDelete') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, data: { ids: ids } })
            .done(function(){ $('#martBannersTable').DataTable().ajax.reload(null,false); })
            .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
    });

    // Single delete
    $('#martBannersTable').on('click', '.delete-banner', function(){
        var id = $(this).data('id');
        if(!confirm('Are you sure you want to delete this banner item?')) return;
        $.ajax({ url: '{{ url('mart-banners') }}' + '/' + id, method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(){ $('#martBannersTable').DataTable().ajax.reload(null,false); })
            .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
    });
</script>

<!-- Add CSS for the publish toggle switch -->
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
@endsection
