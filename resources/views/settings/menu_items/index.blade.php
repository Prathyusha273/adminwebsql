@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.menu_items')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.menu_items_table')}}</li>
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
                        <h3 class="mb-0">{{trans('lang.menu_items')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.menu_items')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.banner_items_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <select id="zoneFilter" class="form-control" style="width: 200px;">
                            <option value="">All Zones</option>
                        </select>
                    </div>
                    <div class="card-header-btn mr-3">
                        <?php
                            $__perms = json_decode(@session('user_permissions'), true) ?: [];
                            if (in_array('banners.create', $__perms) || in_array('setting.banners.create', $__perms)) { ?>
                        <a class="btn-primary btn rounded-full" href="{!! route('setting.banners.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.menu_items_create')}}</a>
                        <?php } ?>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="bannerItemsTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <?php if (in_array('banners.delete', json_decode(@session('user_permissions'), true))) { ?>
                                        <th class="delete-all">
                                            <input type="checkbox" id="select-all">
                                            <label class="col-3 control-label" for="select-all">
                                                <a id="deleteAll" class="do_not_delete" href="javascript:void(0)">
                                                    <i class="mdi mdi-delete"></i> {{trans('lang.all')}}
                                                </a>
                                            </label>
                                        </th>
                                        <?php } ?>
                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.banner_position')}}</th>
                                        <th>Zone</th>
                                        <th>{{trans('lang.publish')}}</th>
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
    var placeholderImage = '{{ asset('images/default_image.png') }}';
    var zoneFilter = '';
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('banners.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    $(document).ready(function() {
        // Load zones first via backend if available (fallback to static options already present)
        // Zone filter change handler
        $('#zoneFilter').on('change', function() {
            zoneFilter = $(this).val();
            $('#bannerItemsTable').DataTable().ajax.reload();
        });

        jQuery("#data-table_processing").show();
        const table = $('#bannerItemsTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function (data, callback) {
                const params = { start: data.start, length: data.length, draw: data.draw, search: data.search.value, zoneId: zoneFilter };
                $.get('{{ route('menu-items.data') }}', params, function(json){
                    $('.total_count').text(json.recordsTotal || 0);
                    var rows = [];
                    var canDelete = (checkDeletePermission);
                    (json.data || []).forEach(function(item){
                        var route1 = '{{ route('setting.banners.edit', ':id') }}'.replace(':id', item.id);
                        var imageHtml = (!item.photo) ? '<img alt="" style="width:70px;height:70px;" src="'+placeholderImage+'" />' : '<img onerror="this.onerror=null;this.src=\''+placeholderImage+'\'" style="width:70px;height:70px;" src="'+item.photo+'" />';
                        var publishHtml = item.is_publish ? '<label class="switch"><input type="checkbox" checked data-id="'+item.id+'" class="toggle-publish"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" data-id="'+item.id+'" class="toggle-publish"><span class="slider round"></span></label>';
                        var actionsHtml = '<span class="action-btn"><?php if (in_array('banners.edit', json_decode(@session('user_permissions'), true))) { ?><a href="'+route1+'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a><?php } ?>';
                        if (canDelete) { actionsHtml += ' <a href="javascript:void(0)" data-id="'+item.id+'" class="delete-item"><i class="mdi mdi-delete"></i></a>'; }
                        actionsHtml += '</span>';
                        var checkboxHtml = canDelete ? '<td class="delete-all"><input type="checkbox" class="is_open" dataId="'+item.id+'"><label class="col-3 control-label"></label></td>' : '';
                        var zoneTitle = item.zoneTitle || 'No Zone';
                        if (canDelete) {
                            rows.push([checkboxHtml, imageHtml + '<a href="'+route1+'">'+(item.title||'')+'</a>', item.position||'', zoneTitle, publishHtml, actionsHtml]);
                        } else {
                            rows.push([imageHtml + '<a href="'+route1+'">'+(item.title||'')+'</a>', item.position||'', zoneTitle, publishHtml, actionsHtml]);
                        }
                    });
                    callback({ draw: json.draw, recordsTotal: json.recordsTotal, recordsFiltered: json.recordsFiltered, data: rows });
                    jQuery('#data-table_processing').hide();
                }).fail(function(xhr){
                    jQuery('#data-table_processing').hide();
                    callback({ draw: data.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                    alert('Failed to load menu items ('+xhr.status+'): '+xhr.statusText);
                });
            },
            order: (checkDeletePermission) ? [1, 'asc'] : [0, 'asc'],
            columnDefs: [
                { targets: (checkDeletePermission) ? [0, 4, 5] : [3, 4], orderable: false }
            ],
            language: {
                zeroRecords: '{{trans("lang.no_record_found")}}',
                emptyTable: '{{trans("lang.no_record_found")}}',
                "processing": '',
            },
        });
        table.columns.adjust().draw();
    })
    // Toggle publish
    $('#bannerItemsTable').on('change', '.toggle-publish', function(){
        var id = $(this).data('id');
        var $cb = $(this);
        var intended = $cb.is(':checked');
        $cb.prop('disabled', true);
        $.post({ url: '{{ url('menu-items') }}' + '/' + id + '/toggle', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(resp){ $cb.prop('checked', !!resp.is_publish); })
            .fail(function(xhr){ $cb.prop('checked', !intended); alert('Failed to update ('+xhr.status+'): '+xhr.statusText); })
            .always(function(){ $cb.prop('disabled', false); });
    });
    $('#select-all').change(function() {
        var isChecked = $(this).prop('checked');
        $('input[type="checkbox"][name="record"]').prop('checked', isChecked);
    });
    // Bulk delete
    $(document).on('click', '#deleteAll', function(){
        var ids = []; $('#bannerItemsTable .is_open:checked').each(function(){ ids.push($(this).attr('dataId')); });
        if (ids.length === 0) { alert("{{trans('lang.select_delete_alert')}}"); return; }
        if (!confirm("{{trans('lang.selected_delete_alert')}}")) return;
        $.post({ url: '{{ route('menu-items.bulkDelete') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, data: { ids: ids } })
            .done(function(){ $('#bannerItemsTable').DataTable().ajax.reload(null,false); })
            .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
    });
    // Single delete
    $('#bannerItemsTable').on('click', '.delete-item', function(){
        var id = $(this).data('id');
        if(!confirm("{{trans('lang.selected_delete_alert')}}")) return;
        $.post({ url: '{{ url('menu-items') }}' + '/' + id + '/delete', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(){ $('#bannerItemsTable').DataTable().ajax.reload(null,false); })
            .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
    });
</script>
@endsection
