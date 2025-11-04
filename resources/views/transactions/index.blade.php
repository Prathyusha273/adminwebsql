@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.wallet_transaction_plural')}} <span class="userTitle"></span></h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.wallet_transaction_table')}}</li>
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
                        <span class="icon mr-3"><img src="{{ asset('images/wallet.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.wallet_transaction_plural')}}</h3>
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
                <div class="menu-tab d-none storeMenuTab">
                    <ul>
                        <li>
                            <a href="{{route('restaurants.view',$id)}}" id="basic">{{trans('lang.tab_basic')}}</a>
                        </li>
                        <li>
                            <a href="{{route('restaurants.foods',$id)}}" id="tab_foods">{{trans('lang.tab_foods')}}</a>
                        </li>
                        <li>
                            <a href="{{route('restaurants.orders',$id)}}"
                                id="tab_orders">{{trans('lang.tab_orders')}}</a>
                        </li>
                        <li>
                            <a href="{{route('restaurants.coupons',$id)}}"
                                id="tab_promos">{{trans('lang.tab_promos')}}</a>
                        <li>
                            <a href="{{route('restaurants.payout',$id)}}"
                                id="tab_payouts">{{trans('lang.tab_payouts')}}</a>
                        </li>
                        <li>
                            <a href="{{route('payoutRequests.restaurants.view',$id)}}" id="payout_request">{{trans('lang.tab_payout_request')}}</a>
                        </li>
                        <li>
                            <a href="{{route('restaurants.booktable',$id)}}"
                                id="dine_in_future">{{trans('lang.dine_in_future')}}</a>
                        </li>
                        <li class="active">
                            <a href="{{route('users.walletstransaction',$id)}}"
                                class="active">{{trans('lang.wallet_transaction')}}</a>
                        </li>
                        <li id="subscription_plan">
                            <a href="{{route('vendor.subscriptionPlanHistory',$id)}}" class="active">{{trans("lang.subscription_history")}}</a>
                        </li>
                    </ul>
                </div>
                <div class="menu-tab d-none driverMenuTab">
                    <ul>
                        <li>
                            <a href="{{route('drivers.view',$id)}}">{{trans('lang.tab_basic')}}</a>
                        </li>
                        <li>
                            <a href="{{route('orders')}}?driverId={{$id}}">{{trans('lang.tab_orders')}}</a>
                        </li>
                        <li>
                            <a href="{{route('driver.payout',$id)}}">{{trans('lang.tab_payouts')}}</a>
                        </li>
                        <li>
                            <a
                                href="{{route('payoutRequests.drivers.view',$id)}}">{{trans('lang.tab_payout_request')}}</a>
                        </li>
                        <li class="active">
                            <a href="{{route('users.walletstransaction',$id)}}">{{trans('lang.wallet_transaction')}}</a>
                        </li>
                    </ul>
                </div>
                <div class="menu-tab d-none userMenuTab">
                    <ul>
                        <li>
                            <a href="{{route('users.view',$id)}}">{{trans('lang.tab_basic')}}</a>
                        </li>
                        <li>
                            <a href="{{route('orders','userId='.$id)}}">{{trans('lang.tab_orders')}}</a>
                        </li>
                        <li class="active">
                            <a href="{{route('users.walletstransaction',$id)}}">{{trans('lang.wallet_transaction')}}</a>
                        </li>
                    </ul>
                </div>
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.wallet_transaction_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.wallet_transactions_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <!-- <a class="btn-primary btn rounded-full" href="{!! route('users.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.user_create')}}</a> -->
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="walletTransactionTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>
                                        <?php if ($id == '') { ?>
                                            <th>{{ trans('lang.users')}}</th>
                                            <th>{{ trans('lang.role')}}</th>
                                        <?php } ?>
                                        <th>{{trans('lang.amount')}}</th>
                                        <th>{{trans('lang.date')}}</th>
                                        <th>{{trans('lang.wallet_transaction_note')}}</th>
                                        <th>{{trans('lang.payment_method')}}</th>
                                        <th>{{trans('lang.payment_status')}}</th>
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
    var user_id = "<?php echo $id; ?>";
    var search = jQuery("#search").val();
    var append_list = '';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;

    $(document.body).on('keyup', '#search', function () {
        search = jQuery(this).val();
    });

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

    $(document).ready(function () {
        if ('{{$id}}') {
            // Fetch user details from SQL
            $.ajax({
                url: '{{ route("walletstransaction.user", ":id") }}'.replace(':id', '{{$id}}'),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var userDetail = response.data;

                        if (userDetail.role == "vendor") {
                            $(".storeMenuTab").removeClass("d-none");
                            // Fetch vendor details
                            $.ajax({
                                url: '/vendors/by-author/' + userDetail.id,
                                method: 'GET',
                                success: function(vendorResp) {
                                    if (vendorResp.success) {
                                        var vendorDetail = vendorResp.data;
                                        document.getElementById('basic').href = `{{ route('restaurants.view', ':id') }}`.replace(':id', vendorDetail.id);
                                        document.getElementById('tab_foods').href = `{{ route('restaurants.foods', ':id') }}`.replace(':id', vendorDetail.id);
                                        document.getElementById('tab_orders').href = `{{ route('restaurants.orders', ':id') }}`.replace(':id', vendorDetail.id);
                                        document.getElementById('tab_promos').href = `{{ route('restaurants.coupons', ':id') }}`.replace(':id', vendorDetail.id);
                                        document.getElementById('tab_payouts').href = `{{ route('restaurants.payout', ':id') }}`.replace(':id', vendorDetail.id);
                                        document.getElementById('dine_in_future').href = `{{ route('restaurants.booktable', ':id') }}`.replace(':id', vendorDetail.id);
                                        document.getElementById('payout_request').href = `{{ route('payoutRequests.restaurants.view', ':id') }}`.replace(':id', vendorDetail.id);
                                    }
                                }
                            });
                        }
                        if (userDetail.role == "driver") {
                            $(".driverMenuTab").removeClass("d-none");
                        }
                        if (userDetail.role == "customer") {
                            $(".userMenuTab").removeClass("d-none");
                        }
                        $(".userTitle").text(' - ' + userDetail.fullName);
                    }
                }
            });
        }
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
                { key: 'user', header: 'User Name' },
                { key: 'role', header: 'Role' },
                { key: 'transactionamount', header: 'Amount' },
                { key: 'payment_method', header: 'Payment Method' },
                { key: 'payment_status', header: 'Payment Status' },
                { key: 'date', header: 'Date' },
            ],
            fileName: 'Wallet Transaction List',
        };
        const table = $('#walletTransactionTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function (data, callback, settings) {
                const searchValue = data.search.value.toLowerCase();
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                // Add user_id parameter if filtering by specific user
                data.user_id = user_id;

                $.ajax({
                    url: '{{ route("walletstransaction.data") }}',
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
            order: ('{{$id}}' != '') ? [[2, 'desc']] : [[4, 'desc']],
            columnDefs: [
                {
                    targets: ('{{$id}}' != '') ? [[1, 'desc']] : [[3, 'desc']],
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                {
                    targets: ('{{$id}}' != '') ? 0 : 2,
                    type: 'num-fmt',
                    render: function (data, type, row, meta) {
                        if (type === 'display') {
                            return data;
                        }
                        return parseFloat(data.replace(/[^0-9.-]+/g, ""));
                    }
                },
                { orderable: false, targets: ('{{$id}}' != '') ? [0,3, 4]  : [0,5, 6] },
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
        <?php if ($id == '') { ?>
            if (val.user_id) {
                var user_role = val.userType;
                var user_name = val.userName;
                var routeuser = "Javascript:void(0)";
                if (user_role == "customer") {
                    routeuser = '{{route("users.view",":id")}}';
                    routeuser = routeuser.replace(':id', val.user_id);
                } else if (user_role == "driver") {
                    routeuser = '{{route("drivers.view",":id")}}';
                    routeuser = routeuser.replace(':id', val.user_id);
                } else if (user_role == "vendor") {
                    // For vendors, link directly to user_id for now
                    routeuser = '{{route("users.view",":id")}}';
                    routeuser = routeuser.replace(':id', val.user_id);
                }

                html.push('<a href="' + routeuser + '">' + user_name + '</a>');
                html.push(user_role);
            } else {
                html.push('');
                html.push('');
            }
        <?php } ?>
        amount = val.amount;
        if (!isNaN(amount)) {
            amount = parseFloat(amount).toFixed(decimal_degits);
        }
        if ((val.hasOwnProperty('isTopUp') && val.isTopUp) || (val.payment_method == "Cancelled restaurantorders Payment")) {
            if (currencyAtRight) {
                html.push('<span class="text-green">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</span>');
            } else {
                html.push('<span class="text-green">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</span>');
            }
        } else if (val.hasOwnProperty('isTopUp') && !val.isTopUp) {
            if (currencyAtRight) {
                html.push('<span class="text-red">(-' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + ')</span>');
            } else {
                html.push('<span class="text-red">(-' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + ')</span>');
            }
        } else {
            if (currencyAtRight) {
                html.push('<span class="">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</span>');
            } else {
                html.push('<span class="">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</span>');
            }
        }
        // Use formatted date from backend
        html.push(val.formattedDate || '');
        if (val.note != undefined && val.note != '' && val.note != null) {
            html.push(val.note);
        } else {
            html.push('');
        }
        var payment_method = '';
        if (val.payment_method) {
            if (val.payment_method == "Stripe" || val.payment_method == "stripe") {
                image = '{{asset("images/stripe.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "RazorPay" || val.payment_method == "razorPay") {
                image = '{{asset("images/razorepay.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Paypal" || val.payment_method == "paypal") {
                image = '{{asset("images/paypal.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "PayFast" || val.payment_method == "payFast") {
                image = '{{asset("images/payfast.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "PayStack" || val.payment_method == "payStack") {
                image = '{{asset("images/paystack.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "FlutterWave" || val.payment_method == "flutterWave") {
                image = '{{asset("images/flutter_wave.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Mercado Pago" || val.payment_method == "mercado Pago") {
                image = '{{asset("images/marcado_pago.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Wallet" || val.payment_method == "wallet") {
                image = '{{asset("images/foodie_wallet.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Paytm" || val.payment_method == "paytm") {
                image = '{{asset("images/paytm.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Xendit" || val.payment_method == "xendit") {
                image = '{{asset("images/xendit.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "OrangePay" || val.payment_method == "orangepay") {
                image = '{{asset("images/orangeMoney.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "MidTrans" || val.payment_method == "midtrans") {
                image = '{{asset("images/midtrans.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Cancelled restaurantorders Payment") {
                image = '{{asset("images/cancel_order.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Refund Amount") {
                image = '{{asset("images/refund_amount.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Referral Amount") {
                image = '{{asset("images/reffral_amount.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else {
                payment_method = val.payment_method;
            }
        }
        html.push('<span class="payment_images">' + payment_method + '</span>');
        if (val.payment_status == 'success') {
            html.push('<span class="success"><span>' + val.payment_status + '</span></span>');
        } else if (val.payment_status == 'undefined') {
            html.push('<span class="undefined"><span>' + val.payment_status + '</span></span>');
        } else if (val.payment_status == 'Refund success') {
            html.push('<span class="refund_success"><span>' + val.payment_status + '</span></span>');
        } else {
            html.push('<span class="refund_success"><span>' + val.payment_status + '</span></span>');
        }
        return html;
    }
    $("#is_active").click(function () {
        $("#walletTransactionTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function () {
        if ($('#walletTransactionTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                $('#walletTransactionTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    deleteDocumentWithImage('wallet', dataId)
                    .then(() => {
                        window.location.reload();
                    })
                    .catch((error) => {
                        console.error('Error deleting document or store data:', error);
                    });
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
</script>
@endsection
