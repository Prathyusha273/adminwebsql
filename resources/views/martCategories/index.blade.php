@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
                            <h3 class="text-themecolor">Mart Categories</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">Mart Categories</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/category.png') }}"></span>
                        <h3 class="mb-0">Mart Categories Table</h3>
                        <span class="counter ml-3 category_count"></span>
                    </div>
                    <div class="d-flex top-title-right align-self-center">
                        <div class="select-box pl-3">
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
                                <h3 class="text-dark-2 mb-2 h4">Bulk Import Mart Categories</h3>
                                <p class="mb-0 text-dark-2">Upload Excel file to import multiple mart categories at once</p>
                                <small class="text-info">
                                    <i class="mdi mdi-lightbulb-outline mr-1"></i>
                                    <strong>Tip:</strong> For photos, use media names, slugs, or direct URLs from the media module!
                                </small>
                                <br><small class="text-success">
                                    <i class="mdi mdi-shield-check mr-1"></i>
                                    <strong>Smart Media Protection:</strong> Images are only deleted when no other items reference them!
                                </small>
                            </div>
                <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <a href="{{ route('mart-categories.download-template') }}" class="btn btn-outline-primary rounded-full">
                            <i class="mdi mdi-download mr-2"></i>Download Template
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('mart-categories.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="importFile" class="control-label">Select Excel File (.xls/.xlsx)</label>
                                <input type="file" name="file" id="importFile" accept=".xls,.xlsx" class="form-control" required>
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-information-outline mr-1"></i>
                                    File should contain: title, description, photo (media name/slug/URL), section, category_order, publish, show_in_homepage, mart_id, review_attributes
                                    <br><small class="text-success">
                                        <i class="mdi mdi-check-circle mr-1"></i>
                                        <strong>Advanced Media Integration:</strong> Supports media names, slugs, image names, direct URLs, and Firebase Storage URLs!
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary rounded-full">
                                <i class="mdi mdi-upload mr-2"></i>Import Mart Categories
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
                    <h3 class="text-dark-2 mb-2 h4">Mart Categories Table</h3>
                    <p class="mb-0 text-dark-2">Manage all mart categories in the system</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <a class="btn-primary btn rounded-full" href="{!! route('mart-categories.create') !!}"><i class="mdi mdi-plus mr-2"></i>Create Mart Category</a>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="categoriesTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (in_array('mart-categories.delete', json_decode(@session('user_permissions'),true))) { ?>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                            <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label></th>
                                    <?php } ?>
                                    <th>Mart Category Name</th>
                                    <th>Section</th>
                                    <th>Sub-Categories</th>
                                    <th>Mart Items</th>
                                    <th>Published</th>
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
    var placeholderImage = '{{ asset("images/placeholder.png") }}';
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;

    if ($.inArray('mart-categories.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }

    $(document).ready(function () {
        jQuery("#data-table_processing").show();

        const table = $('#categoriesTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;

                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                // Make AJAX request to Laravel backend
                console.log('🔗 Calling API:', '{{ route("api.mart-categories.get-data") }}');

                $.ajax({
                    url: '{{ route("api.mart-categories.get-data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start: start,
                        length: length,
                        search: { value: searchValue },
                        order: [{ column: orderColumnIndex, dir: orderDirection }]
                    },
                    success: function(response) {
                        console.log('📦 Categories API response:', response);
                        console.log('📊 Records total:', response.recordsTotal);
                        console.log('📋 Data items:', response.data ? response.data.length : 0);

                        let records = [];
                        let totalRecords = response.recordsTotal || 0;

                        $('.category_count').text(totalRecords);

                        if (!response.data || !Array.isArray(response.data)) {
                            console.error('❌ Response data is not an array!', response.data);
                            $('#data-table_processing').hide();
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: []
                            });
                            return;
                        }

                        response.data.forEach(function(childData) {
                            var id = childData.id;
                            var route1 = '{{route("mart-categories.edit",":id")}}';
                            route1 = route1.replace(':id', id);
                            var url = '{{url("mart-items?categoryID=id")}}';
                            url = url.replace("id", id);

                            var ImageHtml = childData.photo == '' || childData.photo == null
                                ? '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">'
                                : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + childData.photo + '" alt="image">';

                            var subcategoryLink = '{{ route("mart-subcategories.index", ["category_id" => ":category_id"]) }}'.replace(':category_id', childData.id);
                            var subcategoryCount = childData.subcategories_count || 0;
                            var subcategoryHtml = '<a href="' + subcategoryLink + '" class="text-primary">' + subcategoryCount + ' sub-categories</a>';

                            var rowData = [];

                            // Add checkbox column only if user has delete permission
                            if (checkDeletePermission) {
                                rowData.push('<td class="delete-all"><input type="checkbox" id="is_open_' + childData.id + '" class="is_open" dataId="' + childData.id + '"><label class="col-3 control-label" for="is_open_' + childData.id + '" ></label></td>');
                            }

                            // Add remaining columns
                            rowData.push(ImageHtml + '<a href="' + route1 + '">' + childData.title + '</a>');
                            rowData.push('<span class="badge badge-secondary">' + (childData.section || 'General') + '</span>');
                            rowData.push(subcategoryHtml);
                            rowData.push('<a href="' + url + '">' + childData.totalProducts + '</a>');
                            rowData.push(childData.publish ? '<label class="switch"><input type="checkbox" checked id="' + childData.id + '" name="isSwitch"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + childData.id + '" name="isSwitch"><span class="slider round"></span></label>');
                            rowData.push('<span class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a> <a href="' + subcategoryLink + '" class="btn btn-sm btn-info mr-1"><i class="mdi mdi-folder-multiple"></i> Manage</a><?php if(in_array('mart-categories.delete', json_decode(@session('user_permissions'),true))){ ?> <a id="' + childData.id + '" name="category-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a><?php } ?></span>');

                            records.push(rowData);
                        });

                        console.log('✅ Built ' + records.length + ' table rows');

                        $('#data-table_processing').hide();

                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords,
                            recordsFiltered: totalRecords,
                            data: records
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("❌ Error fetching data:", error);
                        console.error("Status:", xhr.status);
                        console.error("Response:", xhr.responseText);
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
            order: (checkDeletePermission) ? [1, 'asc'] : [0,'asc'],
            columnDefs: [
                { orderable: false, targets: (checkDeletePermission) ? [0,3,4] : [2, 3] },
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": ""
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

    // Delete single category
    $(document).on("click", "a[name='category-delete']", async function (e) {
        var id = this.id;

        if (!confirm('Are you sure you want to delete this mart category?')) {
            return;
        }

        jQuery("#data-table_processing").show();

        $.ajax({
            url: '/api/mart-categories/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                jQuery("#data-table_processing").hide();
                window.location.href = '{{ route("mart-categories")}}';
            },
            error: function(xhr, status, error) {
                jQuery("#data-table_processing").hide();
                alert('Error deleting category: ' + error);
            }
        });
    });

    // Select all checkbox
    $("#is_active").click(function () {
        $("#categoriesTable .is_open").prop('checked', $(this).prop('checked'));
    });

    // Delete multiple categories
    $("#deleteAll").click(async function () {
        if ($('#categoriesTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();

                var selectedIds = [];
                $('#categoriesTable .is_open:checked').each(function() {
                    selectedIds.push($(this).attr('dataId'));
                });

                $.ajax({
                    url: '{{ route("api.mart-categories.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        jQuery("#data-table_processing").hide();
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        jQuery("#data-table_processing").hide();
                        alert('Error deleting categories: ' + error);
                    }
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    // Toggle publish status
    $(document).on("click", "input[name='isSwitch']", async function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;

        $.ajax({
            url: '/api/mart-categories/' + id + '/toggle-publish',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                publish: ischeck ? 1 : 0
            },
            success: function(response) {
                console.log('Publish status updated successfully');
            },
            error: function(xhr, status, error) {
                alert('Error updating publish status: ' + error);
            }
        });
    });
</script>
@endsection
