@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
      <div class="admin-top-section pt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/building-four.png') }}"></span>
                       <div class="top-title-breadcrumb">
                        <h3 class="mb-0 restaurantTitle">{{trans('lang.user_plural')}}</h3>
                          <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                            <li class="breadcrumb-item"><a href="{!! route('users') !!}">{{trans('lang.user_plural')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('lang.user_details')}}</li>
                        </ol>
                       </div>
                    </div>
                    <div class="d-flex top-title-right align-self-center">
                       <div class="card-header-right">
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#addWalletModal"class="btn-primary btn rounded-full add-wallate"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.add_wallet_amount')}}</a>
                       </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="resttab-sec mb-4">
        <div class="menu-tab">
            <ul>
                <li class="active">
                    <a href="{{route('users.view',$id)}}">{{trans('lang.tab_basic')}}</a>
                </li>
                <li>
                    <a href="{{route('orders')}}?userId={{$id}}">{{trans('lang.tab_orders')}}</a>
                </li>
                <li>
                    <a href="{{route('users.walletstransaction',$id)}}">{{trans('lang.wallet_transaction')}}</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card card-box-with-icon bg--1">
                    <div class="card-body d-flex justify-content-between align-items-center">
                       <div class="card-box-with-content">
                        <h4 class="text-dark-2 mb-1 h4 total_orders" id="total_orders">00</h4>
                        <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_orders')}}</p>
                       </div>
                        <span class="box-icon ab"><img src="{{ asset('images/active_restaurant.png') }}"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-box-with-icon bg--3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                       <div class="card-box-with-content">
                        <h4 class="text-dark-2 mb-1 h4 wallet_balance" id="wallet_balance">$0.00</h4>
                        <p class="mb-0 small text-dark-2">{{trans('lang.wallet_Balance')}}</p>
                       </div>
                        <span class="box-icon ab"><img src="{{ asset('images/total_payment.png') }}"></span>
                    </div>
                </div>
            </div>
        </div>
       </div>
       <div class="restaurant_info-section">
         <div class="card border">
           <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
            <div class="card-header-title">
                <h3 class="text-dark-2 mb-0 h4">{{trans('lang.user_details')}}</h3>
            </div>
           </div>
           <div class="card-body">
              <!-- Loading indicator -->
              <div id="user-data-loading" style="text-align: center; padding: 40px;">
                  <i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #007cff;"></i>
                  <p style="margin-top: 15px; color: #666;">Loading user details...</p>
              </div>

              <!-- User data content (hidden until loaded) -->
              <div class="row" id="user-data-content" style="display: none;">
                 <div class="col-md-6">
                    <div class="restaurant_info_left">
                        <div class="d-flex mb-1">
                            <div class="sis-img profile_image" id="profile_image">
                            </div>
                            <div class="sis-content pl-4">
                                <ul class="p-0 info-list mb-0">
                                    <li class="d-flex align-items-center mb-2">
                                        <label class="mb-0 font-wi font-semibold text-dark-2">{{trans('lang.first_name')}}</label>
                                        <span class="user_name" id="user_name"></span>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <label class="mb-0 font-wi font-semibold text-dark-2">{{trans('lang.email')}}</label>
                                        <span class="email"></span>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <label class="mb-0 font-wi font-semibold text-dark-2">{{trans('lang.user_phone')}}</label>
                                        <span class="phone"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="mb-0 font-wi font-semibold text-dark-2">{{trans('lang.address')}}</label>
                                <p><span class="address"></span></p>
                            </div>
                        </div>
                    </div>
                 </div>
                 <div class="col-md-6">
                    <div class="map-box">
                        <div class="mapouter" style="display:none;"><div class="gmap_canvas"><iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=600&amp;height=225&amp;hl=en&amp;q=University of Oxford&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a href="https://sprunkiplay.com/">Sprunki Game</a></div><style>.mapouter{position:relative;text-align:right;width:100%;height:225px;}.gmap_canvas {overflow:hidden;background:none!important;width:100%;height:225px;}.gmap_iframe {height:225px!important;}</style></div>
                    </div>
                 </div>
              </div>

              <!-- Error message container -->
              <div id="user-data-error" style="display: none; text-align: center; padding: 40px;">
                  <i class="fa fa-exclamation-triangle" style="font-size: 48px; color: #f44336;"></i>
                  <p style="margin-top: 15px; color: #666;" class="error-message"></p>
                  <button onclick="location.reload()" class="btn btn-primary mt-3">
                      <i class="fa fa-refresh"></i> Reload Page
                  </button>
              </div>
           </div>
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
                                    <label class="col-12 control-label">{{trans('lang.amount')}}</label>
                                    <div class="col-12">
                                        <input type="number" name="amount" class="form-control" id="amount">
                                        <div id="wallet_error" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <label class="col-12 control-label">{{trans('lang.note')}}</label>
                                    <div class="col-12">
                                        <input type="text" name="note" class="form-control" id="note">
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <div id="user_account_not_found_error" class="align-items-center" style="color:red">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary save-form-btn">{{trans('submit')}}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"
                                aria-label="Close">{{trans('close')}}</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // ✅ SQL API VERSION - No Firebase!
        console.log('✅ User View using SQL API');

        var id = "{{$id}}";
        var photo = "";
        var placeholderImage = '{{ asset('images/placeholder.png') }}';
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var emailTemplatesData = null;

        // Get currency settings from window.settings (loaded by settings-loader.js)
        function initializeCurrencySettings() {
            if (window.settings && window.settings.currency) {
                currentCurrency = window.settings.currency.symbol || '₹';
                currencyAtRight = window.settings.currency.symbolAtRight || false;
                decimal_degits = window.settings.currency.decimal_degits || 2;
                $(".currentCurrency").text(currentCurrency);
            }
        }

        // Wait for settings to load
        async function waitForSettings() {
            let attempts = 0;
            while (!window.settings && attempts < 50) {
                await new Promise(resolve => setTimeout(resolve, 100));
                attempts++;
            }
            initializeCurrencySettings();
        }

        $(document).ready(async function () {
            console.log('✅ Loading user data from SQL API');
            await waitForSettings();
            jQuery("#data-table_processing").show();

            // Load user data from SQL API
            $.ajax({
                url: '{{ url("/users/data") }}/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (!response.success || !response.data) {
                        console.error('❌ Failed to load user data');
                        jQuery("#data-table_processing").hide();
                        $('#user-data-loading').hide();
                        $('#user-data-error').show();
                        $('#user-data-error .error-message').text('Failed to load user data');
                        return;
                    }

                    console.log('✅ User data loaded from SQL:', response);
                    var user = response.data;

                    // Hide loading, show content
                    $('#user-data-loading').hide();
                    $('#user-data-content').show();
                    // Display user name
                    $(".user_name").text(user.firstName + ' ' + user.lastName);

                    // Display email
                    if (user.email) {
                        $(".email").text(shortEmail(user.email));
                    } else {
                        $('.email').html("{{trans('lang.not_mentioned')}}");
                    }

                    // Display phone
                    if (user.phoneNumber) {
                        $(".phone").text(shortEditNumber(user.phoneNumber));
                    } else {
                        $('.phone').html("{{trans('lang.not_mentioned')}}");
                    }

                    // Display total orders
                    $("#total_orders").text(user.totalOrders || 0);

                    // Display wallet balance
                    var wallet_balance = 0;
                    if (user.wallet_amount != null && !isNaN(user.wallet_amount)) {
                        wallet_balance = user.wallet_amount;
                    }
                    if (currencyAtRight) {
                        wallet_balance = parseFloat(wallet_balance).toFixed(decimal_degits) + "" + currentCurrency;
                    } else {
                        wallet_balance = currentCurrency + "" + parseFloat(wallet_balance).toFixed(decimal_degits);
                    }
                    $('.wallet_balance').html(wallet_balance);

                    // Display profile image
                    var image = "";
                    if (user.profilePictureURL != "" && user.profilePictureURL != null) {
                        image = '<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" width="100px" id="" height="auto" src="' + user.profilePictureURL + '">';
                    } else {
                        image = '<img width="100px" id="" height="auto" src="' + placeholderImage + '">';
                    }
                    $('.profile_image').html(image);

                    // Display shipping addresses
                    var address = '';
                    if (user.shippingAddress && Array.isArray(user.shippingAddress)) {
                        shippingAddress = user.shippingAddress;
                        address+='<div id="append_list1" class="res-search-list row">';
                        shippingAddress.forEach((listval) => {
                        var defaultBtnHtml = '';
                        if (listval.isDefault == true) {
                            if (listval.hasOwnProperty('location') && listval.location) {
                                var lat = listval.location.latitude;
                                var lng = listval.location.longitude;
                                var mapSrc = `https://maps.google.com/maps?width=600&height=225&hl=en&q=${lat},${lng}&t=&z=14&ie=UTF8&iwloc=B&output=embed`;
                                $(".mapouter").show();
                                $(".gmap_iframe").attr("src", mapSrc);
                            } else {
                                $(".mapouter").html("<p>No map available</p>");
                            }
                            defaultBtnHtml = '<span class="badge badge-success ml-2 py-2 px-2" type="button" >Default</span>';
                        }
                        address = address + '<div class="transactions-list-wrap mt-4 col-md-12">';
                        address +='<div class="bg-white rounded-lg mb-3 transactions-list-view shadow-sm">';
                        address +='<div class="gold-members d-flex align-items-start transactions-list">';
                        address = address + '<div class="media transactions-list-left w-100">';
                        address = address + '<div class="media-body"><h6>' + listval.address + "," + listval.locality + " " + listval.landmark + '</h6>';
                        address = address + '<span class="badge badge-info py-2 px-2">' + listval.addressAs + '</span>' + defaultBtnHtml ;
                        address += '</div></div>';
                        address = address + '</div> </div></div>';
                        });
                        address +='</div>';
                    }
                    if (address != "") {
                        $('.address').html(address);
                    } else {
                        $('.address').html("<h5>{{trans('lang.not_mentioned')}}</h5>");
                    }

                    jQuery("#data-table_processing").hide();
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error loading user data:', error);
                    console.error('Response:', xhr.responseText);
                    jQuery("#data-table_processing").hide();
                    $('#user-data-loading').hide();
                    $('#user-data-error').show();

                    // Show specific error message
                    var errorMsg = 'Failed to load user data';
                    if (xhr.status === 404) {
                        errorMsg = 'User not found';
                    } else if (xhr.status === 403) {
                        errorMsg = 'Access denied';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#user-data-error .error-message').text(errorMsg);
                }
            });
        });
        $(".save-form-btn").click(function () {
            var amount = $('#amount').val();
            if (amount == '') {
                $('#wallet_error').text('{{trans("lang.add_wallet_amount_error")}}')
                return false;
            }
            var note = $('#note').val();

            console.log('✅ Adding wallet amount via SQL API');

            // Add wallet amount via SQL API
            $.ajax({
                url: '{{ url("/users/wallet") }}/' + id,
                method: 'POST',
                dataType: 'json',
                data: {
                    amount: amount,
                    note: note,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('✅ Wallet amount added successfully:', response);

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Wallet amount added successfully',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        console.error('❌ Failed to add wallet amount:', response.message);
                        $('#user_account_not_found_error').text(response.message || '{{trans("lang.user_detail_not_found")}}');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error adding wallet amount:', error);
                    console.error('Response:', xhr.responseText);

                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add wallet amount. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endsection
