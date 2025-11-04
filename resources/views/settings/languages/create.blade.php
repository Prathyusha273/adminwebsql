@extends('layouts.app')
@section('content')
<div class="page-wrapper">
	<div class="row page-titles">
		<div class="col-md-5 align-self-center">
			<h3 class="text-themecolor">{{trans('lang.add_language')}}</h3>
		</div>
		<div class="col-md-7 align-self-center">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
				<li class="breadcrumb-item"><a href= "{!! route('settings.app.languages') !!}" >{{trans('lang.languages')}}</a></li>
				<li class="breadcrumb-item active">{{trans('lang.add_language')}}</li>
			</ol>
		</div>
			<div class="card-body">
				<div class="error_top"></div>
				<div class="row restaurant_payout_create">
          		<div class="restaurant_payout_create-inner">
          			<fieldset>
						<div class="form-group row width-50">
							<label class="col-3 control-label">{{trans('lang.name')}}</label>
							<div class="col-7">
								<input type="text" class="form-control title" id="title">
							</div>
						</div>
						<div class="form-group row width-50">
							<label class="col-3 control-label">{{trans('lang.slug')}}</label>
							<div class="col-7">
								<input type="text" class="form-control slug" id="slug">
								<div class="form-text text-muted">
									{{ trans("lang.slug_help") }}
								</div>
							</div>
						</div>
						<div class="form-group row width-50">
							<label class="col-3 control-label">{{trans('lang.image')}}<span
										class="required-field"></span></label>
							<div class="col-7">
								<input type="file" onChange="handleFileSelect(event)" class="" id="flagImage">
								<div class="form-text text-muted">{{trans('lang.language_flag_help')}}</div>
							</div>
							<div class="placeholder_img_thumb flag_image"></div>
							<div id="uploding_image"></div>
						</div>
						<div class="form-group row width-50">
							<div class="form-check">
								<input type="checkbox" class="is_active" id="is_active">
								<label class="col-3 control-label" for="is_active">{{trans('lang.active')}}</label>
							</div>
						</div>
						<div class="form-group row width-50">
							<div class="form-check">
								<input type="checkbox" class="is_rtl" id="is_rtl">
								<label class="col-3 control-label" for="is_rtl">{{trans('lang.is_rtl')}}</label>
							</div>
						</div>
					</fieldset>
			</div>
		</div>
	</div>
		<div class="form-group col-12 text-center btm-btn" >
			<button type="button" class="btn btn-primary  save-setting-btn" ><i class="fa fa-save"></i> {{ trans('lang.save')}}</button>
			<a href="{!! url('settings/app/languages') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
		</div>
</div>
</div>
@endsection
@section('scripts')
<script>
// SQL mode - no Firebase
var languages=[];
var photo = "";
var fileName = "";
var flagImageFile = '';
$(document).ready(function(){
	// Load languages from SQL
	$.get("{{ route('api.languages.settings') }}", function(response) {
		if (response.success && response.list) {
			if(response.list.length){
				languages = response.list;
			}
		}
	});
});
$(".save-setting-btn").click(function(){
	var title = $("#title").val();
	var slug = $("#slug").val();
	var active = $(".is_active").is(":checked");
	var is_rtl = $(".is_rtl").is(":checked");
	if(title == ''){
		$(".error_top").show();
		$(".error_top").html("");
		$(".error_top").append("<p>{{trans('lang.name_error')}}</p>");
		window.scrollTo(0, 0);
	}else if(slug == ''){
		$(".error_top").show();
		$(".error_top").html("");
		$(".error_top").append("<p>{{trans('lang.slug_error')}}</p>");
		window.scrollTo(0, 0);
	}else if(fileName == ''){
		$(".error_top").show();
		$(".error_top").html("");
		$(".error_top").append("<p>{{trans('lang.language_flag_help')}}</p>");
		window.scrollTo(0, 0);
	}else{
		jQuery("#data-table_processing").show();
		storeImageData().then(IMG => {
			if(languages.length){
				languages.push({'title':title,'slug':slug,'isActive':active,'is_rtl':is_rtl,'image': IMG});
			}else{
				languages=[{'title':title,'slug':slug,'isActive':active,'is_rtl':is_rtl,'image': IMG}];
			}
			$.ajax({
				url: "{{ route('api.languages.update') }}",
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					list: languages
				},
				success: function(result) {
					jQuery("#data-table_processing").hide();
					window.location.href = '{{ route("settings.app.languages") }}';
				},
				error: function(xhr) {
					jQuery("#data-table_processing").hide();
					$(".error_top").show();
					$(".error_top").html("");
					$(".error_top").append("<p>Error saving language</p>");
					window.scrollTo(0, 0);
				}
			});
		}).catch(err => {
			jQuery("#overlay").hide();
			$(".error_top").show();
			$(".error_top").html("");
			$(".error_top").append("<p>" + err + "</p>");
			window.scrollTo(0, 0);
		});
	}
})
$(document).on('click', '.remove-btn', function () {
	$(".image-item").remove();
	$('#flagImage').val('');
	fileName = '';
});
function handleFileSelect(evt) {
	var f = evt.target.files[0];
	var reader = new FileReader();
	reader.onload = (function (theFile) {
		return function (e) {
			var filePayload = e.target.result;
			var val = f.name;
			var ext = val.split('.')[1];
			var docName = val.split('fakepath')[1];
			var filename = (f.name).replace(/C:\\fakepath\\/i, '')
			var timestamp = Number(new Date());
			var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
			photo = filePayload;
			fileName = filename;
			$(".flag_image").empty();
			$(".flag_image").append('<span class="image-item" ><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + filePayload + '" alt="image"></span>');
		};
	})(f);
	reader.readAsDataURL(f);
}
async function storeImageData() {
	var newPhoto = '';
	try {
		if (photo != flagImageFile && photo) {
			// Upload to Laravel server
			var response = await $.ajax({
				url: '/upload-image',
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					image: photo,
					filename: fileName
				}
			});
			if (response.success) {
				newPhoto = response.url;
				photo = response.url;
			}
		} else {
			newPhoto = photo;
		}
	} catch (error) {
		console.error("Image upload error:", error);
	}
	return newPhoto;
}
</script>
@endsection
