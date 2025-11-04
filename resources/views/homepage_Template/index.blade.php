@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.homepageTemplate')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a>
                </li>
                <li class="breadcrumb-item active">
                    {{trans('lang.homepageTemplate')}}
                </li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top"></div>
        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
                <fieldset>
                    <legend>
                        {{trans('lang.homepageTemplate')}}
                    </legend>
                    <div class="form-group width-100">
                        <textarea class="form-control col-7" name="homepageTemplate" id="homepageTemplate"></textarea>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="form-group col-12 text-center btm-btn">
        <button type="button" class="btn btn-primary  edit-setting-btn">
            <i class="fa fa-save"></i> {{ trans('lang.save')}}
        </button>
        <a href="{!! route('dashboard') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function () {
        jQuery("#data-table_processing").show();

        // Initialize Summernote editor first
        $('#homepageTemplate').summernote({
            height: 400,
            width: 1024,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['forecolor', ['forecolor']],
                ['backcolor', ['backcolor']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ]
        });

        // Fetch Homepage Template from SQL database
        $.ajax({
            url: '{{ route("settings.get", "homepageTemplate") }}',
            type: 'GET',
            success: function(response) {
                if(response.success && response.data && response.data.homepageTemplate) {
                    $('#homepageTemplate').summernote("code", response.data.homepageTemplate);
                }
                jQuery("#data-table_processing").hide();
            },
            error: function() {
                jQuery("#data-table_processing").hide();
                console.error('Error loading homepage template');
            }
        });

        // Save Homepage Template
        var isSaving = false;
        $(".edit-setting-btn").click(function () {
            if (isSaving) {
                return false; // Prevent double-click
            }

            var homepageTemplate = $('#homepageTemplate').summernote('code');
            $(".error_top").hide();
            $(".error_top").html("");

            if (homepageTemplate == '' || homepageTemplate == '<p><br></p>') {
                $(".error_top").show();
                $(".error_top").append("<p>{{trans('lang.homepageTemplate_error')}}</p>");
                window.scrollTo(0, 0);
            } else {
                isSaving = true;
                jQuery("#data-table_processing").show();
                $(this).prop('disabled', true);

                $.ajax({
                    url: '{{ route("settings.update", "homepageTemplate") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        homepageTemplate: homepageTemplate
                    },
                    success: function(response) {
                        if(response.success) {
                            // Log the activity (optional - don't block on failure)
                            if (typeof logActivity === 'function') {
                                try {
                                    logActivity('landing_page_template', 'updated', 'Updated Landing Page Template content')
                                        .catch(function(err) {
                                            console.warn('Activity logging failed:', err);
                                        })
                                        .finally(function() {
                                            window.location.href = '{{ route("homepageTemplate")}}';
                                        });
                                } catch(e) {
                                    console.warn('Activity logging error:', e);
                                    window.location.href = '{{ route("homepageTemplate")}}';
                                }
                            } else {
                                window.location.href = '{{ route("homepageTemplate")}}';
                            }
                        } else {
                            jQuery("#data-table_processing").hide();
                            $(".edit-setting-btn").prop('disabled', false);
                            isSaving = false;
                            alert('Error: ' + (response.message || 'Failed to update'));
                        }
                    },
                    error: function() {
                        jQuery("#data-table_processing").hide();
                        $(".edit-setting-btn").prop('disabled', false);
                        isSaving = false;
                        alert('Error updating homepage template');
                    }
                });
            }
        });
    });
</script>
@endsection
