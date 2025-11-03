@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('Media')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('Media')}}</li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/category.png') }}"></span>
                            <h3 class="mb-0">{{trans('Media List')}}</h3>
                            <span class="counter ml-3 media_count"></span>
                        </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3">
                                <!-- Future filters can be added here -->
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('Media List')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('View and manage all the media')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! route('media.create') !!}">
                                        <i class="mdi mdi-plus mr-2"></i>{{trans('Media Create')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="mediaTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="delete-all"><input type="checkbox" id="select-all"><label class="col-3 control-label" for="select-all"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label></th>
                                            <th>{{trans('Media Info')}}</th>
                                            <th>{{trans('Slug')}}</th>
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
<div id="data-table_processing" class="data-table-processing" style="display: none">Processing...</div>
@endsection
@section('scripts')
<style>
.table-responsive {
    overflow-x: auto;
}
#mediaTable {
    width: 100% !important;
}
#mediaTable td {
    white-space: nowrap;
    vertical-align: middle;
}
#mediaTable .delete-all {
    width: 80px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
#mediaTable .delete-all input[type="checkbox"] {
    margin: 0;
}
#mediaTable .delete-all .expand-row {
    margin: 0;
}
#mediaTable th:nth-child(2) {
    width: 200px;
}
#mediaTable th:nth-child(3) {
    width: 150px;
}
#mediaTable th:nth-child(4) {
    width: 100px;
}
.action-btn {
    white-space: nowrap;
}
</style>
<script>
var selectedMedia = new Set();

function formatExpandRow(data) {
    return `
        <div class="p-2">
            <strong>Media Details:</strong>
            <ul class="list-unstyled mt-2">
                <li><strong>Name:</strong> ${data.name || 'N/A'}</li>
                <li><strong>Slug:</strong> ${data.slug || 'N/A'}</li>
                <li><strong>Created:</strong> ${data.created_at ? new Date(data.created_at.seconds * 1000).toLocaleDateString() : 'N/A'}</li>
                <li><strong>Updated:</strong> ${data.updated_at ? new Date(data.updated_at.seconds * 1000).toLocaleDateString() : 'N/A'}</li>
            </ul>
        </div>
    `;
}

async function buildHTML(val) {
    var html = [];
    var id = val.id;
    var route1 = '{{route("media.edit",":id")}}';
    route1 = route1.replace(':id', id);

    // Checkbox column with expand button - same structure as restaurants
    html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" name="record" class="is_open" dataId="' + id + '"><label class="col-3 control-label" for="is_open_' + id + '" ></label><button class="expand-row" data-id="' + id + '" tabindex="-1" style="width: 18px; height: 18px; border-radius: 50%; background-color: #28a745; border: 2px solid #ffffff; display: inline-flex; align-items: center; justify-content: center; padding: 0; margin-left: 5px; position: relative; z-index: 1;"><i class="fa fa-plus" style="color: white; font-size: 8px;"></i></button></td>');

    // Media Info column - same structure as restaurants
    var mediaInfo = '';
    if (val.image_path && val.image_path != '') {
        mediaInfo += '<img src="' + val.image_path + '" style="width:70px;height:70px;border-radius:5px;" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">';
    } else {
        mediaInfo += '<img src="' + placeholderImage + '" style="width:70px;height:70px;border-radius:5px;">';
    }
    if(val.name != " " && val.name != "null" && val.name != null && val.name != ""){
        mediaInfo += '<a href="' + route1 + '">' + val.name + '</a>';
    }else{
        mediaInfo += 'UNKNOWN';
    }
    html.push(mediaInfo);

    // Slug column
    html.push(val.slug || '');

    // Actions column - same structure as restaurants
    var actionHtml = '<span class="action-btn">';
    actionHtml += '<a href="' + route1 + '" class="link-td"><i class="mdi mdi-lead-pencil" title="Edit"></i></a>';
    if (val.image_path && val.image_path != '') {
        actionHtml += '<a href="javascript:void(0)" class="copy-image-path" data-image-path="' + val.image_path + '" title="Copy Image Path"><i class="mdi mdi-content-copy"></i></a>';
    }
    actionHtml += '<a id="' + id + '" name="media-delete" href="javascript:void(0)" class="delete-btn"><i class="mdi mdi-delete" title="Delete"></i></a>';
    actionHtml += '</span>';
    html.push(actionHtml);

    return html;
}

