@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.document_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.document_table')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/document.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.document_plural')}}</h3>
                        <span class="counter ml-3 doc_count"></span>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.document_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.documents_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <a class="btn-primary btn rounded-full" href="{!! route('documents.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.document_create')}}</a>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                        <div class="table-responsive m-t-10">
                            <table id="documentTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <?php if (in_array('documents.delete', json_decode(@session('user_permissions'), true))) { ?>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                        class="do_not_delete" href="javascript:void(0)"><i
                                                            class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                            <?php } ?>
                                        </th>
                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.document_for')}}</th>
                                        <th>{{trans('lang.coupon_enabled')}}</th>
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
    var user_permissions = '<?php echo @session("user_permissions") ?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = ($.inArray('documents.delete', user_permissions) >= 0);

    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#data-table_processing").show();
        const table = $('#documentTable').DataTable({
            pageLength: 10,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: function (data, callback) {
                const params = { start: data.start, length: data.length, draw: data.draw, search: data.search.value };
                $.get('{{ route('documents.data') }}', params, function (json) {
                    $('.doc_count').text(json.recordsTotal || 0);
                    callback(json);
                    jQuery('#data-table_processing').hide();
                }).fail(function(xhr){
                    jQuery('#data-table_processing').hide();
                    alert('Failed to load documents ('+xhr.status+'): '+xhr.statusText);
                });
            },
            order: (checkDeletePermission) ? [[1,'asc']] : [[0,'asc']],
            columnDefs: [ { orderable: false, targets: (checkDeletePermission) ? [0,2,3] : [1,2] } ],
            language: { zeroRecords: "{{trans('lang.no_record_found')}}", emptyTable: "{{trans('lang.no_record_found')}}", processing: "" }
        });

        // Toggle enable
        $('#documentTable').on('change', '.toggle-enable', function(){
            var id = $(this).data('id');
            var enable = $(this).is(':checked');
            var $cb = $(this);
            $cb.prop('disabled', true);
            $.post({ url: '{{ url('documents') }}' + '/' + id + '/toggle', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, data: { enable: enable } })
                .done(function(resp){ $cb.prop('checked', !!resp.enable); })
                .fail(function(xhr){ $cb.prop('checked', !enable); alert('Failed to update ('+xhr.status+'): '+xhr.statusText); })
                .always(function(){ $cb.prop('disabled', false); });
        });

        // Single delete
        $('#documentTable').on('click', '.delete-document', function(){
            var id = $(this).data('id');
            if(!confirm("{{trans('lang.selected_delete_alert')}}")) return;
            $.post({ url: '{{ url('documents') }}' + '/' + id + '/delete', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .done(function(){ $('#documentTable').DataTable().ajax.reload(null,false); })
                .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
        });

        // Bulk
        $(document).on('click', '#is_active', function(){ $("#documentTable .is_open").prop('checked', $(this).prop('checked')); });
        $(document).on('click', '#deleteAll', function(){
            var ids = []; $('#documentTable .is_open:checked').each(function(){ ids.push($(this).attr('dataId')); });
            if(ids.length===0){ alert("{{trans('lang.select_delete_alert')}}"); return; }
            if(!confirm("{{trans('lang.selected_delete_alert')}}")) return;
            $.post({ url: '{{ route('documents.bulkDelete') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, data: { ids: ids } })
                .done(function(){ $('#documentTable').DataTable().ajax.reload(null,false); })
                .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
        });
    });
</script>
@endsection
