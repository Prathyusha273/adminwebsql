@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.add_brand')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('brands') !!}">{{trans('lang.brands')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('lang.add_brand')}}</li>
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
                                        <legend>{{trans('lang.add_brand')}}</legend>
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
                                            <input type="checkbox" class="brand_status" id="brand_status" checked>
                                            <label class="col-3 control-label" for="brand_status">{{trans('lang.status')}}</label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-12 text-center btm-btn">
                        <?php if (in_array('brands.create', json_decode(@session('user_permissions'), true))) { ?>
                        <button type="button" class="btn btn-primary save-form-btn"><i class="fa fa-save"></i> {{trans('lang.save')}}</button>
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
    var database = firebase.firestore();
    var logo_url = "";
    var storageRef = firebase.storage().ref('images');
    var storage = firebase.storage();
    var placeholderImage = '{{ asset('images/placeholder.png') }}';

    $(document).ready(function () {
        jQuery("#data-table_processing").show();

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

        jQuery("#data-table_processing").hide();

        $(".save-form-btn").click(async function () {
            var name = $(".brand_name").val();
            var slug = $(".brand_slug").val();
            var description = $("#brand_description").val();
            var status = $(".brand_status").is(":checked");

            // Enhanced validation
            if (name == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_brand_name_error')}}</p>");
                window.scrollTo(0, 0);
                return;
            } else if (description == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_brand_description_error')}}</p>");
                window.scrollTo(0, 0);
                return;
            } else if (name.length > 255) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>Brand name must be 255 characters or less.</p>");
                window.scrollTo(0, 0);
                return;
            } else {
                $(".error_top").hide();

                // Check for duplicate brand names
                jQuery("#data-table_processing").show();
                const existingBrands = await database.collection('brands')
                    .where('name', '==', name.trim())
                    .get();

                if (!existingBrands.empty) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>A brand with this name already exists. Please choose a different name.</p>");
                    window.scrollTo(0, 0);
                    return;
                }

                var id = database.collection("tmp").doc().id;

                await storeLogoData().then(async (logo) => {
                    if (logo) {
                        logo_url = logo;
                    }

                    var brandData = {
                        'name': name,
                        'slug': slug || name.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim('-'),
                        'description': description,
                        'logo_url': logo_url,
                        'status': status,
                        'id': id,
                        'created_at': firebase.firestore.FieldValue.serverTimestamp(),
                        'updated_at': firebase.firestore.FieldValue.serverTimestamp()
                    };

                    database.collection('brands').doc(id).set(brandData)
                        .then(async function (result) {
                            console.log('✅ Brand saved successfully, now logging activity...');
                            try {
                                if (typeof logActivity === 'function') {
                                    console.log('🔍 Calling logActivity for brand creation...');
                                    await logActivity('brands', 'created', 'Created new brand: ' + name);
                                    console.log('✅ Activity logging completed successfully');
                                } else {
                                    console.error('❌ logActivity function is not available');
                                }
                            } catch (error) {
                                console.error('❌ Error calling logActivity:', error);
                            }
                            window.location.href = '{{ route("brands") }}';
                        });
                }).catch(err => {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + err + "</p>");
                    window.scrollTo(0, 0);
                });
            }
        })
    })

    async function storeLogoData() {
        var logo = '';
        if (logo_url && logo_url != '') {
            try {
                // Upload base64 image to Firebase Storage
                var filename = 'brand_logo_' + Date.now() + '.jpg';
                var storageRef = firebase.storage().ref('images/brands/' + filename);

                // Convert base64 to blob
                var base64Data = logo_url.replace(/^data:image\/[a-z]+;base64,/, "");
                var uploadTask = await storageRef.putString(base64Data, 'base64', {
                    contentType: 'image/jpeg'
                });

                // Get download URL
                logo = await uploadTask.ref.getDownloadURL();
                console.log('✅ Logo uploaded to Firebase Storage:', logo);
            } catch (error) {
                console.error('❌ Error uploading logo:', error);
                logo = logo_url; // Fallback to base64
            }
        }
        return logo;
    }

    $("#brand_logo").resizeImg({
        callback: function (base64str) {
            var val = $('#brand_logo').val().toLowerCase();
            var ext = val.split('.')[1];
            var filename = $('#brand_logo').val().replace(/C:\\fakepath\\/i, '')
            var timestamp = Number(new Date());
            var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

            var logo_html = '<span class="image-item" id="logo_1"><span class="remove-btn" data-id="1" data-img="' + base64str + '"><i class="fa fa-remove"></i></span><img class="rounded" width="50px" id="" height="auto" src="' + base64str + '"></span>'
            $(".brand_logo_preview").append(logo_html);
            logo_url = base64str;
            $("#brand_logo").val('');
        }
    });

    $(document).on("click", ".remove-btn", function () {
        var id = $(this).attr('data-id');
        var logo_remove = $(this).attr('data-img');
        $("#logo_" + id).remove();
        logo_url = '';
    });
</script>
@endsection
