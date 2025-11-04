@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.restaurants_payout_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.restaurants_payout_table')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/payment.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.restaurants_payout_plural')}}</h3>
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
                <?php if ($id != '') { ?>
                    <div class="menu-tab">
                        <ul>
                            <li>
                                <a href="{{route('restaurants.view',$id)}}">{{trans('lang.tab_basic')}}</a>
                            </li>
                            <li>
                                <a href="{{route('restaurants.foods',$id)}}">{{trans('lang.tab_foods')}}</a>
                            </li>
                            <li>
                                <a href="{{route('restaurants.orders',$id)}}">{{trans('lang.tab_orders')}}</a>
                            </li>
                            <li>
                                <a href="{{route('restaurants.coupons',$id)}}">{{trans('lang.tab_promos')}}</a>
                            <li class="active">
                                <a href="{{route('restaurants.payout',$id)}}">{{trans('lang.tab_payouts')}}</a>
                            </li>
                            <li>
                                <a
                                    href="{{route('payoutRequests.restaurants.view',$id)}}">{{trans('lang.tab_payout_request')}}</a>
                            </li>
                            <li>
                                <a href="{{route('restaurants.booktable',$id)}}">{{trans('lang.dine_in_future')}}</a>
                            </li>
                            <li id="restaurant_wallet"></li>
                            <li id="subscription_plan"></li>
                        </ul>
                    </div>
                <?php } ?>
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.restaurants_payout_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.restaurant_payouts_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <?php if ($id != '') { ?>
                            <a class="btn-primary btn rounded-full" href="{!! route('restaurantsPayouts.create') !!}/{{$id}}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.restaurants_payout_create')}}</a>
                        <?php } else { ?>
                            <a class="btn-primary btn rounded-full" href="{!! route('restaurantsPayouts.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.restaurants_payout_create')}}</a>
                        <?php } ?>
                    </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                         <table id="restaurantPayoutTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>
                                        <?php if ($id == '') { ?>
                                            <th>{{ trans('lang.restaurant')}}</th>
                                        <?php } ?>
                                        <th>{{trans('lang.paid_amount')}}</th>
                                        <th>{{trans('lang.date')}}</th>
                                        <th>{{trans('lang.restaurants_payout_note')}}</th>
                                        <th>{{trans('lang.admin_note')}}</th>
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
<script>
    var intRegex = /^\d+$/;
    var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
    var getId = '{{$id}}';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;

    // Fetch currency settings from Laravel backend
    $.ajax({
        url: '{{ route("payments.currency") }}',
        method: 'GET',
        async: false,
        success: function(response) {
            if (response.success) {
                currentCurrency = response.data.symbol;
                currencyAtRight = response.data.symbolAtRight;
                decimal_degits = response.data.decimal_degits || 0;
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching currency:', error);
        }
    });

    <?php if ($id != '') { ?>
        // Fetch vendor details for the tab
        $.ajax({
            url: '{{ route("restaurantsPayouts.vendor", ":id") }}'.replace(':id', '<?php echo $id; ?>'),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    var vendorData = response.data;
                    walletRoute = "{{route('users.walletstransaction',':id')}}";
                    walletRoute = walletRoute.replace(":id", vendorData.author);
                    $('#restaurant_wallet').append('<a href="' + walletRoute + '">{{trans("lang.wallet_transaction")}}</a>');
                    $('#subscription_plan').append('<a href="' + "{{route('vendor.subscriptionPlanHistory', ':id')}}".replace(':id', vendorData.author) + '">' + '{{trans('lang.subscription_history')}}' + '</a>');

                    if (vendorData.title) {
                        $('.restaurantTitle').html('{{trans("lang.restaurants_payout_plural")}} - ' + vendorData.title);
                    }
                    if (vendorData.dine_in_active == true) {
                        $(".dine_in_future").show();
                    }
                }
            }
        });
    <?php } ?>
    var append_list = '';
    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#data-table_processing").show();
        $(document).on('click', '.dt-button-collection .dt-button', function () {
            $('.dt-button-collection').hide();
            $('.dt-button-background').hide();
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            }
        });
        var fieldConfig = {
            columns: [
                { key: 'restaurant', header: "{{ trans('lang.restaurant')}}" },
                { key: 'paidamount', header: "{{trans('lang.paid_amount')}}" },
                { key: 'paidDate', header: "{{trans('lang.date')}}" },
                { key: 'adminNote', header: "{{trans('lang.admin_note')}}" },
                { key: 'note', header: "{{trans('lang.restaurants_payout_note')}}" },
            ],
            fileName: "{{trans('lang.restaurants_payout_table')}}",
        };
        const table = $('#restaurantPayoutTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: function (data, callback, settings) {
                const searchValue = data.search.value.toLowerCase();
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                // Add vendor_id parameter if filtering by specific restaurant
                data.vendor_id = getId;

                $.ajax({
                    url: '{{ route("restaurantsPayouts.data") }}',
                    method: 'GET',
                    data: data,
                    success: function(response) {
                        let records = [];

                        // Update count
                        $('.total_count').text(response.recordsTotal);

                        // Build HTML for each record
                        response.data.forEach(function(childData) {
                            var html = buildHTML(childData);
                            records.push(html);
                        });

                        $('#data-table_processing').hide();

                        callback({
                            draw: data.draw,
                            recordsTotal: response.recordsTotal,
                            recordsFiltered: response.recordsFiltered,
                            data: records
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                        $('.total_count').text(0);
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
            order: [[3, 'desc']],
            columnDefs: [
                {
                    targets:  3,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                { orderable: false, targets: 0 },

            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
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
                                exportData(dt, 'excel',fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'pdf',fieldConfig);
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'csv',fieldConfig);
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
        function debounce(func, wait) {
            let timeout;
            const context = this;
            return function (...args) {
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
    function buildHTML(val) {
        var html = [];
        var id = val.id;
        html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
        'for="is_open_' + id + '" ></label></td>');

        var price_val = '';
        var price = val.amount;
        if (intRegex.test(price) || floatRegex.test(price)) {
            price = parseFloat(price).toFixed(2);
        } else {
            price = 0;
        }
        if (currencyAtRight) {
            price_val = parseFloat(price).toFixed(decimal_degits) + "" + currentCurrency;
        } else {
            price_val = currentCurrency + "" + parseFloat(price).toFixed(decimal_degits);
        }

        <?php if ($id == '') { ?>
            var route = '{{route("restaurants.view",":id")}}';
            route = route.replace(':id', val.vendorID);
            html.push('<a href="' + route + '" class="redirecttopage" >' + val.restaurantName + '</a>');
        <?php } ?>

        html.push('<span class="text-red">(' + price_val + ')</span>');
        html.push('<span class="dt-time">' + val.formattedDate + '</span>');

        if (val.note != undefined && val.note != '') {
           html.push(val.note);
        } else {
            html.push('');
        }
        if (val.adminNote != undefined && val.adminNote != '') {
            html.push(val.adminNote);
        } else {
            html.push('');
        }
        return html;
    }
    $("#is_active").click(function () {
        $("#restaurantPayoutTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function () {
        if ($('#restaurantPayoutTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                var deleteIds = [];
                $('#restaurantPayoutTable .is_open:checked').each(function () {
                    deleteIds.push($(this).attr('dataId'));
                });

                // Delete via AJAX
                $.ajax({
                    url: '{{ route("restaurantsPayouts.data") }}',
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: deleteIds
                    },
                    success: function(response) {
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting payouts:', error);
                        alert('Error deleting payouts');
                        jQuery("#data-table_processing").hide();
                    }
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
</script>
@endsection