$(document).ready(function () {
    var fieldConfig = {
        columns: [
            { key: 'name', header: "{{trans('Media Info')}}" },
            { key: 'slug', header: "{{trans('Slug')}}" },
        ],
        fileName: "{{trans('Media List')}}",
    };

    jQuery("#data-table_processing").show();
    var table = $('#mediaTable').DataTable({
        pageLength: 10,
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: function(data, callback){
            const params = { start: data.start, length: data.length, draw: data.draw, search: data.search.value };
            $.get('{{ route('media.data') }}', params, function(json){
                $('.media_count').text(json.recordsTotal || 0);
                callback(json);
            }).fail(function(xhr){ alert('Failed to load ('+xhr.status+'): '+xhr.statusText); })
              .always(function(){ $('#data-table_processing').hide(); });
        },
        order: [1, 'asc'],
        columnDefs: [
            {orderable: false, targets: [0, 3]}
        ],
        "language": {
            "zeroRecords": "{{trans('lang.no_record_found')}}",
            "emptyTable": "{{trans('lang.no_record_found')}}",
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
        initComplete: function() {
            $(".dataTables_filter").append($(".dt-buttons").detach());
            $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password').val('');
            $('.dataTables_filter label').contents().filter(function() {
                return this.nodeType === 3;
            }).remove();
        }
    });

    // Select all logic
    $("#select-all").change(function () {
        var isChecked = $(this).prop('checked');
        $('input[type="checkbox"][name="record"]').prop('checked', isChecked);
    });

    // Row checkbox logic
    $('#mediaTable tbody').on('change', '.is_open', function () {
        var id = $(this).attr('dataId');
        if (this.checked) {
            selectedMedia.add(id);
        } else {
            selectedMedia.delete(id);
        }
        $('#select-all').prop('checked', $('.is_open:checked').length === $('.is_open').length);
    });

    // Remove expand since server data does not include it

    // Single delete
    $('#mediaTable tbody').on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        var mediaName = $(this).closest('tr').find('a').text().trim() || 'Unknown';
        if (!confirm('Are you sure you want to delete "' + mediaName + '"?')) return;
        jQuery('#data-table_processing').show();
        $.post({ url: '{{ url('media') }}' + '/' + id + '/delete', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(){ table.ajax.reload(); })
            .fail(function(xhr){ alert('Error deleting media: ' + xhr.statusText); })
            .always(function(){ jQuery('#data-table_processing').hide(); });
    });

    // Bulk delete
    $("#deleteAll").click(async function () {
        var selectedCount = $('#mediaTable .is_open:checked').length;

        if (selectedCount === 0) {
            alert("{{trans('lang.selected_delete_alert')}}");
            return;
        }

        if (!confirm("{{trans('lang.selected_delete_alert')}}")) return;
        jQuery('#data-table_processing').show();
        var ids = [];
        $('#mediaTable .is_open:checked').each(function () { ids.push($(this).attr('dataId')); });
        $.post({ url: '{{ route('media.bulkDelete') }}', data: { ids: ids }, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(){ table.ajax.reload(); })
            .fail(function(xhr){ alert('Error deleting selected media: ' + xhr.statusText); })
            .always(function(){ jQuery('#data-table_processing').hide(); });
    });

    // Search functionality with debounce
    $('#search-input').on('input', debounce(function() {
        const searchValue = $(this).val();
        if (searchValue.length >= 3) {
            $('#data-table_processing').show();
            table.search(searchValue).draw();
        } else if (searchValue.length === 0) {
            $('#data-table_processing').show();
            table.search('').draw();
        }
    }, 300));

    // Copy image path to clipboard (works if we render copy icon)
    $('#mediaTable tbody').on('click', '.copy-image-path', function() {
        var imagePath = $(this).data('image-path');
        if (imagePath) {
            // Create a temporary textarea element
            var tempTextArea = document.createElement('textarea');
            tempTextArea.value = imagePath;
            document.body.appendChild(tempTextArea);
            tempTextArea.select();
            tempTextArea.setSelectionRange(0, 99999); // For mobile devices

            try {
                // Copy the text to clipboard
                document.execCommand('copy');

                // Show success feedback
                var $btn = $(this);
                var originalIcon = $btn.find('i').attr('class');
                $btn.find('i').removeClass().addClass('mdi mdi-check text-success');
                $btn.attr('title', 'Copied!');

                // Reset after 2 seconds
                setTimeout(function() {
                    $btn.find('i').removeClass().addClass(originalIcon);
                    $btn.attr('title', 'Copy Image Path');
                }, 2000);

            } catch (err) {
                console.error('Failed to copy: ', err);
                alert('Failed to copy image path to clipboard');
            }

            // Remove the temporary element
            document.body.removeChild(tempTextArea);
        }
    });
});

function debounce(func, wait) {
    let timeout;
    const context = this;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}
</script>
@endsection
