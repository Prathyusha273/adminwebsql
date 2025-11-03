@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.on_board_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.on_board_plural')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/onboarding.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.on_board_plural')}}</h3>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.on_board_plural')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.on_board_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <!-- <a class="btn-primary btn rounded-full" href="{!! route('users.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.user_create')}}</a> -->
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="onboardTable" class="display  table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.description')}}</th>
                                        <th>{{trans('lang.app_screen')}}</th>
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
        $(document).ready(function(){
            jQuery("#data-table_processing").show();
            const table = $('#onboardTable').DataTable({
                pageLength: 10,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: function (data, callback) {
                    const params = { start: data.start, length: data.length, draw: data.draw, search: data.search.value };
                    $.get('{{ route('on-board.data') }}', params, function (json) {
                        $('.total_count').text(json.recordsTotal || 0);
                        callback(json);
                    }).fail(function(xhr){ alert('Failed to load ('+xhr.status+'): '+xhr.statusText); })
                      .always(function(){ jQuery('#data-table_processing').hide(); });
                },
                order: [[0,'asc']],
                columnDefs: [ { orderable: false, targets: [3] } ],
                language: { zeroRecords: "{{trans('lang.no_record_found')}}", emptyTable: "{{trans('lang.no_record_found')}}" }
            });

            // Delete single record
            $('#onboardTable').on('click', '.delete-onboard', function(){
                var id = $(this).data('id');
                if(!confirm("{{trans('lang.selected_delete_alert')}}")) return;
                $.post({ url: '{{ url('on-board') }}' + '/' + id + '/delete', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                    .done(function(){ $('#onboardTable').DataTable().ajax.reload(null,false); })
                    .fail(function(xhr){ alert('Failed to delete ('+xhr.status+'): '+xhr.statusText); });
            });
        });
    </script>
@endsection
