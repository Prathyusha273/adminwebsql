@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.drivers_payout_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.drivers_payout_plural')}}</li>
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
                        <h3 class="mb-0">{{trans('lang.drivers_payout_plural')}}</h3>
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
                                <a href="{{route('drivers.view',$id)}}">{{trans('lang.tab_basic')}}</a>
                            </li>
                            <li>
                                <a href="{{route('orders')}}?driverId={{$id}}">{{trans('lang.tab_orders')}}</a>
                            </li>
                            <li class="active">
                                <a href="{{route('driver.payout',$id)}}">{{trans('lang.tab_payouts')}}</a>
                            </li>
                            <li>
                                <a href="{{route('users.walletstransaction',$id)}}">{{trans('lang.wallet_transaction')}}</a>
                            </li>
                        </ul>
                    </div>
                <?php } ?>
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.drivers_payout_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.driver_payouts_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">
                        <?php if ($id != '') { ?>
                            <a class="btn-primary btn rounded-full" href="{!! route('driver.payout.create',$id) !!}/"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.drivers_payout_create')}}</a>
                        <?php } else { ?>
                            <a class="btn-primary btn rounded-full" href="{!! route('driversPayouts.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.drivers_payout_create')}}</a>
                        <?php } ?>
                     </div>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                         <table id="driverPayoutTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>
                                        <th>{{ trans('lang.driver')}}</th>
                                        <th>{{trans('lang.paid_amount')}}</th>
                                        <th>{{trans('lang.drivers_payout_paid_date')}}</th>
                                        <th>{{trans('lang.drivers_payout_note')}}</th>
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
    var driver_id = "<?php echo $id; ?>";
    var append_list = '';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_digits = 0;

    // Fetch currency settings from Laravel backend
    $.ajax({
        url: '{{ route("payments.currency") }}',
        method: 'GET',
        async: false,
        success: function(response) {
            if (response.success) {
                currentCurrency = response.data.symbol;
                currencyAtRight = response.data.symbolAtRight;
                decimal_digits = response.data.decimal_degits || 0;
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching currency:', error);
        }
    });

    // Fetch driver details if driver_id is provided
    if (driver_id) {
        $('.menu-tab').show();
        $.ajax({
            url: '{{ route("driversPayouts.driver", ":id") }}'.replace(':id', driver_id),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('.driverTitle').html('{{trans("lang.drivers_payout_plural")}} - ' + response.data.fullName);
                }
            }
        });
    }
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
                { key: 'driverName', header: "{{ trans('lang.driver')}}" },
                { key: 'paidamount', header: "{{trans('lang.paid_amount')}}" },
                { key: 'paidDate', header: "{{trans('lang.drivers_payout_paid_date')}}" },
                { key: 'adminNote', header: "{{trans('lang.admin_note')}}" },
                { key: 'note', header: "{{trans('lang.drivers_payout_note')}}" },
            ],
            fileName: "{{trans('lang.drivers_payout_table')}}",
        };
        const table = $('#driverPayoutTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function (data, callback, settings) {
                const searchValue = data.search.value.toLowerCase();
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#data-table_processing').show();
                }

                // Add driver_id parameter if filtering by specific driver
                data.driver_id = driver_id;

                $.ajax({
                    url: '{{ route("driversPayouts.data") }}',
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
                    targets: 3,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                {
                    orderable: false,
                    targets: 0,
                },
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
        html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label" for="is_open_' + id + '" ></label></td>');

        var route1 = '{{route("drivers.view", ":id")}}';
        route1 = route1.replace(':id', val.driverID);
        html.push('<a href="' + route1 + '">' + val.driverName + '</a>');

        if (currencyAtRight) {
            html.push('<span class="text-red">' + parseFloat(val.amount).toFixed(decimal_digits) + ' ' + currentCurrency + '</span>');
        } else {
            html.push('<span class="text-red">' + currentCurrency + ' ' + parseFloat(val.amount).toFixed(decimal_digits) + '</span>');
        }

        html.push(val.formattedDate);

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
        $("#driverPayoutTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function () {
        if ($('#driverPayoutTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                var deleteIds = [];
                $('#driverPayoutTable .is_open:checked').each(function () {
                    deleteIds.push($(this).attr('dataId'));
                });

                // Note: Delete functionality needs backend route implementation
                alert('Delete functionality requires backend implementation');
                jQuery("#data-table_processing").hide();
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
</script>
@endsection
