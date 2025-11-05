@extends('layouts.app')
@section('content')
<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array)$countries;
$newcountries = array();
$newcountriesjs = array();
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>
<div class="page-wrapper">
	<div class="row page-titles">
		<div class="col-md-5 align-self-center">
			<h3 class="text-themecolor">{{trans('lang.user_plural')}}</h3>
		</div>
		<div class="col-md-7 align-self-center">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
				<li class="breadcrumb-item"><a href="{!! route('users') !!}">{{trans('lang.user_plural')}}</a></li>
				<li class="breadcrumb-item active">{{trans('lang.user_create')}}</li>
			</ol>
		</div>
		<div>
			<div class="card-body">
				<div class="error_top"></div>
				<div class="row restaurant_payout_create">
					<div class="restaurant_payout_create-inner">
						<fieldset>
							<legend>{{trans('lang.user_details')}}</legend>
							<div class="form-group row width-50">
								<label class="col-3 control-label">{{trans('lang.first_name')}}</label>
								<div class="col-7">
									<input type="text" class="form-control user_first_name" id="firstname"  onkeypress="return chkAlphabets(event,'error')">
									<div id="error" class="err"></div>
									<div class="form-text text-muted">
										{{ trans("lang.user_first_name_help") }}
									</div>
								</div>
							</div>
							<div class="form-group row width-50">
								<label class="col-3 control-label">{{trans('lang.last_name')}}</label>
								<div class="col-7">
									<input type="text" class="form-control user_last_name" onkeypress="return chkAlphabets(event,'error1')">
									<div id="error1" class="err"></div>
									<div class="form-text text-muted">
										{{ trans("lang.user_last_name_help") }}
									</div>
								</div>
							</div>
							<div class="form-group row width-50">
								<label class="col-3 control-label">{{trans('lang.email')}}</label>
								<div class="col-7">
									<input type="text" class="form-control user_email">
									<div class="form-text text-muted">
										{{ trans("lang.user_email_help") }}
									</div>
								</div>
							</div>
							<div class="form-group row width-50">
								<label class="col-3 control-label">{{trans('lang.password')}}</label>
								<div class="col-7">
									<input type="password" class="form-control user_password">
									<div class="form-text text-muted">
										{{ trans("lang.user_password_help") }}
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-md-3 control-label">{{trans('lang.user_phone')}}</label>
									<div class="col-md-6">
										<div class="phone-box position-relative" id="phone-box">
											<select name="country" id="country_selector">
												<?php foreach ($newcountries as $keycy => $valuecy) { ?>
												<?php $selected = ""; ?>
												<option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
														value="<?php echo $keycy; ?>">
													+<?php echo $valuecy->phoneCode; ?> {{$valuecy->countryName}}</option>
												<?php } ?>
											</select>
											<input type="text" class="form-control user_phone"  onkeypress="return chkAlphabets2(event,'error2')">
											<div id="error2" class="err"></div>
										</div>
									</div>
									<div class="form-text text-muted w-50">
										{{ trans("lang.user_phone_help") }}
									</div>
							</div>
							<div class="form-group row width-100">
								<label class="col-3 control-label">{{trans('lang.restaurant_image')}}</label>
								<input type="file" onChange="handleFileSelect(event)" class="col-7">
								<div class="placeholder_img_thumb user_image"></div>
								<div id="uploding_image"></div>
							</div>
						</fieldset>
						<fieldset>
							<legend>{{trans('user')}} {{trans('lang.active_deactive')}}</legend>
							<div class="form-group row">
								<div class="form-group row width-50">
									<div class="form-check width-100">
										<input type="checkbox" class="user_active" id="user_active">
										<label class="col-3 control-label"
											for="user_active">{{trans('lang.active')}}</label>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
			<div class="form-group col-12 text-center btm-btn">
				<button type="button" class="btn btn-primary  save-form-btn"><i class="fa fa-save"></i> {{
					trans('lang.save')}}</button>
				<a href="{!! route('users') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
					trans('lang.cancel')}}</a>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
		jQuery("#country_selector").select2({
			templateResult: formatState,
			templateSelection: formatState2,
			placeholder: "Select Country",
			allowClear: true
		});
	});
    var apiBase = '{{ url('/api') }}';
    var photo = "";
	var fileName='';
	$(".save-form-btn").click(function () {
		var userFirstName = $(".user_first_name").val();
		var userLastName = $(".user_last_name").val();
		var email = $(".user_email").val();
		var password = $(".user_password").val();
		var country_code = $("#country_selector").val();
		var userPhone = $(".user_phone").val();
		var active = $(".user_active").is(":checked");
		var role = $("#user_role option:selected").val();
		var user_name = userFirstName + " " + userLastName;
		var vendorRestaurantSelect = $("#vendor_restaurant_select option:selected").val();
		var id = "<?php echo uniqid(); ?>";
		var name = userFirstName + " " + userLastName;
		if (userFirstName == '') {
			$(".error_top").show();
			$(".error_top").html("");
			$(".error_top").append("<p>{{trans('lang.user_firstname_error')}}</p>");
			window.scrollTo(0, 0);
		} else if (userLastName == '') {
			$(".error_top").show();
			$(".error_top").html("");
			$(".error_top").append("<p>{{trans('lang.user_lastname_error')}}</p>");
			window.scrollTo(0, 0);
		}  else if (email == '') {
			$(".error_top").show();
			$(".error_top").html("");
			$(".error_top").append("<p>{{trans('lang.user_email_error')}}</p>");
			window.scrollTo(0, 0);
		} else if (password == '') {
			$(".error_top").show();
			$(".error_top").html("");
			$(".error_top").append("<p>{{trans('lang.user_password_error')}}</p>");
			window.scrollTo(0, 0);
		} else if (userPhone == '') {
			$(".error_top").show();
			$(".error_top").html("");
			$(".error_top").append("<p>{{trans('lang.user_phone_error')}}</p>");
			window.scrollTo(0, 0);
		}
		 else {
            jQuery("#data-table_processing").show();
            storeImageData().then(IMG => {
                $.ajax({
                    url: apiBase + '/app-users',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: {
                        firstName: userFirstName,
                        lastName: userLastName,
                        email: email,
                        password: password,
                        countryCode: country_code,
                        phoneNumber: userPhone,
                        active: active, // Send boolean directly
                        role: 'customer',
                        zoneId: null,
                        photo: photo || '',
                        fileName: fileName || ''
                    }
                }).done(async function(response){
                    console.log('✅ User created successfully:', response);
                    try {
                        if (typeof logActivity === 'function') {
                            await logActivity('users', 'created', 'Created new user: ' + userFirstName + ' ' + userLastName);
                        }
                    } catch (e){}
                    jQuery("#data-table_processing").hide();
                    window.location.href = '{{ route("users")}}';
                }).fail(function(xhr){
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");

                    console.error('❌ Error creating user:', xhr);

                    var msg = 'Failed to create user';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;
                            msg = Object.values(errors).flat().join('<br>');
                        }
                    } else if (xhr.responseText) {
                        msg = 'Server error: ' + xhr.statusText;
                    }

                    $(".error_top").append("<p>" + msg + "</p>");
                    window.scrollTo(0, 0);
                });
            });
		}
	})
    async function storeImageData() {
        var newPhoto = '';
        try {
			if(photo!=""){
            // keep base64 for server to save
			}
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }
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
				$(".user_image").empty();
				$(".user_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
			};
		})(f);
		reader.readAsDataURL(f);
	}
