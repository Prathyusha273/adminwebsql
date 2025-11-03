@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.cms_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.cms_plural')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/cms.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.cms_plural')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.cms_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.cms_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                   <div class="card-header-btn mr-3">
                        <a class="btn-primary btn rounded-full" href="{!! route('cms.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.cms_create')}}</a>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                         <table id="cmsTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <?php if (in_array('cms.delete', json_decode(@session('user_permissions'),true))) { ?>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                                <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                            </th>
                                        <?php } ?>
                                        <th>{{trans('lang.cms_name')}}</th>
                                        <th>{{trans('lang.cms_slug')}}</th>
                                        <th>{{trans('lang.status')}}</th>
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
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('cms.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    $(document).ready(function () {
        jQuery("#data-table_processing").show();
        const table = $('#cmsTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function (data, callback) {
                const params = { start: data.start, length: data.length, draw: data.draw, search: data.search.value };
                $.get('{{ route('cms.data') }}', params, function (json) {
                    $('.total_count').text(json.recordsTotal || 0);
                    callback(json);
                }).fail(function(xhr){
                    alert('Failed to load CMS ('+xhr.status+'): '+xhr.statusText);
                }).always(function(){ jQuery('#data-table_processing').hide(); });
            },
            order: (checkDeletePermission) ? [1, 'asc'] : [0, 'asc'],
            columnDefs: [
                { targets: (checkDeletePermission) ? [0, 2, 3] : [2, 3], orderable: false }
            ],
            language: {
                zeroRecords: "{{trans("lang.no_record_found")}}",
                emptyTable: "{{trans("lang.no_record_found")}}",
                "processing": "",
            },
        });
        table.columns.adjust().draw();
        function debounce(func, wait) {
            let timeout;
            const context = this;
            return function(...args) {
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
    });
    // Select all / bulk delete
    $(document).on('click', '#is_active', function(){ $("#cmsTable .is_open").prop('checked', $(this).prop('checked')); });
    $(document).on('click', '#deleteAll', function(){
        var ids = []; $('#cmsTable .is_open:checked').each(function(){ ids.push($(this).attr('dataId')); });
        if(ids.length===0){ alert("{{trans('lang.select_delete_alert')}}"); return; }
        if(!confirm("{{trans('lang.selected_delete_alert')}}")) return;
        $.post({ url: '{{ route('cms.bulkDelete') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, data: { ids: ids } })
            .done(function(){ $('#cmsTable').DataTable().ajax.reload(null,false); })
            .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
    });
    // Toggle publish
    $('#cmsTable').on('change', '.toggle-publish', function(){
        var id = $(this).data('id');
        var publish = $(this).is(':checked');
        var $cb = $(this); $cb.prop('disabled', true);
        $.post({ url: '{{ url('cms') }}' + '/' + id + '/toggle', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, data: { publish: publish } })
            .fail(function(xhr){ $cb.prop('checked', !publish); alert('Failed to update ('+xhr.status+'): '+xhr.statusText); })
            .always(function(){ $cb.prop('disabled', false); });
    });
    // Single delete
    $('#cmsTable').on('click', '.delete-cms', function(){
        var id = $(this).data('id');
        if(!confirm("{{trans('lang.selected_delete_alert')}}")) return;
        $.post({ url: '{{ url('cms') }}' + '/' + id + '/delete', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(){ $('#cmsTable').DataTable().ajax.reload(null,false); })
            .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
    });
</script>
@endsection
