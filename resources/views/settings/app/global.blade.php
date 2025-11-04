@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.app_setting_global') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.app_setting_global') }}</li>
                </ol>
            </div>
        </div>
        <div class="card-body">
            <div class="error_top" style="display:none"></div>
            <div class="row restaurant_payout_create">
                <div class="restaurant_payout_create-inner">
                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-settings"></i>{{ trans('lang.app_setting_global') }}</legend>
                        <div class="form-group row width-100">
                            <label class="col-5 control-label">{{ trans('lang.app_setting_app_name') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control application_name">
                                <div class="form-text text-muted">
                                    {{ trans('lang.app_setting_app_name_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-5 control-label">{{ trans('lang.app_setting_meta_title') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control meta_title">
                                <div class="form-text text-muted">
                                    {{ trans('lang.app_setting_meta_title_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.upload_app_logo') }}</label>
                            <input type="file" class="col-7" onChange="handleFileSelect(event)">
                            <div id="uploding_image"></div>
                            <div class="logo_img_thumb"></div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.menu_placeholder_image') }}</label>
                            <input type="file" class="col-7" onChange="handleFileSelectplaceholder(event)">
                            <div id="uploading_placeholder"></div>
                            <div class="placeholder_img_thumb">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.upload_favicon') }}</label>
                            <input type="file" class="col-7" onChange="handleFileSelectFavicon(event)">
                            <div id="uploding_favicon"></div>
                            <div class="favicon_img_thumb"></div>
                        </div>
                        <div class="form-group width-100 choose-theme">
                            <label class="col-12 control-label">{{ trans('lang.app_homepage_theme') }}</label>
                            <div class="col-12">
                                <div class="select-theme-radio">
                                    <label class="form-check-label" for="app_homepage_theme_1">
                                        <input type="radio" class="btn-check" name="app_homepage_theme" id="app_homepage_theme_1" value="theme_1">
                                        <img src="{{ url('images/app_homepage_theme_1.png') }}" height="150">
                                    </label>
                                    <label class="form-check-label" for="app_homepage_theme_2">
                                        <input type="radio" class="btn-check" name="app_homepage_theme" id="app_homepage_theme_2" value="theme_2">
                                        <img src="{{ url('images/app_homepage_theme_2.png') }}" height="150">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.admin_panel_color_settings') }}</label>
                            <input type="color" class="ml-3" name="admin_color" id="admin_color">
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.store_panel_color_settings') }}</label>
                            <input type="color" class="ml-3" name="store_color" id="store_color">
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.website_color_settings') }}</label>
                            <input type="color" class="ml-3" name="website_color" id="website_color">
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.app_customer_color_settings') }}</label>
                            <input type="color" class="ml-3" name="customer_app_color" id="customer_app_color">
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.app_driver_color_settings') }}</label>
                            <input type="color" class="ml-3" name="driver_app_color" id="driver_app_color">
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.app_restaurant_color_settings') }}</label>
                            <input type="color" class="ml-3" name="restaurant_app_color" id="restaurant_app_color">
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>{{ trans('lang.google_map_api_key_title') }}</legend>
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{ trans('lang.google_map_api_key') }}</label>
                            <div class="col-7">
                                <input type="password" class="form-control address_line1" name="map_key" id="map_key">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 fa fa-solid fa-address-book"></i>{{ trans('lang.contact_us') }}</legend>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.contact_us_address') }}</label>
                            <div class="col-7">
                                <textarea class="form-control contact_us_address" rows="3"></textarea>
                                <div class="form-text text-muted">
                                    {{ trans('lang.contact_us_address_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.contact_us_email') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control contact_us_email">
                                <div class="form-text text-muted">
                                    {{ trans('lang.contact_us_email_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.contact_us_phone') }}</label>
                            <div class="col-7">
                                <input type="number" class="form-control contact_us_phone">
                                <div class="form-text text-muted">
                                    {{ trans('lang.contact_us_phone_help') }}
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>{{ trans('lang.map_redirection') }}</legend>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.select_map_type_for_application') }}</label>
                            <div class="col-7">
                                <select name="selectedMapType" id="selectedMapType" class="form-control selectedMapType">
                                    <option value="google">{{ trans('lang.google_maps') }}</option>
                                    <option value="osm">{{ trans('lang.open_street_map') }}</option>
                                </select>
                            </div>
                            <div class="form-text pl-3 text-muted">
                                <span><strong>{{ trans('lang.note') }} :</strong>
                                    {{ trans('lang.google_map_note') }}<br>
                                    {{ trans('lang.open_street_map_note') }}<br>
                                    <strong>{{ trans('lang.recommended_note') }}</strong></span>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.select_map_type') }}</label>
                            <div class="col-7">
                                <select name="map_type" id="map_type" class="form-control map_type">
                                    <option value="">{{ trans('lang.select_type') }}</option>
                                    <option value="google">{{ trans('lang.google_map') }}</option>
                                    <option value="googleGo">{{ trans('lang.google_go_map') }}</option>
                                    <option value="waze">{{ trans('lang.waze_map') }}</option>
                                    <option value="mapswithme">{{ trans('lang.mapswithme_map') }}</option>
                                    <option value="yandexNavi">{{ trans('lang.vandexnavi_map') }}</option>
                                    <option value="yandexMaps">{{ trans('lang.vandex_map') }}</option>
                                    <option value="inappmap">{{ trans('lang.inapp_map') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.driver_location_update') }}</label>
                            <div class="col-7">
                                <input name="radius" id="driver_location_update" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <div class="form-check width-100">
                                <input type="checkbox" class="form-check-inline" id="single_order_receive">
                                <label class="col-5 control-label" for="single_order_receive">{{ trans('lang.single_order_receive') }}</label>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <div class="form-check width-100">
                                <input type="checkbox" class="form-check-inline" id="auto_approve_driver">
                                <label class="col-5 control-label" for="auto_approve_driver">{{ trans('lang.auto_approve_driver') }}</label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-cash-100"></i>{{ trans('lang.wallet_settings') }}</legend>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.minimum_deposit_amount') }}</label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <input type="number" class="form-control minimum_deposit_amount">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.minimum_withdrawal_amount') }}</label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <input type="number" class="form-control minimum_withdrawal_amount">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-share"></i>{{ trans('lang.referral_settings') }}</legend>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.referral_amount') }}</label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <input type="number" class="form-control referral_amount">
                                    <span class="currentCurrency"></span>
                                    <div class="form-text text-muted">
                                        {{ trans('lang.referral_amount_help') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-shopping"></i>{{ trans('lang.restaurant_settings') }}</legend>
                        <div class="form-group row width-100">
                            <div class="form-check width-100">
                                <input type="checkbox" class="form-check-inline" id="restaurant_can_upload_story">
                                <label class="col-5 control-label" for="restaurant_can_upload_story">{{ trans('lang.restaurant_can_upload_story') }}</label>
                                <input type="checkbox" class="form-check-inline" id="auto_approve_restaurant">
                                <label class="col-5 control-label" for="auto_approve_restaurant">{{ trans('lang.auto_approve_restaurant') }}</label>
                            </div>
                        </div>
                        <div class="form-group row width-50" id="story_upload_time_div" style="display:none;">
                            <label class="col-5 control-label">{{ trans('lang.story_upload_time') }}</label>
                            <div class="col-7">
                                <input type="number" class="form-control" id="story_upload_time" value="30" min="0">
                                <div class="form-text text-muted">
                                    {{ trans('lang.story_upload_time_help') }}
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-newspaper"></i>{{ trans('lang.advertisement_setting') }}</legend>
                        <div class="form-group row width-100">
                            <div class="form-check width-100">
                                <input type="checkbox" class="form-check-inline" id="enable_adv_feature">
                                <label class="col-5 control-label" for="enable_adv_feature">{{ trans('lang.enable_adv_feature') }}</label>
                                <div class="form-text text-muted">
                                    {{ trans('lang.enable_adv_feature_help') }}
                                </div>
                            </div>
                        </div>

                    </fieldset>

                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-truck-delivery"></i>{{ trans('lang.self_delivery_setting') }}</legend>
                        <div class="form-group row width-100">
                            <div class="form-check width-100">
                                <input type="checkbox" class="form-check-inline" id="enable_self_delivery">
                                <label class="col-5 control-label" for="enable_self_delivery">{{ trans('lang.enable_self_delivery') }}</label>
                                <div class="form-text text-muted">
                                    {{ trans('lang.enable_self_delivery_help') }}
                                </div>
                            </div>
                        </div>

                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-music-box"></i>{{ trans('lang.order_ringtone_setting') }}</legend>
                        <div class="form-group row width-100">
                            <div class="form-check width-100">
                                <input type="file" id="ringtone_file" onchange="handleRingtoneSelect(event)">
                                <div class="ringtone_file mt-2"></div>
                                <div class="form-text text-muted w-50">{!! nl2br(trans('lang.audio_information')) !!}
                                </div>
                            </div>

                    </fieldset>

                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-email"></i>{{ trans('lang.email_setting') }}</legend>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.smtp') }}
                                {{ trans('lang.from_name') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control from_name">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.smtp') }} {{ trans('lang.host') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control host">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.smtp') }} {{ trans('lang.port') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control port">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.smtp_user_name') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_name">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.smtp') }}
                                {{ trans('lang.password') }}</label>
                            <div class="col-7">
                                <input type="password" class="form-control password">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 mdi mdi-comment-alert"></i>{{ trans('lang.notification_setting') }}
                        </legend>
                        <div class="form-group row width-100">
                            <label class="col-5 control-label">{{ trans('lang.sender_id') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control" id="sender_id">
                            </div>
                            <div class="form-text pl-3 text-muted">
                                {{ trans('lang.notification_sender_id_help') }}
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{ trans('lang.upload_json_file') }}</label>
                            <input type="file" class="col-7 pb-2" onChange="handleUploadJsonFile(event)">
                            <div id="uploding_json_file"></div>
                            <div id="uploded_json_file"></div>
                            <div class="form-text pl-3 text-muted">
                                {{ trans('lang.notification_json_file_help') }}
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><i class="mr-3 fa fa-solid fa fa-android"></i>{{ trans('lang.version') }}</legend>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.app_version') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control app_version">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.web_version') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control" id="web_version">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.app_setting_app_store_link') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control" id="app_store_link">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.app_setting_play_store_link') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control" id="play_store_link">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.setting_website_url') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control" id="website_url">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-5 control-label">{{ trans('lang.setting_store_url') }}</label>
                            <div class="col-7">
                                <input type="text" class="form-control" id="store_url">
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class="form-group col-12 text-center btm-btn">
            <button type="button" class="btn btn-primary edit-setting-btn"><i class="fa fa-save"></i>
                {{ trans('lang.save') }}</button>
            <a href="{{ url('/dashboard') }}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel') }}
            </a>
        </div>
    </div>
    <div class="modal fade" id="themeModal" tabindex="-1" role="dialog" aria-labelledby="themeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 50%;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <img id="themeImage" src="" width="630">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // SQL mode - no Firebase
        var theme_1_url = '{!! url('images/app_homepage_theme_1.png') !!}';
        var theme_2_url = '{!! url('images/app_homepage_theme_2.png') !!}';
        var photo = "";
        var placeholderphoto = '';
        var favicon = "";
        var appLogoImagePath = '';
        var appFavIconImagePath = '';
        var placeholderImagePath = '';
        var logoFileName = '';
        var favIconFileName = '';
        var serviceJsonFile = '';
        var placeholderFileName = '';
        var audioData = '';
        var audioFileName = '';
        var oldAudioData = '';

        // Load currency from SQL
        $.ajax({
            url: '{{ route("payments.currency") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    $(".currentCurrency").text(response.data.symbol);
                }
            },
            error: function() {
                $(".currentCurrency").text('₹');
            }
        });
        $(document).ready(function() {
            jQuery("#data-table_processing").show();

            // Load global settings from SQL
            $.get("{{ route('api.global.settings') }}", function(globalSettings) {
                try {
                    $(".application_name").val(globalSettings.applicationName || '');
                    $(".meta_title").val(globalSettings.meta_title || '');
                    $("#website_color").val(globalSettings.website_color || '#fa8500');
                    $("#admin_color").val(globalSettings.admin_panel_color || '#ff6839');
                    $("#store_color").val(globalSettings.store_panel_color || '#ff683a');
                    $("#customer_app_color").val(globalSettings.app_customer_color || '#f47825');
                    $("#driver_app_color").val(globalSettings.app_driver_color || '#c33737');
                    $("#restaurant_app_color").val(globalSettings.app_restaurant_color || '#e7463c');

                    if (globalSettings.appLogo != "" && globalSettings.appLogo != null) {
                        photo = globalSettings.appLogo;
                        appLogoImagePath = globalSettings.appLogo;
                        $(".logo_img_thumb").append('<img class="rounded" style="width:50px" src="' +
                            photo + '" alt="image">');
                    }
                    if (globalSettings.favicon != "" && globalSettings.favicon != null) {
                        favicon = globalSettings.favicon;
                        appFavIconImagePath = globalSettings.favicon;
                        $(".favicon_img_thumb").append('<img class="rounded" style="width:50px" src="' +
                            favicon + '" alt="image">');
                    }
                    (globalSettings.isEnableAdsFeature) ? $('#enable_adv_feature').prop('checked', true): '';
                    (globalSettings.isSelfDelivery) ? $('#enable_self_delivery').prop('checked', true): '';

                    if (globalSettings.order_ringtone_url != '' && globalSettings.order_ringtone_url != null) {
                        audioData = globalSettings.order_ringtone_url;
                        oldVideo = globalSettings.order_ringtone_url;
                        console.log('Audio URL:', audioData);
                        var html = '<div class="col-md-3">\n' +
                            '<div class="audio-inner">\n' +
                            '  <audio controls>\n' +
                            '    <source src="' + audioData + '" type="audio/mp3">\n' +
                            '    Your browser does not support the audio element.\n' +
                            '  </audio>\n' +
                            '</div>\n' +
                            '</div>';
                        $(".ringtone_file").html(html);
                    }
                } catch (error) {
                    console.error('Error loading global settings:', error);
                }
                jQuery("#data-table_processing").hide();
            }).fail(function() {
                jQuery("#data-table_processing").hide();
                console.error('Failed to load global settings from SQL');
            });

            // Load all other settings from SQL
            loadAllSettings();
        });

        // Function to load all settings from SQL database
        function loadAllSettings() {
            // Load notification settings
            $.get("{{ route('api.notification.settings') }}", function(notificationData) {
                if (notificationData.senderId) {
                    $('#sender_id').val(notificationData.senderId);
                }
                if (notificationData.serviceJson) {
                    $('#uploded_json_file').html("<a href='" + notificationData.serviceJson +
                        "' class='btn-link pl-3' target='_blank'>See Uploaded File</a>");
                    serviceJsonFile = notificationData.serviceJson;
                }
            });

            // Load placeholder image from global settings
            $.get("{{ route('api.global.settings') }}", function(data) {
                if (data.placeHolderImage) {
                    placeholderphoto = data.placeHolderImage;
                    placeholderImagePath = data.placeHolderImage;
                    $(".placeholder_img_thumb").append('<img class="rounded" style="width:50px" src="' +
                        placeholderphoto + '" alt="image">');
                }
                // Restaurant auto-approve
                if (data.auto_approve_restaurant) {
                    $("#auto_approve_restaurant").prop('checked', true);
                }
                // Map key
                if (data.map_key) {
                    $('#map_key').val(data.map_key);
                }
                // Driver settings
                if (data.minimumDepositToRideAccept) {
                    $(".minimum_deposit_amount").val(data.minimumDepositToRideAccept);
                }
                if (data.minimumAmountToWithdrawal) {
                    $(".minimum_withdrawal_amount").val(data.minimumAmountToWithdrawal);
                }
                if (data.mapType) {
                    $('#map_type').val(data.mapType).trigger('change');
                }
                if (data.selectedMapType) {
                    $('#selectedMapType').val(data.selectedMapType).trigger('change');
                }
                if (data.driverLocationUpdate) {
                    $('#driver_location_update').val(data.driverLocationUpdate);
                }
                if (data.singleOrderReceive) {
                    $('#single_order_receive').prop('checked', true);
                }
                if (data.auto_approve_driver) {
                    $('#auto_approve_driver').prop('checked', true);
                }
                // Referral amount
                if (data.referralAmount) {
                    $(".referral_amount").val(data.referralAmount);
                }
                // Email settings
                if (data.fromName) {
                    $('.from_name').val(data.fromName);
                }
                if (data.host) {
                    $('.host').val(data.host);
                }
                if (data.port) {
                    $('.port').val(data.port);
                }
                if (data.userName) {
                    $('.user_name').val(data.userName);
                }
                if (data.password) {
                    $('.password').val(data.password);
                }
                // Version data
                if (data.app_version) {
                    $('.app_version').val(data.app_version);
                }
                if (data.web_version) {
                    $('#web_version').val(data.web_version);
                }
                if (data.appStoreLink) {
                    $('#app_store_link').val(data.appStoreLink);
                }
                if (data.googlePlayLink) {
                    $('#play_store_link').val(data.googlePlayLink);
                }
                if (data.websiteUrl) {
                    $('#website_url').val(data.websiteUrl);
                }
                if (data.storeUrl) {
                    $('#store_url').val(data.storeUrl);
                }
                // Contact us
                if (data.Address) {
                    $('.contact_us_address').val(data.Address);
                }
                if (data.Email) {
                    $('.contact_us_email').val(data.Email);
                }
                if (data.Phone) {
                    $('.contact_us_phone').val(data.Phone);
                }
            });

            // Load story settings
            $.get("{{ route('api.story.settings') }}", function(story_data) {
                if (story_data.isEnabled) {
                    $("#restaurant_can_upload_story").prop('checked', true);
                    $("#story_upload_time_div").show();
                }
                if (story_data.videoDuration) {
                    $("#story_upload_time").val(story_data.videoDuration);
                }
            });

            // Load homepage theme
            $.get("{{ route('api.global.settings') }}", function(data) {
                if (data.theme == "theme_1") {
                    $("#app_homepage_theme_1").prop('checked', true);
                } else if (data.theme == "theme_2") {
                    $("#app_homepage_theme_2").prop('checked', true);
                }
            });
        });
        $(".edit-setting-btn").click(function() {
            var website_color = $("#website_color").val();
            var admin_color = $("#admin_color").val();
            var customer_app_color = $("#customer_app_color").val();
            var driver_app_color = $("#driver_app_color").val();
            var restaurant_app_color = $("#restaurant_app_color").val();
            var googleApiKey = $("#map_key").val();
            var store_color = $("#store_color").val();
            var contact_us_address = $('.contact_us_address').val();
            var contact_us_email = $('.contact_us_email').val();
            var contact_us_phone = $('.contact_us_phone').val();
            var app_version = $('.app_version').val();
            var web_version = $('#web_version').val();
            var app_store_link = $('#app_store_link').val();
            var play_store_link = $('#play_store_link').val();
            var website_url = $('#website_url').val();
            var store_url = $('#store_url').val();
            var auto_approve_restaurant = $("#auto_approve_restaurant").is(":checked");
            var restaurant_can_upload_story = $("#restaurant_can_upload_story").is(":checked");
            var story_upload_time = parseInt($('#story_upload_time').val());
            var minimumDepositToRideAccept = $(".minimum_deposit_amount").val();
            var minimumAmountToWithdrawal = $(".minimum_withdrawal_amount").val();
            var referralAmount = $(".referral_amount").val();
            var app_homepage_theme = $(".form-group input[name='app_homepage_theme']:checked").val();
            var senderId = $("#sender_id").val();
            var fromName = $('.from_name').val();
            var host = $('.host').val();
            var port = $('.port').val();
            var userName = $('.user_name').val();
            var password = $('.password').val();
            if (admin_color != null) {
                setCookie('admin_panel_color', admin_color, 365);
            }
            var applicationName = $(".application_name").val();
            var meta_title = $(".meta_title").val();
            var selectedMapType = $("#selectedMapType").val();
            var map_type = $('#map_type').val();
            var driver_location_update = $('#driver_location_update').val();
            var single_order_receive = $("#single_order_receive").is(":checked");
            var auto_approve_driver = $("#auto_approve_driver").is(":checked");
            var enable_adv_feature = $("#enable_adv_feature").is(":checked");
            var enable_self_delivery = $("#enable_self_delivery").is(":checked");
            if (applicationName == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.enter_app_name_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (minimumDepositToRideAccept == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.enter_minimum_deposit_amount_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (minimumAmountToWithdrawal == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.enter_minimum_withdrawal_amount_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (referralAmount == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.enter_referral_amount_error') }}</p>");
                window.scrollTo(0, 0);
                window.scrollTo(0, 0);
            } else if (host == "") {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.host_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (port == "") {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.port_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (userName == "") {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.username_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (password == "") {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.password_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (senderId == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.notification_sender_id_error') }}</p>");
                window.scrollTo(0, 0);
            } else if (serviceJsonFile == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.notification_service_json_error') }}</p>");
                window.scrollTo(0, 0);
            } else {
                jQuery("#data-table_processing").show();
                storeImageData().then(IMG => {
                    storeRingtone().then(ringtone => {
                        // Save all settings to SQL via single API call
                        $.ajax({
                            url: "{{ route('api.global.update') }}",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                website_color: website_color,
                                admin_panel_color: admin_color,
                                store_panel_color: store_color,
                                app_customer_color: customer_app_color,
                                app_driver_color: driver_app_color,
                                app_restaurant_color: restaurant_app_color,
                                applicationName: applicationName,
                                meta_title: meta_title,
                                appLogo: IMG.photo,
                                favicon: IMG.favicon,
                                isEnableAdsFeature: enable_adv_feature,
                                isSelfDelivery: enable_self_delivery,
                                order_ringtone_url: ringtone,
                                placeHolderImage: IMG.placeholderphoto,
                                Address: contact_us_address,
                                Email: contact_us_email,
                                Phone: contact_us_phone,
                                app_version: app_version,
                                web_version: web_version,
                                appStoreLink: app_store_link,
                                googlePlayLink: play_store_link,
                                websiteUrl: website_url,
                                storeUrl: store_url,
                                auto_approve_restaurant: auto_approve_restaurant,
                                minimumDepositToRideAccept: minimumDepositToRideAccept,
                                minimumAmountToWithdrawal: minimumAmountToWithdrawal,
                                selectedMapType: selectedMapType,
                                mapType: map_type,
                                driverLocationUpdate: driver_location_update,
                                singleOrderReceive: single_order_receive,
                                auto_approve_driver: auto_approve_driver,
                                referralAmount: referralAmount,
                                theme: app_homepage_theme,
                                map_key: googleApiKey
                            },
                            success: function(response) {
                                // Also update story settings
                                $.ajax({
                                    url: "{{ route('api.story.update') }}",
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        isEnabled: restaurant_can_upload_story,
                                        videoDuration: story_upload_time
                                    }
                                });

                                // Update notification settings
                                $.ajax({
                                    url: "{{ route('api.notification.update') }}",
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        senderId: senderId,
                                        serviceJson: serviceJsonFile
                                    }
                                });

                                // Update email settings in global settings
                                $.ajax({
                                    url: "{{ route('api.global.update') }}",
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        fromName: fromName,
                                        host: host,
                                        port: port,
                                        userName: userName,
                                        password: password,
                                        mailMethod: "smtp",
                                        mailEncryptionType: "ssl"
                                    },
                                    success: function() {
                                        jQuery("#data-table_processing").hide();
                                        // Refresh custom ringtone in notification system if available
                                        if (typeof window.orderNotificationSystem !== 'undefined' && window.orderNotificationSystem.refreshCustomRingtone) {
                                            window.orderNotificationSystem.refreshCustomRingtone();
                                            console.log('Custom ringtone refreshed after settings save');
                                        }
                                        window.location.href = '{{ url('settings/app/globals') }}';
                                    },
                                    error: function(xhr) {
                                        jQuery("#data-table_processing").hide();
                                        $(".error_top").show();
                                        $(".error_top").html("");
                                        $(".error_top").append("<p>Error saving email settings</p>");
                                        window.scrollTo(0, 0);
                                    }
                                });
                            },
                            error: function(xhr) {
                                jQuery("#data-table_processing").hide();
                                $(".error_top").show();
                                $(".error_top").html("");
                                $(".error_top").append("<p>Error saving settings: " + (xhr.responseJSON?.message || 'Unknown error') + "</p>");
                                window.scrollTo(0, 0);
                            }
                        });
                    }).catch(err => {
                        jQuery("#data-table_processing").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>Ringtone upload error: " + err + "</p>");
                        window.scrollTo(0, 0);
                    });
                }).catch(err => {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>Image upload error: " + err + "</p>");
                    window.scrollTo(0, 0);
                });
            }
        });
        $("#restaurant_can_upload_story").click(function() {
            if ($(this).is(':checked')) {
                $("#story_upload_time_div").show();
            } else {
                $("#story_upload_time_div").hide();
            }
        });
        $(".form-group input[name='app_homepage_theme']").click(function() {
            if ($(this).is(':checked')) {
                var modal = $('#themeModal');
                if ($(this).val() == "theme_1") {
                    modal.find('#themeImage').attr('src', theme_1_url);
                } else {
                    modal.find('#themeImage').attr('src', theme_2_url);
                }
                $('#themeModal').modal('show');
            }
        });
        $('#themeModal').on('hide.bs.modal', function(event) {
            var modal = $(this);
            modal.find('#themeImage').attr('src', '');
        });
        // Upload images using Laravel API
        async function storeImageData() {
            var newPhoto = [];
            try {
                // Upload app logo if changed
                if (photo != appLogoImagePath && photo) {
                    var uploadedPhoto = await uploadImageToServer(photo, logoFileName);
                    if (uploadedPhoto) {
                        newPhoto['photo'] = uploadedPhoto;
                        photo = uploadedPhoto;
                    } else {
                        newPhoto['photo'] = appLogoImagePath;
                    }
                } else {
                    newPhoto['photo'] = photo || appLogoImagePath;
                }

                // Upload favicon if changed
                if (favicon != appFavIconImagePath && favicon) {
                    var uploadedFavicon = await uploadImageToServer(favicon, favIconFileName);
                    if (uploadedFavicon) {
                        newPhoto['favicon'] = uploadedFavicon;
                        favicon = uploadedFavicon;
                    } else {
                        newPhoto['favicon'] = appFavIconImagePath;
                    }
                } else {
                    newPhoto['favicon'] = favicon || appFavIconImagePath;
                }

                // Upload placeholder if changed
                if (placeholderphoto != placeholderImagePath && placeholderphoto) {
                    var uploadedPlaceholder = await uploadImageToServer(placeholderphoto, placeholderFileName);
                    if (uploadedPlaceholder) {
                        newPhoto['placeholderphoto'] = uploadedPlaceholder;
                        placeholderphoto = uploadedPlaceholder;
                    } else {
                        newPhoto['placeholderphoto'] = placeholderImagePath;
                    }
                } else {
                    newPhoto['placeholderphoto'] = placeholderphoto || placeholderImagePath;
                }
            } catch (error) {
                console.error("Image upload error:", error);
            }
            return newPhoto;
        }

        // Helper function to upload image to Laravel server
        async function uploadImageToServer(base64Data, filename) {
            try {
                var response = await $.ajax({
                    url: '/upload-image',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        image: base64Data,
                        filename: filename
                    }
                });
                if (response.success) {
                    return response.url;
                }
                return null;
            } catch (error) {
                console.error('Upload error:', error);
                return null;
            }
        }

        function handleFileSelect(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var filePayload = e.target.result;
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    photo = filePayload;
                    logoFileName = filename;
                    $(".logo_img_thumb").empty();
                    $(".logo_img_thumb").append(
                        '<span class="image-item"><img class="rounded" style="width:50px" src="' +
                        filePayload + '" alt="image"></span>');
                };
            })(f);
            reader.readAsDataURL(f);
        }

        function handleFileSelectplaceholder(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var filePayload = e.target.result;
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    placeholderphoto = filePayload;
                    placeholderFileName = filename;
                    $(".placeholder_img_thumb").empty();
                    $(".placeholder_img_thumb").append(
                        '<span class="image-item"><img class="rounded" style="width:50px" src="' +
                        filePayload + '" alt="image"></span>');
                };
            })(f);
            reader.readAsDataURL(f);
        }

        function handleFileSelectFavicon(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var filePayload = e.target.result;
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    favicon = filePayload;
                    favIconFileName = filename;
                    $(".favicon_img_thumb").empty();
                    $(".favicon_img_thumb").append(
                        '<span class="image-item"><img class="rounded" style="width:50px" src="' +
                        filePayload + '" alt="image"></span>');
                };
            })(f);
            reader.readAsDataURL(f);
        }

        function handleUploadJsonFile(evt) {
            var f = evt.target.files[0];
            var formData = new FormData();
            formData.append('file', f);

            jQuery("#uploding_json_file").text("File is uploading...");

            $.ajax({
                url: '/upload-json',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        jQuery("#uploding_json_file").text("Upload is completed");
                        serviceJsonFile = response.url;
                        setTimeout(function() {
                            jQuery("#uploding_json_file").hide();
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    jQuery("#uploding_json_file").text("Upload failed");
                    console.error('JSON upload error:', xhr);
                }
            });
        }

        async function handleRingtoneSelect(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            var isAudio = document.getElementById('ringtone_file');
            var audioValue = isAudio.value;
            var allowedExtensions = /(\.mp3)$/i;
            if (!allowedExtensions.exec(audioValue)) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>Error: Invalid audio type</p>");
                window.scrollTo(0, 0);
                isAudio.value = '';
                return false;
            }

            var audio = document.createElement('audio');
            audio.preload = 'metadata';

            audio.onloadedmetadata = function() {
                window.URL.revokeObjectURL(audio.src);

                reader.onload = (function(theFile) {
                    return function(e) {
                        var filePayload = e.target.result;
                        var val = f.name;
                        var ext = val.split('.').pop(); // get the extension
                        var filename = val.replace(/C:\\fakepath\\/i, '');

                        var timestamp = Number(new Date());
                        var filenameWithoutExt = filename.split('.').slice(0, -1).join('.');
                        var finalFilename = filenameWithoutExt + "_" + timestamp + '.' + ext;

                        audioData = filePayload;
                        audioFileName = finalFilename;

                        $(".ringtone_file").empty();

                        var html = '<div class="col-md-3">\n' +
                            '<div class="audio-inner">\n' +
                            '  <audio controls>\n' +
                            '    <source src="' + audioData + '" type="audio/mp3">\n' +
                            '    Your browser does not support the audio element.\n' +
                            '  </audio>\n' +
                            '</div>\n' +
                            '</div>';

                        jQuery(".ringtone_file").append(html);
                        $("#ringtone_file").val('');
                    };
                })(f);

                reader.readAsDataURL(f);
            };

            audio.src = URL.createObjectURL(f);

        }
        async function storeRingtone() {
            var newAudioURL = audioData;
            try {
                if (audioData && audioData !== oldAudioData && audioData.startsWith('data:audio')) {
                    // Upload audio file to Laravel server
                    var response = await $.ajax({
                        url: '/upload-audio',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            audio: audioData,
                            filename: audioFileName
                        }
                    });
                    if (response.success) {
                        newAudioURL = response.url;
                        audioData = response.url;
                        console.log("Audio available at:", newAudioURL);
                    }
                }
            } catch (error) {
                console.error("Error uploading audio:", error);
            }

            return newAudioURL;
        }

        function base64ToBlob(base64, contentType) {
            var byteCharacters = atob(base64); // Remove Data URL header
            var byteNumbers = new Array(byteCharacters.length);
            for (var i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            var byteArray = new Uint8Array(byteNumbers);
            return new Blob([byteArray], {
                type: contentType
            });
        }
    </script>
    <script>
        // First, define a global callback function
        function initMap() {
            try {
                // Verify if Google Maps is loaded
                if (!google || !google.maps) {
                    throw new Error('Google Maps API not loaded');
                }

                const mapOptions = {
                    zoom: 10,
                    center: { lat: YOUR_DEFAULT_LAT, lng: YOUR_DEFAULT_LNG },
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    gestureHandling: 'greedy'
                };

                const map = new google.maps.Map(document.getElementById('map'), mapOptions);

                // Add error handling listener
                google.maps.event.addListenerOnce(map, 'error', function(e) {
                    console.error('Map loading error:', e);
                    handleMapError();
                });

            } catch (error) {
                console.error('Map initialization error:', error);
                handleMapError();
            }
        }

        function handleMapError() {
            // Show user-friendly error message
            document.getElementById('map').innerHTML =
                '<div class="alert alert-danger">Unable to load map. Please try again later.</div>';
        }

        // Handle authentication failures
        window.gm_authFailure = function() {
            console.error('Google Maps authentication failed');
            handleMapError();
        };
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap&loading=async" async defer></script>
@endsection
