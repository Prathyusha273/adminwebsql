@extends('layouts.app')
@section('content')
<div class="page-wrapper">
	<div class="row page-titles">
		<div class="col-md-5 align-self-center">
			<h3 class="text-themecolor">{{trans('lang.privacy_policy')}}</h3>
		</div>
		<div class="col-md-7 align-self-center">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
				<li class="breadcrumb-item active">{{trans('lang.privacy_policy')}}</li>
			</ol>
		</div>
		<div>
	</div>
</div>
<div class="card-body">
				<div class="error_top"></div>
				<div class="terms-cond restaurant_payout_create row">
          		<div class="restaurant_payout_create-inner">
          			<fieldset>
              		<legend>{{trans('lang.privacy_policy')}}</legend>
                <div class="form-group width-100">
                  <textarea class="form-control col-7" name="privacy_policy" id="privacy_policy"></textarea>
                </div>
				</fieldset>
		</div>
	</div>
</div>
		<div class="form-group col-12 text-center btm-btn" >
			<button type="button" class="btn btn-primary  edit-setting-btn" ><i class="fa fa-save"></i> {{ trans('lang.save')}}</button>
			<a href="{!! route('privacyPolicy') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
		</div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function () {
	jQuery("#data-table_processing").show();

	// Initialize Summernote editor first
	$('#privacy_policy').summernote({
		height: 400,
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['forecolor', ['forecolor']],
			['backcolor', ['backcolor']],
			['para', ['ul', 'ol', 'paragraph']],
			['height', ['height']]
		]
    });

	// Fetch Privacy Policy from SQL database
	$.ajax({
		url: '{{ route("settings.get", "privacyPolicy") }}',
		type: 'GET',
		success: function(response) {
			if(response.success && response.data && response.data.privacy_policy) {
				$('#privacy_policy').summernote("code", response.data.privacy_policy);
			}
			jQuery("#data-table_processing").hide();
		},
		error: function() {
			jQuery("#data-table_processing").hide();
			console.error('Error loading privacy policy');
		}
	});

	// Save Privacy Policy
	var isSaving = false;
	$(".edit-setting-btn").click(function(){
		if (isSaving) {
			return false; // Prevent double-click
		}

		var privacy_policy = $('#privacy_policy').summernote('code');

	    if(privacy_policy == '' || privacy_policy == '<p><br></p>'){
	        $(".error_top").show();
	        $(".error_top").html("");
	        $(".error_top").append("<p>{{trans('lang.user_firstname_error')}}</p>");
	        window.scrollTo(0, 0);
	  	} else {
			isSaving = true;
			jQuery("#data-table_processing").show();
			$(this).prop('disabled', true);

			$.ajax({
				url: '{{ route("settings.update", "privacyPolicy") }}',
				type: 'POST',
				data: {
					_token: '{{ csrf_token() }}',
					privacy_policy: privacy_policy
				},
				success: function(response) {
					if(response.success) {
						// Log the activity (optional - don't block on failure)
						if (typeof logActivity === 'function') {
							try {
								logActivity('privacy_policy', 'updated', 'Updated Privacy Policy content')
									.catch(function(err) {
										console.warn('Activity logging failed:', err);
									})
									.finally(function() {
										window.location.href = '{{ route("privacyPolicy")}}';
									});
							} catch(e) {
								console.warn('Activity logging error:', e);
								window.location.href = '{{ route("privacyPolicy")}}';
							}
						} else {
							window.location.href = '{{ route("privacyPolicy")}}';
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
					alert('Error updating privacy policy');
				}
			});
		}
	});
});
</script>
@endsection
