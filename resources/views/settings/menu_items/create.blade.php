@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.menu_items')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('setting.banners') !!}">{{trans('lang.menu_items')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.menu_items_create')}}</li>
            </ol>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top"></div>
        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.menu_items')}}</legend>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.title')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control title">
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.set_order')}}</label>
                        <div class="col-7">
                            <input type="number" class="form-control set_order" min="0">
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check width-100">
                            <input type="checkbox" id="is_publish">
                            <label class="col-3 control-label" for="is_publish">{{trans('lang.is_publish')}}</label>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.photo')}}</label>
                        <input type="file" onChange="handleFileSelect(event)" class="col-7">
                        <div id="uploding_image"></div>
                        <div class="placeholder_img_thumb user_image"></div>
                    </div>
                    <div class="form-group row width-50" id="banner_position">
                        <label class="col-3 control-label ">{{trans('lang.banner_position')}}</label>
                        <div class="col-7">
                            <select name="position" id="position" class="form-control">
                                <option value="top">{{trans('lang.top')}}</option>
                                <option value="middle">{{trans('lang.middle')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">Zone</label>
                        <div class="col-7">
                            <select name="zoneId" id="zoneId" class="form-control">
                                <option value="">Select Zone</option>
                            </select>
                            <div class="form-text text-muted">Select the zone for this banner item.</div>
                        </div>
                    </div>
                    <div class="form-group row width-100 radio-form-row d-flex" id="redirect_type_div">
                        <div class="radio-form col-md-2">
                            <input type="radio" class="redirect_type" value="store" name="redirect_type" id="store">
                            <label class="custom-control-label">{{trans('lang.store')}}</label>
                        </div>
                        <div class="radio-form col-md-2">
                            <input type="radio" class="redirect_type" value="product" name="redirect_type" id="product">
                            <label class="custom-control-label">{{trans('lang.product')}}</label>
                        </div>
                        <div class="radio-form col-md-4">
                            <input type="radio" class="redirect_type" value="external_link" name="redirect_type" id="external">
                            <label class="custom-control-label">{{trans('lang.external_link')}}</label>
                        </div>
                    </div>
                    <div class="form-group row width-50" id="vendor_div" style="display: none;">
                        <label class="col-3 control-label ">{{trans('lang.store')}}</label>
                        <div class="col-7">
                            <select name="storeId" id="storeId" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-50" id="product_div" style="display: none;">
                        <label class="col-3 control-label ">{{trans('lang.product')}}</label>
                        <div class="col-7">
                            <select name="productId" id="productId" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-100" id="external_link_div" style="display: none;">
                        <label class="col-3 control-label">{{trans('lang.external_link')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control" id="external_link">
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="form-group col-12 text-center">
        <?php
            $__perms = json_decode(@session('user_permissions'), true) ?: [];
            if (in_array('banners.create', $__perms) || in_array('setting.banners.create', $__perms)) { ?>
        <button type="button" class="btn btn-primary  save-setting-btn"><i class="fa fa-save"></i> {{trans('lang.save')}}</button>
        <?php } ?>
        <a href="{!! route('setting.banners') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function toggleRedirectUI(){
        var redirect_type = $(".redirect_type:checked").val();
        if (redirect_type == "store") {
            $('#vendor_div').show(); $('#product_div').hide(); $('#external_link_div').hide();
        } else if (redirect_type == "product") {
            $('#vendor_div').hide(); $('#product_div').show(); $('#external_link_div').hide();
        } else {
            $('#vendor_div').hide(); $('#product_div').hide(); $('#external_link_div').show();
        }
    }
    $(document).on('change', "input[name='redirect_type']:radio", toggleRedirectUI);
    $(document).ready(function(){
        toggleRedirectUI();
        // Load zones from SQL API
        $('#zoneId').html('<option value="">Select Zone</option>');
        $.get('{{ url('/zone/data') }}', function(resp){
            if(resp && resp.data){
                resp.data.forEach(function(z){
                    $('#zoneId').append($('<option></option>').attr('value', z.id).text(z.name));
                });
            }
        }).fail(function(xhr){
            console.error('Failed to load zones', xhr.status, xhr.statusText);
        });
    });

    $(".save-setting-btn").click(function() {
        $(".error_top").hide().html('');
        var title = $(".title").val();
        if(!title){ $(".error_top").show().html('<p>{{trans('lang.title_error')}}'); window.scrollTo(0,0); return; }

        var fd = new FormData();
        fd.append('title', title);
        fd.append('set_order', $('.set_order').val() || 0);
        fd.append('is_publish', $('#is_publish').is(':checked') ? 1 : 0);
        fd.append('position', $('#position').val() || 'top');
        fd.append('zoneId', $('#zoneId').val() || '');
        fd.append('zoneTitle', $('#zoneId option:selected').text() || '');
        fd.append('redirect_type', $(".redirect_type:checked").val() || 'external_link');
        var redirect_id = '';
        if($("#store").is(':checked')) redirect_id = $('#storeId').val()||'';
        else if($("#product").is(':checked')) redirect_id = $('#productId').val()||'';
        else if($("#external").is(':checked')) redirect_id = $('#external_link').val()||'';
        fd.append('redirect_id', redirect_id);
        var fileInput = $("input[type='file']")[0];
        if (fileInput && fileInput.files && fileInput.files[0]) { fd.append('photo', fileInput.files[0]); }
        $.ajax({ url: '{{ route('menu-items.store') }}', method: 'POST', data: fd, processData: false, contentType: false, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .done(function(){ window.location.href = '{{ route('setting.banners') }}'; })
            .fail(function(xhr){ $(".error_top").show().html('<p>Failed ('+xhr.status+'): '+xhr.responseText+'</p>'); window.scrollTo(0,0); });
    });
</script>
@endsection
