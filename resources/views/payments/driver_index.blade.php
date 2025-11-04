@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.driver')}} {{trans('lang.payment_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.driver')}} {{trans('lang.payment_plural')}}</li>
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
                        <h3 class="mb-0">{{ trans('lang.driver')}} {{trans('lang.payment_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
                    </div>
                    <div class="d-flex top-title-right align-self-center">
                        <div class="select-box pl-3">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-12">
                <div class="card border">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--1">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                       <div class="card-box-with-content">
                                        <h4 class="text-dark-2 mb-1 h4 rest_count">00</h4>
                                        <p class="mb-0 small text-dark-2">{{ trans('lang.dashboard_total_drivers')}}</p>
                                       </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/restaurant_icon.png') }}"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--2">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                       <div class="card-box-with-content">
                                        <h4 class="text-dark-2 mb-1 h4 total_payments">00</h4>
                                        <p class="mb-0 small text-dark-2">{{ trans('lang.total_payments')}}</p>
                                       </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/total_earning.png') }}"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--3">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                       <div class="card-box-with-content">
                                        <h4 class="text-dark-2 mb-1 h4 total_paid_amounts">00</h4>
                                        <p class="mb-0 small text-dark-2">{{ trans('lang.total_paid_amount')}}</p>
                                       </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/total_payment.png') }}"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-box-with-icon bg--5">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                       <div class="card-box-with-content">
                                        <h4 class="text-dark-2 mb-1 h4 total_remaining_amounts">00</h4>
                                        <p class="mb-0 small text-dark-2">{{ trans('lang.remaining_amounts')}}</p>
                                       </div>
                                        <span class="box-icon ab"><img src="{{ asset('images/remaining_payment.png') }}"></span>
                                    </div>
                                </div>
                            </div>
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
                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.driver')}} {{trans('lang.payment_plural')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.driver_payments_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <!-- <a class="btn-primary btn rounded-full" href="{!! route('users.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.user_create')}}</a> -->
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                        <div class="table-responsive m-t-10">
                            <table id="driverPaymentTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ trans('lang.driver')}}</th>
                                        <th>{{ trans('lang.total_amount')}}</th>
                                        <th>{{trans('lang.paid_amount')}}</th>
                                        <th>{{trans('lang.remaining_amount')}}</th>
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
    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
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
                { key: 'fullName', header: "{{ trans('lang.driver')}}" },
                { key: 'total', header: "{{ trans('lang.total_amount')}}" },
                { key: 'paid', header: "{{trans('lang.paid_amount')}}" },
                { key: 'remaining', header: "{{trans('lang.remaining_amount')}}" },
            ],
            fileName: "{{ trans('lang.driver')}} {{trans('lang.payment_plural')}}",
        };
        const table = $('#driverPaymentTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: function (data, callback, settings) {
                const searchValue = data.search.value.toLowerCase();
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                $.ajax({
                    url: '{{ route("driver.driverpayments.data") }}',
                    method: 'GET',
                    data: data,
                    success: function(response) {
                        let records = [];

                        // Update summary statistics
                        const summary = response.summary;
                        $('.total_count').text(summary.rest_count);
                        $('.rest_count').text(summary.rest_count);

                        let total_payments = summary.total_payments;
                        let total_paid_amounts = summary.total_paid_amounts;
                        let total_remaining_amounts = summary.total_remaining_amounts;

                        if (currencyAtRight) {
                            total_payments = parseFloat(total_payments).toFixed(decimal_degits) + "" + currentCurrency;
                            total_paid_amounts = parseFloat(total_paid_amounts).toFixed(decimal_degits) + "" + currentCurrency;
                            total_remaining_amounts = parseFloat(total_remaining_amounts).toFixed(decimal_degits) + "" + currentCurrency;
                        } else {
                            total_payments = currentCurrency + "" + parseFloat(total_payments).toFixed(decimal_degits);
                            total_paid_amounts = currentCurrency + "" + parseFloat(total_paid_amounts).toFixed(decimal_degits);
                            total_remaining_amounts = currentCurrency + "" + parseFloat(total_remaining_amounts).toFixed(decimal_degits);
                        }

                        $('.total_payments').text(total_payments);
                        $('.total_paid_amounts').text(total_paid_amounts);
                        $('.total_remaining_amounts').text(total_remaining_amounts);

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
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: [1, 2, 3],
                    type: 'num-fmt',
                    render: function (data, type, row, meta) {
                        if (type === 'display') {
                            return data;
                        }
                        return parseFloat(data.replace(/[^0-9.-]+/g, ""));
                    }
                },
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
        var route1 = '{{route("drivers.view", ":id")}}';
        route1 = route1.replace(':id', id);
        var data = {};
        data.total = val.totalAmount;
        data.paid_price_val = val.paidAmount;
        data.remaining_val = val.remainingAmount;
        var total = Math.abs(val.totalAmount);
        var remaining_val = Math.abs(val.remainingAmount);
        var paid_price_val = Math.abs(val.paidAmount);

        if (Math.abs(data.total) != 0) {
            var firstName = val.firstName || '';
            var lastName = val.lastName || '';
            html.push('<a href="' + route1 + '" class="redirecttopage">' + firstName + ' ' + lastName + '</a>');
            var total_class = 'text-green';
            var paid_price_val_class = 'text-green';
            var remaining_val_class = 'text-green';

            if (currencyAtRight) {
                if (data.total < 0) {
                    total_class = 'text-red';
                    total = Math.abs(data.total);
                    data.total = '(-' + parseFloat(total).toFixed(decimal_degits) + "" + currentCurrency + ')';
                } else {
                    data.total = parseFloat(data.total).toFixed(decimal_degits) + "" + currentCurrency;
                }
                if (data.paid_price_val < 0) {
                    paid_price_val_class = 'text-red';
                    paid_price_val = Math.abs(data.paid_price_val);
                    data.paid_price_val = '(-' + parseFloat(paid_price_val).toFixed(decimal_degits) + "" + currentCurrency + ')';
                } else {
                    data.paid_price_val = parseFloat(data.paid_price_val).toFixed(decimal_degits) + "" + currentCurrency;
                }
                if (data.remaining_val < 0) {
                    remaining_val_class = 'text-red';
                    remaining_val = Math.abs(data.remaining_val);
                    data.remaining_val = '(-' + parseFloat(remaining_val).toFixed(decimal_degits) + "" + currentCurrency + ')';
                } else {
                    data.remaining_val = parseFloat(data.remaining_val).toFixed(decimal_degits) + "" + currentCurrency;
                }
            } else {
                if (data.total < 0) {
                    total_class = 'text-red';
                    total = Math.abs(data.total);
                    data.total = '(-' + currentCurrency + "" + parseFloat(total).toFixed(decimal_degits) + ')';
                } else {
                    data.total = currentCurrency + "" + parseFloat(data.total).toFixed(decimal_degits);
                }
                if (data.paid_price_val < 0) {
                    paid_price_val_class = 'text-red';
                    paid_price_val = Math.abs(data.paid_price_val);
                    data.paid_price_val = '(-' + currentCurrency + "" + parseFloat(paid_price_val).toFixed(decimal_degits) + ')';
                } else {
                    data.paid_price_val = currentCurrency + "" + parseFloat(data.paid_price_val).toFixed(decimal_degits);
                }
                if (data.remaining_val < 0) {
                    remaining_val_class = 'text-red';
                    remaining_val = Math.abs(data.remaining_val);
                    data.remaining_val = '(-' + currentCurrency + "" + parseFloat(remaining_val).toFixed(decimal_degits) + ')';
                } else {
                    data.remaining_val = currentCurrency + "" + parseFloat(data.remaining_val).toFixed(decimal_degits);
                }
            }
            html.push('<span class="' + total_class + '">' + data.total + '</span>');
            html.push('<span class="' + paid_price_val_class + '">' + data.paid_price_val + '</span>');
            html.push('<span class="' + remaining_val_class + '">' + data.remaining_val + '</span>');
        }
        return html;
    }
</script>
@endsection
