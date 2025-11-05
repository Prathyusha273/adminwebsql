@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.edit_brand')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('brands') !!}">{{trans('lang.brands')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.edit_brand')}}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="cat-edite-page max-width-box">
                <div class="card  pb-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li role="presentation" class="nav-item">
                                <a href="#brand_information" aria-controls="description" role="tab" data-toggle="tab"
                                   class="nav-link active">{{trans('lang.brand_information')}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="error_top" style="display:none"></div>
                        <div class="row restaurant_payout_create" role="tabpanel">
                            <div class="restaurant_payout_create-inner tab-content">
                                <div role="tabpanel" class="tab-pane active" id="brand_information">
                                    <fieldset>
                                        <legend>{{trans('lang.edit_brand')}}</legend>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.brand_name')}}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control brand_name" required>
                                                <div class="form-text text-muted">
                                                    {{ trans("lang.brand_name_help") }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.brand_slug')}}</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control brand_slug">
                                                <div class="form-text text-muted">
                                                    {{ trans("lang.slug_help") }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.brand_logo')}}</label>
                                            <div class="col-7">
                                                <input type="file" id="brand_logo">
                                                <div class="placeholder_img_thumb brand_logo_preview"></div>
                                                <div id="uploading_logo"></div>
                                                <div class="form-text text-muted">
                                                    {{ trans("lang.logo_help") }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">{{trans('lang.brand_description')}}</label>
                                            <div class="col-7">
                                                <textarea rows="7" class="form-control brand_description" id="brand_description"></textarea>
                                                <div class="form-text text-muted">
                                                    {{ trans("lang.brand_description_help") }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-check row width-100">
                                            <input type="checkbox" class="brand_status" id="brand_status">
                                            <label class="col-3 control-label" for="brand_status">{{trans('lang.status')}}</label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-12 text-center btm-btn">
                        <?php if (in_array('brands.edit', json_decode(@session('user_permissions'), true))) { ?>
                        <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i> {{trans('lang.save')}}</button>
                        <?php } ?>
                        <a href="{!! route('brands') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var id = "<?php echo $id;?>";
        var placeholderImage = '{{ asset('images/placeholder.png') }}';

        $(document).ready(function () {
            $.get('{{ url('/brands/json') }}/' + id, function(brand){
                if (brand.logo_url) {
                    $(".brand_logo_preview").append('<span class="image-item" id="logo_1"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" width="50px" height="auto" src="' + brand.logo_url + '"></span>');
                } else {
                    $(".brand_logo_preview").append('<span class="image-item" id="logo_1"><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
                }
                $(".brand_name").val(brand.name);
                $(".brand_slug").val(brand.slug);
                $("#brand_description").val(brand.description);
                if (brand.status) { $(".brand_status").prop('checked', true); }
            });

            // Auto-generate slug from name
            $('.brand_name').on('input', function () {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
                $('.brand_slug').val(slug);
            });

            $(".edit-form-btn").click(function(){
                const name = $(".brand_name").val();
                const description = $("#brand_description").val();
                if(!name){
                    $(".error_top").show().html('<p>{{trans('lang.enter_brand_name_error')}}</p>');
                    window.scrollTo(0,0); return;
                }
                if(!description){
                    $(".error_top").show().html('<p>{{trans('lang.enter_brand_description_error')}}</p>');
                    window.scrollTo(0,0); return;
                }
                $(".error_top").hide();
                let fd = new FormData();
                fd.append('name', name);
                fd.append('slug', $(".brand_slug").val());
                fd.append('description', description);
                fd.append('status', $(".brand_status").is(':checked') ? 1 : 0);
                const file = document.getElementById('brand_logo').files[0];
                if(file){ fd.append('logo', file); }
                fd.append('_token','{{ csrf_token() }}');
                fd.append('_method','PUT');
                $.ajax({
                    url: '{{ url('/brands') }}/' + id,
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(){ window.location.href = '{{ route('brands') }}'; },
                    error: function(xhr){
                        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error updating brand';
                        $(".error_top").show().html('<p>'+msg+'</p>');
                        window.scrollTo(0,0);
                    }
                });
            })
        })

    </script>
@endsection