function chkAlphabets(event,msg)
	{
		if(!(event.which>=97 && event.which<=122) && !(event.which>=65 && event.which<=90) )
		{
		document.getElementById(msg).innerHTML="Accept only Alphabets";
		return false;
		}
		else
		{
		document.getElementById(msg).innerHTML="";
		return true;
		}
	}
	function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags";
            var $state = $(
                '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.svg" class="img-flag" /> ' + state.text + '</span>'
            );
            return $state;
        }
        function formatState2(state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags"
            var $state = $(
                '<span><img class="img-flag" /> <span></span></span>'
            );
            $state.find("span").text(state.text);
            $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".svg");
            return $state;
        }
        var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
        var newcountriesjs = JSON.parse(newcountriesjs);
	function chkAlphabets2(event,msg)
	{
		if(!(event.which>=48  && event.which<=57)
		)
		{
		document.getElementById(msg).innerHTML="Accept only Number";
		return false;
		}
		else
		{
		document.getElementById(msg).innerHTML="";
		return true;
		}
	}
	function chkAlphabets3(event,msg)
	{
		if(!((event.which>=48  && event.which<=57) || (event.which>=97 && event.which<=122)))
		{
		document.getElementById(msg).innerHTML="Special characters not accepted ";
		return false;
		}
		else
		{
		document.getElementById(msg).innerHTML="";
		return true;
		}
	}
</script>
@endsection
