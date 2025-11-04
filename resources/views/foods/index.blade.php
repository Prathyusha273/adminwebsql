@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor restaurantTitle">{{trans('lang.food_plural')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.food_plural')}}</li>
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
                                <span class="icon mr-3"><img src="{{ asset('images/food.png') }}"></span>
                                <h3 class="mb-0">{{trans('lang.food_table')}}</h3>
                                <span class="counter ml-3 food_count"></span>
                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="select-box pl-3">
                                    <select class="form-control food_type_selector">
                                        <option value=""  selected>{{trans("lang.type")}}</option>
                                        <option value="veg">{{trans("lang.veg")}}</option>
                                        <option value="non-veg">{{trans("lang.non_veg")}}</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <select class="form-control restaurant_selector">
                                        <option value=""  selected>{{trans("lang.restaurant")}}</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <select class="form-control category_selector">
                                        <option value=""  selected>{{trans("lang.category_plural")}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Import Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">Bulk Import Foods</h3>
                                <p class="mb-0 text-dark-2">Upload Excel file to import multiple foods at once</p>
                                <small class="text-info">
                                    <i class="mdi mdi-lightbulb-outline mr-1"></i>
                                    <strong>Tip:</strong> You can use vendor names and category names instead of IDs for easier data entry!
                                </small>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a href="{{ route('foods.download-template') }}" class="btn btn-outline-primary rounded-full">
                                        <i class="mdi mdi-download mr-2"></i>Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('foods.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="importFile" class="control-label">Select Excel File (.xls/.xlsx)</label>
                                            <input type="file" name="file" id="importFile" accept=".xls,.xlsx" class="form-control" required>
                                            <div class="form-text text-muted">
                                                <i class="mdi mdi-information-outline mr-1"></i>
                                                File should contain: name, price, description, vendorID/vendorName, categoryID/categoryName, disPrice, publish, nonveg, isAvailable, photo
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary rounded-full">
                                            <i class="mdi mdi-upload mr-2"></i>Import Foods
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
                        <?php if ($id != '') { ?>
                        <div class="menu-tab">
                            <ul>
                                <li>
                                    <a href="{{route('restaurants.view', $id)}}">{{trans('lang.tab_basic')}}</a>
                                </li>
                                <li class="active">
                                    <a href="{{route('restaurants.foods', $id)}}">{{trans('lang.tab_foods')}}</a>
                                </li>
                                <li>
                                    <a href="{{route('restaurants.orders', $id)}}">{{trans('lang.tab_orders')}}</a>
                                </li>
                                <li>
                                    <a href="{{route('restaurants.coupons', $id)}}">{{trans('lang.tab_promos')}}</a>
                                <li>
                                    <a href="{{route('restaurants.payout', $id)}}">{{trans('lang.tab_payouts')}}</a>
                                </li>
                                <li>
                                    <a
                                        href="{{route('payoutRequests.restaurants.view', $id)}}">{{trans('lang.tab_payout_request')}}</a>
                                </li>
                                <li>
                                    <a href="{{route('restaurants.booktable', $id)}}">{{trans('lang.dine_in_future')}}</a>
                                </li>
                                <li id="restaurant_wallet"></li>
                                <li id="subscription_plan"></li>
                            </ul>
                        </div>
                        <?php } ?>
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <div class="card-header-title">
                                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.food_table')}}</h3>
                                    <p class="mb-0 text-dark-2">{{trans('lang.food_table_text')}}</p>
                                </div>
                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                        <?php if ($id != '') { ?>
                                        <a class="btn-primary btn rounded-full"
                                           href="{!! route('foods.create') !!}/{{$id}}"><i
                                                class="mdi mdi-plus mr-2"></i>{{trans('lang.food_create')}}</a>
                                        <?php } else { ?>
                                        <a class="btn-primary btn rounded-full" href="{!! route('foods.create') !!}"><i
                                                class="mdi mdi-plus mr-2"></i>{{trans('lang.food_create')}}</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="foodTable"
                                           class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                           cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <?php if (in_array('foods.delete', json_decode(@session('user_permissions'), true))) { ?>
                                            <th class="delete-all"><input type="checkbox" id="select-all">
                                                <label class="col-3 control-label" for="select-all">
                                                    <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                            class="mdi mdi-delete"></i> {{trans('lang.all')}}</a>
                                                </label>
                                            </th>
                                            <?php } ?>
                                            <th>{{trans('lang.food_name')}}</th>
                                            <th>{{trans('lang.food_price')}}</th>
                                            <th>Discount Price</th>
                                            <?php if ($id == '') { ?>
                                            <th>{{trans('lang.food_restaurant_id')}}</th>
                                            <?php } ?>
                                            <th>{{trans('lang.food_category_id')}}</th>
                                            <th>{{trans('lang.food_publish')}}</th>
                                            <th>Available</th>
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
        const urlParams=new URLSearchParams(location.search);
        var categoryID = '';
        for(const [key,value] of urlParams) {
            if(key=='categoryID') {
                categoryID=value;
            }
        }

        var currentCurrency='$';
        var currencyAtRight=false;
        var decimal_degits=0;
        var user_permissions='<?php echo @session("user_permissions") ?>';
        user_permissions=Object.values(JSON.parse(user_permissions));
        var checkDeletePermission=false;
        if($.inArray('foods.delete',user_permissions)>=0) {
            checkDeletePermission=true;
        }
        var restaurantID="{{$id}}";
        var placeholderImage='{{ asset('images/placeholder.png') }}';

        // Fetch currency from SQL
        $.ajax({
            url: '{{ route("settings.get", "payment") }}',
            type: 'GET',
            async: false,
            success: function(response) {
                if(response.success && response.data) {
                    currentCurrency = response.data.currency_symbol || '$';
                    currencyAtRight = response.data.currencyAtRight || false;
                    decimal_degits = response.data.decimal_degits || 0;
                }
            }
        });

        // Load restaurants for filter dropdown from SQL
        $.ajax({
            url: '{{ route("foods.options") }}?type=restaurants',
            type: 'GET',
            success: function(response) {
                if(response.success && response.data) {
                    response.data.forEach(function(restaurant) {
                        if(restaurant.title && restaurant.title !== '') {
                            $('.restaurant_selector').append($("<option></option>")
                                .attr("value", restaurant.id)
                                .text(restaurant.title));
                        }
                    });
                }
            }
        });

        // Load categories for filter dropdown from SQL
        $.ajax({
            url: '{{ route("foods.options") }}?type=categories',
            type: 'GET',
            success: function(response) {
                if(response.success && response.data) {
                    response.data.forEach(function(category) {
                        $('.category_selector').append($("<option></option>")
                            .attr("value", category.id)
                            .text(category.title));
                    });
                }
            }
        });

        // Filter change handler
        $('select').change(function() {
            $('#foodTable').DataTable().ajax.reload();
        });

        $(document).ready(function() {
            $('.restaurant_selector').select2({
                placeholder: "{{trans('lang.restaurant')}}",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('.food_type_selector').select2({
                placeholder: "{{trans('lang.type')}}",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('.category_selector').select2({
                placeholder: "{{trans('lang.category')}}",
                minimumResultsForSearch: Infinity,
                allowClear: true
            });
            $('select').on("select2:unselecting", function(e) {
                var self = $(this);
                setTimeout(function() {
                    self.select2('close');
                }, 0);
            });

            jQuery("#data-table_processing").show();

            const table=$('#foodTable').DataTable({
                pageLength: 10,
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("foods.data") }}',
                    type: 'GET',
                    data: function(d) {
                        d.restaurant = $('.restaurant_selector').val();
                        d.category = $('.category_selector').val();
                        d.foodType = $('.food_type_selector').val();
                        d.categoryId = categoryID;
                        d.restaurantId = restaurantID;
                    },
                    dataSrc: function(json) {
                        $('#data-table_processing').hide();
                        $('.food_count').text(json.recordsFiltered);
                        return json.data;
                    }
                },
                order: (checkDeletePermission)? [1,'asc']:[0,'asc'],
                columns: [
                    @if(in_array('foods.delete', json_decode(@session('user_permissions'), true)))
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<td class="delete-all"><input type="checkbox" id="is_open_'+row.id+'" class="is_open" dataId="'+row.id+'"><label class="col-3 control-label" for="is_open_'+row.id+'"></label></td>';
                        }
                    },
                        @endif
                    {
                        data: 'name',
                        render: function(data, type, row) {
                            var imageHtml = '';
                            if(row.photo && row.photo != '') {
                                imageHtml = '<img onerror="this.onerror=null;this.src=\''+placeholderImage+'\'" class="rounded" style="width:70px;height:70px;object-fit:cover;" src="'+row.photo+'" alt="image">';
                            } else {
                                imageHtml = '<img style="width:70px;height:70px;" src="'+placeholderImage+'" alt="image">';
                            }
                            var foodType = row.nonveg ? '<span class="badge badge-danger ml-2">Non-Veg</span>' : '<span class="badge badge-success ml-2">Veg</span>';
                            return '<div class="d-flex align-items-center">' + imageHtml + '<div class="ml-3">' + data + foodType + '</div></div>';
                        }
                    },
                    {
                        data: 'price',
                        render: function(data, type, row) {
                            if (data == null || data == '' || data == '0') {
                                return '-';
                            }
                            var price = parseFloat(data).toFixed(decimal_degits);
                            return currencyAtRight ? price + ' ' + currentCurrency : currentCurrency + ' ' + price;
                        }
                    },
                    {
                        data: 'disPrice',
                        render: function(data, type, row) {
                            if (data == null || data == '' || data == '0') {
                                return '-';
                            }
                            var price = parseFloat(data).toFixed(decimal_degits);
                            return currencyAtRight ? price + ' ' + currentCurrency : currentCurrency + ' ' + price;
                        }
                    },
                    @if($id == '')
                    {
                        data: 'restaurant_name',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    @endif
                    {
                        data: 'category_name',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'publish',
                        orderable: false,
                        render: function(data, type, row) {
                            var checked = data ? 'checked' : '';
                            return '<label class="switch"><input type="checkbox" ' + checked + ' id="publish_'+row.id+'" data-id="'+row.id+'"><span class="slider round"></span></label>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            // Assuming isAvailable field exists
                            var isAvailable = row.isAvailable !== false;
                            var checked = isAvailable ? 'checked' : '';
                            return '<label class="switch"><input type="checkbox" ' + checked + ' id="available_'+row.id+'" data-id="'+row.id+'"><span class="slider round"></span></label>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            var route1 = '{{route("foods.edit", ":id")}}'.replace(':id', row.id);
                            @if($id != '')
                                route1 += '?eid={{$id}}';
                            @endif
                            return '<span class="action-btn"><a href="'+route1+'"><i class="mdi mdi-pencil font-18"></i></a></span>';
                        }
                    }
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: (restaurantID=='')? ((checkDeletePermission)? [0,6,7]:[5,6]):((checkDeletePermission)? [0,5,6]:[4,5,6])
                    }
                ],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}",
                    "processing": ""
                },
                dom: 'lfrtipB',
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="mdi mdi-cloud-download"></i> Export as',
                        className: 'btn btn-info',
                        buttons: ['csv', 'excel', 'pdf']
                    }
                ],
                initComplete: function() {
                    $(".dataTables_filter").append($(".dt-buttons").detach());
                    $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password');
                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
            });

            // Handle publish toggle
            $(document).on('change', 'input[id^="publish_"]', function() {
                var id = $(this).data('id');
                var isPublished = $(this).is(':checked');

                $.ajax({
                    url: '{{ route("foods.toggle", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        publish: isPublished
                    },
                    success: function(response) {
                        if(response.success) {
                            console.log('Publish status updated');
                        }
                    },
                    error: function() {
                        console.error('Error updating publish status');
                        // Revert checkbox
                        $('input[id="publish_'+id+'"]').prop('checked', !isPublished);
                    }
                });
            });

            // Select all checkboxes
            $("#select-all").click(function() {
                $(".is_open").prop('checked', $(this).prop('checked'));
            });

            // Delete selected items
            $("#deleteAll").click(function() {
                if ($('.is_open:checked').length == 0) {
                    alert("{{trans('lang.select_delete_alert')}}");
                    return false;
                }
                if(!confirm("{{trans('lang.delete_alert')}}")){
                    return false;
                }

                var selectedIds = [];
                $('.is_open:checked').each(function() {
                    selectedIds.push($(this).attr('dataId'));
                });

                jQuery("#data-table_processing").show();

                // Delete via AJAX (you'll need to create a batch delete endpoint)
                $.ajax({
                    url: '{{ url("foods/delete-multiple") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        jQuery("#data-table_processing").hide();
                        if(response.success) {
                            alert('{{trans("lang.delete_success")}}');
                            table.ajax.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        jQuery("#data-table_processing").hide();
                        alert('{{trans("lang.error_deleting")}}');
                    }
                });
            });
        });
    </script>
@endsection
