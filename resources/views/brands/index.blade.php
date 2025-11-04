@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.brands')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.brands')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
       <div class="admin-top-section">
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/brand.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.brands_table')}}</h3>
                        <span class="counter ml-3 brand_count"></span>
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
                    <h3 class="text-dark-2 mb-2 h4">Bulk Import Brands</h3>
                    <p class="mb-0 text-dark-2">Upload Excel or CSV file to import multiple brands at once</p>
                </div>
                <div class="card-header-right d-flex align-items-center">
                    <?php if (in_array('brands.create', json_decode(@session('user_permissions'), true))) { ?>
                    <div class="card-header-btn mr-3">
                        <div class="btn-group" role="group">
                            <a href="{{ route('brands.download-template', ['format' => 'excel']) }}" class="btn btn-outline-success rounded-full">
                                <i class="mdi mdi-download mr-2"></i>Excel Template
                            </a>
                            <a href="{{ route('brands.download-template', ['format' => 'csv']) }}" class="btn btn-outline-primary rounded-full">
                                <i class="mdi mdi-download mr-2"></i>CSV Template
                            </a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('brands.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="importFile" class="control-label">Select File (.csv/.xls/.xlsx)</label>
                                <input type="file" name="file" id="importFile" accept=".csv,.xls,.xlsx" class="form-control" required>
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-information-outline mr-1"></i>
                                    File should contain: name, slug, description, status, logo_url
                                    <br><strong>Recommended:</strong> Use Excel format for better formatting and instructions.
                                    <br><strong>Note:</strong> Use CSV format if you encounter ZipArchive errors with Excel files.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <?php if (in_array('brands.create', json_decode(@session('user_permissions'), true))) { ?>
                            <button type="submit" class="btn btn-primary rounded-full">
                                <i class="mdi mdi-upload mr-2"></i>Import Brands
                            </button>
                            <?php } ?>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.brands_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.brands_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <?php if (in_array('brands.create', json_decode(@session('user_permissions'), true))) { ?>
                        <a class="btn-primary btn rounded-full" href="{!! route('brands.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.add_brand')}}</a>
                        <?php } ?>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="brandsTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (in_array('brands.delete', json_decode(@session('user_permissions'),true))) { ?>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active">
                                            <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label></th>
                                    <?php } ?>
                                    <th>{{trans('lang.brand_name')}}</th>
                                    <th>{{trans('lang.brand_slug')}}</th>
                                    <th>{{trans('lang.brand_logo')}}</th>
                                    <th>{{trans('lang.brand_description')}}</th>
                                    <th> {{trans('lang.status')}}</th>
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
    var database = firebase.firestore();
    var ref = database.collection('brands').orderBy('name');
    var placeholderImage = '{{ asset('images/placeholder.png') }}';
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('brands.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    $(document).ready(function () {
        jQuery("#data-table_processing").show();
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        });
        const table = $('#brandsTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns = (checkDeletePermission) ? ['','name', 'slug', 'logo_url', 'description', 'status',''] : ['name', 'slug', 'logo_url', 'description', 'status','']; // Ensure this matches the actual column names
                const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }
                ref.get().then(async function (querySnapshot) {
                    if (querySnapshot.empty) {
                        $('.brand_count').text(0);
                        console.error("No data found in Firestore.");
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data
                        });
                        return;
                    }
                    let records = [];
                    let filteredRecords = [];
                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data
                        if (searchValue) {
                            if (
                                (childData.name && childData.name.toString().toLowerCase().includes(searchValue)) ||
                                (childData.slug && childData.slug.toString().toLowerCase().includes(searchValue)) ||
                                (childData.description && childData.description.toString().toLowerCase().includes(searchValue))
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));
                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                        let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                        if (orderByField === 'status') {
                            aValue = a[orderByField] ? 1 : 0;
                            bValue = b[orderByField] ? 1 : 0;
                        }
                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });
                    const totalRecords = filteredRecords.length;
                    $('.brand_count').text(totalRecords);
                    filteredRecords.slice(start, start + length).forEach(function (childData) {
                        var id = childData.id;
                        var route1 = '{{route("brands.edit",":id")}}';
                        route1 = route1.replace(':id', id);
                        var ImageHtml=childData.logo_url == '' || childData.logo_url == null ? '<img alt="" width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">' : '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="" width="100%" style="width:70px;height:70px;" src="' + childData.logo_url + '" alt="image">'
                        records.push([
                            checkDeletePermission ? '<td class="delete-all"><input type="checkbox" id="is_open_' + childData.id + '" class="is_open" dataId="' + childData.id + '"><label class="col-3 control-label"\n' + 'for="is_open_' + childData.id + '" ></label></td>' : '',
                            '<a href="' + route1 + '">' + childData.name + '</a>',
                            childData.slug || '-',
                            ImageHtml,
                            childData.description || '-',
                            childData.status ? '<label class="switch"><input type="checkbox" checked id="' + childData.id + '" name="isSwitch"><span class="slider round"></span></label>' : '<label class="switch"><input type="checkbox" id="' + childData.id + '" name="isSwitch"><span class="slider round"></span></label>',
                            '<span class="action-btn"><?php if (in_array('brands.edit', json_decode(@session('user_permissions'), true))) { ?><a href="' + route1 + '"><i class="mdi mdi-lead-pencil" title="Edit"></i></a><?php } ?><?php if (in_array('brands.delete', json_decode(@session('user_permissions'), true))) { ?> <a id="' + childData.id + '" name="brand-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a><?php } ?></span>'
                        ]);
                    });
                    $('#data-table_processing').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, // Total number of records in Firestore
                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                        data: records // The actual data to display in the table
                    });
                }).catch(function (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#data-table_processing').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: [] // No data due to error
                    });
                });
            },
            order: (checkDeletePermission) ? [1, 'asc'] : [0,'asc'],
            columnDefs: [
                { orderable: false, targets: (checkDeletePermission) ? [0,2,6] : [2, 5] },
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
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
    $(document).on("click", "a[name='brand-delete']", async function (e) {
        var id = this.id;
        var brandTitle = '';
        var logoUrl = '';

        try {
            var doc = await database.collection('brands').doc(id).get();
            if (doc.exists) {
                var data = doc.data();
                brandTitle = data.name || 'Unknown';
                logoUrl = data.logo_url || '';
            }
        } catch (error) {
            console.error('Error getting brand data:', error);
        }

        if (confirm("{{trans('lang.are_you_sure')}}")) {
            jQuery("#data-table_processing").show();

            try {
                // Delete logo from storage if exists
                if (logoUrl && logoUrl !== '') {
                    try {
                        var imageRef = firebase.storage().refFromURL(logoUrl);
                        await imageRef.delete();
                        console.log('✅ Logo deleted from storage');
                    } catch (storageError) {
                        console.log('⚠️ Could not delete logo from storage:', storageError);
                    }
                }

                // Delete document from Firestore
                await database.collection('brands').doc(id).delete();
                console.log('✅ Brand deleted from Firestore');

                // Log activity
                try {
                    if (typeof logActivity === 'function') {
                        await logActivity('brands', 'deleted', 'Deleted brand: ' + brandTitle);
                        console.log('✅ Activity logged successfully');
                    }
                } catch (error) {
                    console.error('❌ Error logging activity:', error);
                }

                // Reload table data instead of full page reload
                $('#brandsTable').DataTable().ajax.reload();
                jQuery("#data-table_processing").hide();

            } catch (error) {
                console.error('❌ Error deleting brand:', error);
                alert('Error deleting brand. Please try again.');
                jQuery("#data-table_processing").hide();
            }
        }
    });
    $("#is_active").click(function () {
        $("#brandsTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(async function () {
        if ($('#brandsTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                var selectedBrands = [];
                var selectedIds = [];

                // Collect brand data
                for (let i = 0; i < $('#brandsTable .is_open:checked').length; i++) {
                    var dataId = $('#brandsTable .is_open:checked').eq(i).attr('dataId');
                    selectedIds.push(dataId);
                    try {
                        var doc = await database.collection('brands').doc(dataId).get();
                        if (doc.exists) {
                            selectedBrands.push(doc.data().name || 'Unknown');
                        }
                    } catch (error) {
                        console.error('Error getting brand title:', error);
                    }
                }

                try {
                    // Delete all brands in parallel for better performance
                    var deletePromises = selectedIds.map(async (dataId) => {
                        try {
                            // Get brand data first
                            var doc = await database.collection('brands').doc(dataId).get();
                            var logoUrl = '';
                            if (doc.exists) {
                                logoUrl = doc.data().logo_url || '';
                            }

                            // Delete logo from storage if exists
                            if (logoUrl && logoUrl !== '') {
                                try {
                                    var imageRef = firebase.storage().refFromURL(logoUrl);
                                    await imageRef.delete();
                                } catch (storageError) {
                                    console.log('⚠️ Could not delete logo from storage for brand:', dataId);
                                }
                            }

                            // Delete document from Firestore
                            await database.collection('brands').doc(dataId).delete();
                            return true;
                        } catch (error) {
                            console.error('❌ Error deleting brand:', dataId, error);
                            return false;
                        }
                    });

                    // Wait for all deletions to complete
                    await Promise.all(deletePromises);
                    console.log('✅ Bulk brand deletion completed');

                    // Log activity
                    try {
                        if (typeof logActivity === 'function') {
                            await logActivity('brands', 'bulk_deleted', 'Bulk deleted brands: ' + selectedBrands.join(', '));
                            console.log('✅ Activity logged successfully');
                        }
                    } catch (error) {
                        console.error('❌ Error logging activity:', error);
                    }

                    // Reload table data instead of full page reload
                    $('#brandsTable').DataTable().ajax.reload();
                    jQuery("#data-table_processing").hide();

                } catch (error) {
                    console.error('❌ Bulk deletion failed:', error);
                    alert('Some brands could not be deleted. Please try again.');
                    jQuery("#data-table_processing").hide();
                }
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
    $(document).on("click", "input[name='isSwitch']", async function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        var brandTitle = '';
        try {
            var doc = await database.collection('brands').doc(id).get();
            if (doc.exists) {
                brandTitle = doc.data().name || 'Unknown';
            }
        } catch (error) {
            console.error('Error getting brand title:', error);
        }
        if (ischeck) {
            database.collection('brands').doc(id).update({'status': true}).then(async function (result) {
                console.log('✅ Brand published successfully, now logging activity...');
                try {
                    if (typeof logActivity === 'function') {
                        console.log('🔍 Calling logActivity for brand publish...');
                        await logActivity('brands', 'published', 'Published brand: ' + brandTitle);
                        console.log('✅ Activity logging completed successfully');
                    } else {
                        console.error('❌ logActivity function is not available');
                    }
                } catch (error) {
                    console.error('❌ Error calling logActivity:', error);
                }
            });
        } else {
            database.collection('brands').doc(id).update({'status': false}).then(async function (result) {
                console.log('✅ Brand unpublished successfully, now logging activity...');
                try {
                    if (typeof logActivity === 'function') {
                        console.log('🔍 Calling logActivity for brand unpublish...');
                        await logActivity('brands', 'unpublished', 'Unpublished brand: ' + brandTitle);
                        console.log('✅ Activity logging completed successfully');
                    } else {
                        console.error('❌ logActivity function is not available');
                    }
                } catch (error) {
                    console.error('❌ Error calling logActivity:', error);
                }
            });
        }
    });
</script>
@endsection
