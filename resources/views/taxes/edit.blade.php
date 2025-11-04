@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.tax')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('tax') !!}">{{trans('lang.tax_plural')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.tax_edit')}}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card pb-4">
                <div class="card-body">
                    <div class="row daes-top-sec mb-3">
                    </div>
                    <div class="error_top"></div>
                    <div class="row restaurant_payout_create">
                        <div class="restaurant_payout_create-inner">
                            <fieldset>
                                <legend>{{trans('lang.tax_edit')}}</legend>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.tax_title')}}<span
                                                class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="text" class="form-control tax_title">
                                        <div class="form-text text-muted">
                                            {{ trans("lang.tax_title_help") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.country')}}<span
                                                class="required-field"></span></label>
                                    <div class="col-7">
                                        <select name="country" id="country" class="form-control tax_country">
                                            @foreach($countries_data as $country)
                                                <option
                                                        value="{{$country->countryName}}">{{$country->countryName}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted">
                                            {{ trans("lang.tax_country_help") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.tax_type')}}<span
                                                class="required-field"></span></label>
                                    <div class="col-7">
                                        <select class="form-control tax_type">
                                            <option value="fix">
                                                {{trans('lang.fix')}}
                                            </option>
                                            <option value="percentage">
                                                {{trans('lang.percentage')}}
                                            </option>
                                        </select>
                                        <div class="form-text text-muted">
                                            {{ trans("lang.tax_type_help") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.tax_amount')}}<span
                                                class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="number" class="form-control tax_amount" min="0">
                                        <div class="form-text text-muted w-50">
                                            {{ trans("lang.tax_amount_help") }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row width-100">
                                    <div class="form-check">
                                        <input type="checkbox" class="tax_active" id="tax_active">
                                        <label class="col-3 control-label"
                                               for="tax_active">{{trans('lang.enable')}}</label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i> {{
                trans('lang.save')}}
                    </button>
                    <a href="{!! route('tax') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                trans('lang.cancel')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var id = "<?php echo $id;?>";

        $(document).ready(function () {
            $('.tax_menu').addClass('active');
            jQuery("#data-table_processing").show();

            // Fetch tax data from MySQL
            $.ajax({
                url: '{{ url('tax/get') }}/' + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $(".tax_title").val(data.title);
                        $(".tax_type").val(data.type);
                        $(".tax_country").val(data.country);
                        $('.tax_amount').val(data.tax);
                        if (data.enable) {
                            $('.tax_active').prop('checked', true);
                        }
                    } else {
                        alert('Error loading tax data: ' + response.message);
                    }
                    jQuery("#data-table_processing").hide();
                },
                error: function(xhr, status, error) {
                    alert('Error loading tax data: ' + error);
                    jQuery("#data-table_processing").hide();
                }
            });

            // Save changes to MySQL
            $(".edit-setting-btn").click(function () {
                var title = $(".tax_title").val();
                var country = $(".tax_country").val();
                var type = $(".tax_type :selected").val();
                var tax = $(".tax_amount").val();
                var enable = $(".tax_active").is(':checked') ? 1 : 0;

                // Validation
                if (title == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.tax_title_error')}}</p>");
                    window.scrollTo(0, 0);
                    return;
                } else if (tax == '' || tax <= 0) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.tax_amount_error')}}</p>");
                    window.scrollTo(0, 0);
                    return;
                }

                // Update in MySQL
                jQuery("#overlay").show();
                $.ajax({
                    url: '{{ url('tax') }}/' + id + '/update',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: title,
                        country: country,
                        tax: tax,
                        type: type,
                        enable: enable
                    },
                    success: function(response) {
                        jQuery("#overlay").hide();
                        if (response.success) {
                            // Log the activity (don't wait for it)
                            if (typeof logActivity === 'function') {
                                logActivity('tax_settings', 'updated', 'Updated tax: ' + title + ' (' + country + ') - Type: ' + type + ', Amount: ' + tax + ', Enabled: ' + (enable ? 'Yes' : 'No')).catch(function(e) {
                                    console.log('Activity logging failed:', e);
                                });
                            }
                            // Redirect immediately
                            alert('Tax updated successfully!');
                            window.location.href = '{{ route("tax") }}';
                        } else {
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>" + response.message + "</p>");
                        }
                    },
                    error: function(xhr, status, error) {
                        jQuery("#overlay").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>Error: " + (xhr.responseJSON?.message || error) + "</p>");
                    }
                });
            });
        });
    </script>
@endsection
