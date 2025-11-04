@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.driver_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('drivers') !!}">{{trans('lang.driver_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.restaurant_details')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="resttab-sec">
                    <div class="menu-tab">
                        <ul>
                            <li class="active">
                                <a href="{{route('drivers.view',$id)}}">{{trans('lang.tab_basic')}}</a>
                            </li>
                            <li>
                                <a href="{{route('orders')}}?driverId={{$id}}">{{trans('lang.tab_orders')}}</a>
                            </li>
                            <li>
                                <a href="{{route('driver.payout',$id)}}">{{trans('lang.tab_payouts')}}</a>
                            </li>
                            <li>
                                <a href="{{route('payoutRequests.drivers.view',$id)}}">{{trans('lang.tab_payout_request')}}</a>
                            </li>
                            <li>
                                <a href="{{route('users.walletstransaction',$id)}}">{{trans('lang.wallet_transaction')}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="row restaurant_payout_create driver_details">
                        <div class="restaurant_payout_create-inner">
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#addWalletModal"
                               class="add-wallate btn btn-success"><i class="fa fa-plus"></i> Add Wallet Amount</a>
                            <fieldset>
                                <legend>{{trans('lang.driver_details')}}</legend>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
                                    <div class="col-7" class="driver_name">
                                        <span class="driver_name" id="driver_name"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.email')}}</label>
                                    <div class="col-7">
                                        <span class="email"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                    <div class="col-7">
                                        <span class="phone"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.wallet_Balance')}}</label>
                                    <div class="col-7">
                                        <span class="wallet"></span>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.profile_image')}}</label>
                                    <div class="col-7 profile_image">
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.zone')}}</label>
                                    <div class="col-7">
                                        <span id="zone_name"></span>
                                    </div>
                                </div>
                        </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row restaurant_payout_create restaurant_details">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.bankdetails')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-4 control-label">{{
                                    trans('lang.bank_name')}}</label>
                                <div class="col-7">
                                    <span class="bank_name"></span>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-4 control-label">{{
                                    trans('lang.branch_name')}}</label>
                                <div class="col-7">
                                    <span class="branch_name"></span>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-4 control-label">{{
                                    trans('lang.holer_name')}}</label>
                                <div class="col-7">
                                    <span class="holer_name"></span>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-4 control-label">{{
                                    trans('lang.account_number')}}</label>
                                <div class="col-7">
                                    <span class="account_number"></span>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-4 control-label">{{
                                    trans('lang.other_information')}}</label>
                                <div class="col-7">
                                    <span class="other_information"></span>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-12 text-center btm-btn">
            <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title locationModalTitle">{{trans('lang.add_wallet_amount')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="">
                    <div class="form-row">
                        <div class="form-group row">
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.amount')}}</label>
                                <div class="col-12">
                                    <input type="number" name="amount" class="form-control" id="amount">
                                    <div id="wallet_error" style="color:red"></div>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.note')}}</label>
                                <div class="col-12">
                                    <input type="text" name="note" class="form-control" id="note">
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <div id="user_account_not_found_error" class="align-items-center"  style="color:red"></div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary save-form-btn">{{trans('submit')}}</a>
                    </button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        {{trans('close')}}</a>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    var id = "<?php echo $id; ?>";
    var photo = "";
    var placeholderImage = '{{ asset('images/placeholder.png') }}';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var emailTemplatesData = null;

    // Load currency from SQL
    $.ajax({
        url: '{{url("/payments/currency")}}',
        type: 'GET',
        success: function(response) {
            if(response.success && response.data) {
                currentCurrency = response.data.symbol;
                currencyAtRight = response.data.symbolAtRight;
                decimal_degits = response.data.decimal_degits || 0;
            }
        }
    });

    // Load email template from SQL
    $.ajax({
        url: '{{url("/api/email-templates/wallet_topup")}}',
        type: 'GET',
        success: function(response) {
            if(response.success && response.template) {
                emailTemplatesData = response.template;
            }
        }
    });

    $(document).ready(async function () {
        jQuery("#data-table_processing").show();

        // Load driver data from SQL
        console.log('Loading driver with ID:', id);
        $.ajax({
            url: '/drivers/' + id + '/data',
            type: 'GET',
            success: function(response) {
                console.log('Driver data response:', response);
                if(response.success && response.data) {
                    var driver = response.data;
                    console.log('Driver loaded successfully:', driver.firstName, driver.lastName);
            $(".driver_name").text(driver.firstName);
            if(driver.hasOwnProperty('email') && driver.email){
                $(".email").text(shortEmail(driver.email));
            }
            else
            {
                $('.email').html("{{trans('lang.not_mentioned')}}");
            }
            if (driver.hasOwnProperty('phoneNumber') && driver.phoneNumber) {
                $(".phone").text(shortEditNumber(driver.phoneNumber));
            }
            else{
                $('.phone').html("{{trans('lang.not_mentioned')}}");
            }
            var wallet_balance = 0;
            if (driver.hasOwnProperty('wallet_amount') && driver.wallet_amount != null && !isNaN(driver.wallet_amount)) {
                wallet_balance = driver.wallet_amount;
            }
            if (currencyAtRight) {
                wallet_balance = parseFloat(wallet_balance).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                wallet_balance = currentCurrency + "" + parseFloat(wallet_balance).toFixed(decimal_degits);
            }
            $(".wallet").text(wallet_balance);
            var image = "";
            if (driver.profilePictureURL!="" && driver.profilePictureURL!= null) {
                image = '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" width="200px" id="" height="auto" src="' + driver.profilePictureURL + '">';
            } else {
                image = '<img width="200px" id="" height="auto" src="' + placeholderImage + '">';
            }
            $(".profile_image").html(image);
            if (driver.hasOwnProperty('zoneId') && driver.zoneId != '') {
                $.ajax({
                    url: '/api/zone/' + driver.zoneId,
                    type: 'GET',
                    success: function(zoneResponse) {
                        if(zoneResponse.success && zoneResponse.zone) {
                            $("#zone_name").text(zoneResponse.zone.name);
                        }
                    }
                });
            }
            if (driver.userBankDetails) {
                if (driver.userBankDetails.bankName != undefined) {
                    $(".bank_name").text(driver.userBankDetails.bankName);
                }
                if (driver.userBankDetails.branchName != undefined) {
                    $(".branch_name").text(driver.userBankDetails.branchName);
                }
                if (driver.userBankDetails.holderName != undefined) {
                    $(".holer_name").text(driver.userBankDetails.holderName);
                }
                if (driver.userBankDetails.accountNumber != undefined) {
                    $(".account_number").text(driver.userBankDetails.accountNumber);
                }
                if (driver.userBankDetails.otherDetails != undefined) {
                    $(".other_information").text(driver.userBankDetails.otherDetails);
                }
            }
            jQuery("#data-table_processing").hide();
            } else {
                console.error('Failed to load driver data:', response);
                jQuery("#data-table_processing").hide();
                alert('Error loading driver data: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading driver data:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status
            });
            jQuery("#data-table_processing").hide();
            alert('Error loading driver data (HTTP ' + xhr.status + '). Check console for details.');
        }
    });
    });

    $(".save-form-btn").click(function () {
        var amount = $('#amount').val();
        if(amount==''){
            $('#wallet_error').text('{{trans("lang.add_wallet_amount_error")}}');
            return false;
        }
        var note = $('#note').val();

        // Add wallet amount via AJAX
        $.ajax({
            url: '{{url("/api/users/wallet/add")}}',
            type: 'POST',
            data: {
                user_id: id,
                amount: amount,
                note: note,
                _token: '{{csrf_token()}}'
            },
            success: function(response) {
                if(response.success) {
                    var amountFormatted, newWalletFormatted;
                    if(currencyAtRight) {
                        amountFormatted = parseInt(amount).toFixed(decimal_degits) + currentCurrency;
                        newWalletFormatted = parseFloat(response.newWalletAmount).toFixed(decimal_degits) + currentCurrency;
                    } else {
                        amountFormatted = currentCurrency + parseInt(amount).toFixed(decimal_degits);
                        newWalletFormatted = currentCurrency + parseFloat(response.newWalletAmount).toFixed(decimal_degits);
                    }

                    var formattedDate = new Date();
                    var month = formattedDate.getMonth() + 1;
                    var day = formattedDate.getDate();
                    var year = formattedDate.getFullYear();
                    month = month < 10 ? '0' + month : month;
                    day = day < 10 ? '0' + day : day;
                    formattedDate = day + '-' + month + '-' + year;

                    if(emailTemplatesData) {
                        var message = emailTemplatesData.message;
                        message = message.replace(/{username}/g, response.user.firstName + ' ' + response.user.lastName);
                        message = message.replace(/{date}/g, formattedDate);
                        message = message.replace(/{amount}/g, amountFormatted);
                        message = message.replace(/{paymentmethod}/g, 'Wallet');
                        message = message.replace(/{transactionid}/g, response.transaction_id);
                        message = message.replace(/{newwalletbalance}/g, newWalletFormatted);

                        var url = "{{url('send-email')}}";
                        sendEmail(url, emailTemplatesData.subject, message, [response.user.email]).then(function(sendEmailStatus) {
                            if(sendEmailStatus) {
                                window.location.reload();
                            }
                        });
                    } else {
                        window.location.reload();
                    }
                } else {
                    $('#user_account_not_found_error').text(response.message || '{{trans("lang.user_detail_not_found")}}');
                }
            },
            error: function() {
                $('#user_account_not_found_error').text('{{trans("lang.error_adding_wallet_amount")}}');
            }
        });
    });
</script>
@endsection
