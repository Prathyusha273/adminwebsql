@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.footer_template')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.footer_template')}}</li>
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
                    <legend>{{trans('lang.footer_template')}}</legend>
                    <div class="form-group width-100">
                        <textarea class="form-control col-7" name="footerTemplate" id="footerTemplate"></textarea>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="form-group col-12 text-center btm-btn">
        <button type="button" class="btn btn-primary  edit-setting-btn"><i
                    class="fa fa-save"></i> {{ trans('lang.save')}}
        </button>
        <a href="{!! route('dashboard') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}
        </a>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function () {
    jQuery("#data-table_processing").show();

    // Initialize Summernote editor first
    $('#footerTemplate').summernote({
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

    // Fetch Footer Template from SQL database
    $.ajax({
        url: '{{ route("settings.get", "footerTemplate") }}',
        type: 'GET',
        success: function(response) {
            if(response.success && response.data && response.data.footerTemplate) {
                $('#footerTemplate').summernote("code", response.data.footerTemplate);
            }
            jQuery("#data-table_processing").hide();
        },
        error: function() {
            jQuery("#data-table_processing").hide();
            console.error('Error loading footer template');
        }
    });

    // Save Footer Template
    var isSaving = false;
    $(".edit-setting-btn").click(function () {
        if (isSaving) {
            return false; // Prevent double-click
        }

        var footerTemplate = $('#footerTemplate').summernote('code');
        $(".error_top").hide();
        $(".error_top").html("");

        if (footerTemplate == '' || footerTemplate == '<p><br></p>') {
            $(".error_top").show();
            $(".error_top").append("<p>{{trans('lang.footer_template_error')}}</p>");
            window.scrollTo(0, 0);
        } else {
            isSaving = true;
            jQuery("#data-table_processing").show();
            $(this).prop('disabled', true);

            $.ajax({
                url: '{{ route("settings.update", "footerTemplate") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    footerTemplate: footerTemplate
                },
                success: function(response) {
                    if(response.success) {
                        // Log the activity (optional - don't block on failure)
                        if (typeof logActivity === 'function') {
                            try {
                                logActivity('footer_template', 'updated', 'Updated Footer Template content')
                                    .catch(function(err) {
                                        console.warn('Activity logging failed:', err);
                                    })
                                    .finally(function() {
                                        window.location.href = '{{ route("footerTemplate")}}';
                                    });
                            } catch(e) {
                                console.warn('Activity logging error:', e);
                                window.location.href = '{{ route("footerTemplate")}}';
                            }
                        } else {
                            window.location.href = '{{ route("footerTemplate")}}';
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
                    alert('Error updating footer template');
                }
            });
        }
    });
});
</script>
@endsection
