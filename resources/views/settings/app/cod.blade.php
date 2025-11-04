@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <!-- <div class="row page-titles"> -->
        <div class="card">
            <div class="payment-top-tab mt-3 mb-3">
                <ul class="nav nav-tabs card-header-tabs align-items-end">
                    <li class="nav-item">
                        <a class="nav-link  stripe_active_label" href="{!! url('settings/payment/stripe') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_stripe')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active cod_active_label" href="{!! url('settings/payment/cod') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_cod_short')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link razorpay_active_label" href="{!! url('settings/payment/razorpay') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_razorpay')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link paypal_active_label" href="{!! url('settings/payment/paypal') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_paypal')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link paytm_active_label" href="{!! url('settings/payment/paytm') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_paytm')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link wallet_active_label" href="{!! url('settings/payment/wallet') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_wallet')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link payfast_active_label" href="{!! url('settings/payment/payfast') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.payfast')}}<span
                                    class="badge ml-2"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link paystack_active_label" href="{!! url('settings/payment/paystack') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_paystack_lable')}}<span
                                    class="badge ml-2"></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link flutterWave_active_label" href="{!! url('settings/payment/flutterwave') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.flutterWave')}}<span
                                    class="badge ml-2"></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mercadopago_active_label"
                        href="{!! url('settings/payment/mercadopago') !!}"><i
                                    class="fa fa-envelope-o mr-2"></i>{{trans('lang.mercadopago')}}<span
                                    class="badge ml-2"></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link xendit_active_label"
                           href="{!! url('settings/payment/xendit') !!}"><i
                                class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_xendit')}}<span
                                class="badge ml-2"></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link orangepay_active_label"
                           href="{!! url('settings/payment/orangepay') !!}"><i
                                class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_orangepay')}}<span
                                class="badge ml-2"></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link midtrans_active_label"
                           href="{!! url('settings/payment/midtrans') !!}"><i
                                class="fa fa-envelope-o mr-2"></i>{{trans('lang.app_setting_midtrans')}}<span
                                class="badge ml-2"></span></a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend><i class="mr-3 fa fa-money"></i>{{trans('lang.app_setting_cod_short')}}</legend>
                            <div class="form-check width-100">
                                <input type="checkbox" class=" enable_cod" id="enable_cod">
                                <label class="col-3 control-label"
                                       for="enable_cod">{{trans('lang.app_setting_enable_cod')}}</label>
                                <div class="form-text text-muted">
                                    {!! trans('lang.app_settings_enable_cod_help') !!}
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary edit-setting-btn"><i
                            class="fa fa-save"></i> {{trans('lang.save')}}</button>
                <a href="{{url('/dashboard')}}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            jQuery("#data-table_processing").show();

            // Load COD settings from SQL
            $.get("{{ route('api.cod.settings') }}", function(cod) {
                if (cod.isEnabled) {
                    $(".enable_cod").prop('checked', true);
                    jQuery(".cod_active_label span").addClass('badge-success');
                    jQuery(".cod_active_label span").text('Active');
                }

                // Load payment gateway statuses for tab badges
                $.get("{{ route('api.stripe.settings') }}", function(stripe) {
                    if (stripe.isEnabled) {
                        jQuery(".stripe_active_label span").addClass('badge-success');
                        jQuery(".stripe_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.razorpay.settings') }}", function(razorPay) {
                    if (razorPay.isEnabled) {
                        jQuery(".razorpay_active_label span").addClass('badge-success');
                        jQuery(".razorpay_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.paypal.settings') }}", function(paypal) {
                    if (paypal.isEnabled) {
                        jQuery(".paypal_active_label span").addClass('badge-success');
                        jQuery(".paypal_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.paytm.settings') }}", function(paytm) {
                    if (paytm.isEnabled) {
                        jQuery(".paytm_active_label span").addClass('badge-success');
                        jQuery(".paytm_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.wallet.settings') }}", function(wallet) {
                    if (wallet.isEnabled) {
                        jQuery(".wallet_active_label span").addClass('badge-success');
                        jQuery(".wallet_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.payfast.settings') }}", function(payFast) {
                    if (payFast.isEnable) {
                        jQuery(".payfast_active_label span").addClass('badge-success');
                        jQuery(".payfast_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.paystack.settings') }}", function(payStack) {
                    if (payStack.isEnable) {
                        jQuery(".paystack_active_label span").addClass('badge-success');
                        jQuery(".paystack_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.flutterwave.settings') }}", function(flutterWave) {
                    if (flutterWave.isEnable) {
                        jQuery(".flutterWave_active_label span").addClass('badge-success');
                        jQuery(".flutterWave_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.mercadopago.settings') }}", function(mercadopago) {
                    if (mercadopago.isEnabled) {
                        jQuery(".mercadopago_active_label span").addClass('badge-success');
                        jQuery(".mercadopago_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.xendit.settings') }}", function(xendit) {
                    if (xendit.enable) {
                        jQuery(".xendit_active_label span").addClass('badge-success');
                        jQuery(".xendit_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.orangepay.settings') }}", function(orangePay) {
                    if (orangePay.enable) {
                        jQuery(".orangepay_active_label span").addClass('badge-success');
                        jQuery(".orangepay_active_label span").text('Active');
                    }
                });

                $.get("{{ route('api.midtrans.settings') }}", function(midtrans) {
                    if (midtrans.enable) {
                        jQuery(".midtrans_active_label span").addClass('badge-success');
                        jQuery(".midtrans_active_label span").text('Active');
                    }
                });

                jQuery("#data-table_processing").hide();
            });

            // Save COD settings to SQL
            $(".edit-setting-btn").click(function () {
                var isCODEnabled = $(".enable_cod").is(":checked");

                $.ajax({
                    url: "{{ route('api.cod.update') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        isEnabled: isCODEnabled
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ url("settings/payment/cod")}}';
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('Error saving COD settings');
                    }
                });
            });
        });
    </script>
@endsection
