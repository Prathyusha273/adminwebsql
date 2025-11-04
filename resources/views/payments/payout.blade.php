@extends('layouts.app')
@section('content')
	<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.restaurant_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href= "{!! route('restaurants') !!}" >{{trans('lang.restaurant_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.restaurant_edit')}}</li>
            </ol>
        </div>
    <div>
    <div class="card-body">
      <div class="menu-tab">
      		<ul>
      			<li>
      					<a href="{{route('restaurants.view',$id)}}">Basic</a>
      			</li>
      			<li>
      					<a href="{{route('restaurants.foods',$id)}}">Foods</a>
      			</li>
      			<li>
      					<a href="{{route('restaurants.orders',$id)}}">Orders</a>
      			</li>
      			<li>
      					<a href="{{route('restaurants.promos',$id)}}">Promos</a>
      			</li>
      			<li class="active">
      					<a href="#">Payouts</a>
      			</li>
            <li >
                <a href="{{route('restaurants.booktable',$id)}}">{{trans('lang.dine_in_future')}}</a>
            </li>
      		</ul>
      </div>
      <div class="row daes-top-sec">
      				<div class="col-lg-4 col-md-6">
                  <div class="card">
                      <div class="flex-row">
                          <div class="p-10 bg-info col-md-12 text-center">
                              <h3 class="text-white box m-b-0"><i class="mdi mdi-bank"></i></h3></div>
                          <div class="align-self-center pt-3 col-md-12 text-center">
                              <h3 class="m-b-0 text-info" id="restaurant_count">44</h3>
                              <h5 class="text-muted m-b-0">Total Earning</h5>
                          </div>
                      </div>
                  </div>
            </div>
            <div class="col-lg-4 col-md-6">
                  <div class="card">
                      <div class="flex-row">
                          <div class="p-10 bg-info col-md-12 text-center">
                              <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3></div>
                          <div class="align-self-center pt-3 col-md-12 text-center">
                              <h3 class="m-b-0 text-info" id="restaurant_count">44</h3>
                              <h5 class="text-muted m-b-0">Total Payment</h5>
                          </div>
                      </div>
                  </div>
            </div>
            <div class="col-lg-4 col-md-6">
                  <div class="card">
                      <div class="flex-row">
                          <div class="p-10 bg-info col-md-12 text-center">
                              <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3></div>
                          <div class="align-self-center pt-3 col-md-12 text-center">
                              <h3 class="m-b-0 text-info" id="restaurant_count">44</h3>
                              <h5 class="text-muted m-b-0">Remaining Payment</h5>
                          </div>
                      </div>
                  </div>
            </div>
      </div>
      <div class="row restaurant_payout_create">
        <div class="restaurant_payout_create-inner">
          <fieldset>
             <legend>{{trans('lang.restaurant_details')}}</legend>
              <div class="form-group row width-50">
                <label class="col-3 control-label">{{trans('lang.restaurant_name')}}</label>
               	<div class="col-7">
                	<input type="text" class="form-control restaurant_name">
                	<div class="form-text text-muted">
                  	{{ trans("lang.restaurant_name_help") }}
                	</div>
              	</div>
            	</div>
      			<div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.restaurant_cuisines')}}</label>
        			<div class="col-9">
        				<select id='restaurant_cuisines' class="form-control">
        					<option value="">Select Cuisines</option>
        				</select>
        				<div class="form-text text-muted">
                  			{{ trans("lang.restaurant_cuisines_help") }}
        				</div>
      				</div>
      			</div>
            <div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.restaurant_phone')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_phone">
        				<div class="form-text text-muted">
                  	{{ trans("lang.restaurant_phone_help") }}
        				</div>
      				</div>
      			</div>
            <div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.restaurant_address')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_address">
        				<div class="form-text text-muted">
                  			{{ trans("lang.restaurant_address_help") }}
        				</div>
      				</div>
      			</div>
      			<div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.restaurant_latitude')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_latitude">
        				<div class="form-text text-muted">
                  			{{ trans("lang.restaurant_latitude_help") }}
        				</div>
      				</div>
      			</div>
      			<div class="form-group row">
        			<label class="col-3 control-label">{{trans('lang.restaurant_longitude')}}</label>
        			<div class="col-9">
        				<input type="text" class="form-control restaurant_longitude">
        				<div class="form-text text-muted">
                  			{{ trans("lang.restaurant_longitude_help") }}
        				</div>
      				</div>
      			</div>
          <div class="form-group row">
            <label class="col-3 control-label ">{{trans('lang.restaurant_description')}}</label>
              <div class="col-7">
                <textarea rows="7" class="restaurant_description form-control" id="restaurant_description"></textarea>
              </div>
          </div>
          <div class="form-group row">
            <label class="col-3 control-label">{{trans('lang.restaurant_image')}}</label>
            <div class="col-9">
              <input type="file" onChange="handleFileSelect(event)">
              <div id="uploding_image"></div>
              <div class="form-text text-muted">
                {{ trans("lang.restaurant_image_help") }}
              </div>
            </div>
          </div>
      </fieldset>
      <fieldset>
        <legend>{{trans('lang.admin_area')}}</legend>
        <div class="form-group row">
          <label class="col-3 control-label">{{trans('lang.restaurant_users')}}</label>
          <input type="text" class=" col-3 form-control restaurant_owners" disabled>
        </div>
      </fieldset>
    </div>
  </div>
</div>
      <div class="form-group col-12 text-center">
          <button type="button" class="btn btn-primary  save_restaurant_btn" ><i class="fa fa-save"></i> {{trans('lang.save')}}</button>
         <a href="{!! route('restaurants') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
      </div>
    </div>
  </div>
</div>
 @endsection
@section('scripts')
 <script>
	var id = "<?php echo $id;?>";
	var photo = "";
	var restaurantOwnerId = "";
	var restaurantOwnerOnline = false;

	$(document).ready(function(){
  		jQuery("#data-table_processing").show();

  		// Fetch restaurant data from SQL
  		$.ajax({
  			url: '/restaurants/' + id + '/data',
  			method: 'GET',
  			success: function(response) {
  				if (response.success) {
  					var restaurant = response.data;
  					$(".restaurant_name").val(restaurant.title);
  					$(".restaurant_address").val(restaurant.location);
  					$(".restaurant_latitude").val(restaurant.latitude);
  					$(".restaurant_longitude").val(restaurant.longitude);
  					$(".restaurant_description").val(restaurant.description);
  					restaurantOwnerOnline = restaurant.isActive;
  					photo = restaurant.photo;
  					restaurantOwnerId = restaurant.author;

  					if (restaurant.phonenumber) {
  						$(".restaurant_phone").val(restaurant.phonenumber);
  					}

  					// Fetch owner user data
  					if (restaurant.author) {
  						$.ajax({
  							url: '/users/' + restaurant.author + '/data',
  							method: 'GET',
  							success: function(userResponse) {
  								if (userResponse.success) {
  									var user = userResponse.data;
  									$(".restaurant_owners").val(user.firstName + " " + user.lastName);
  								}
  							}
  						});
  					}

  					// Fetch categories
  					$.ajax({
  						url: '/restaurants/categories',
  						method: 'GET',
  						success: function(catResponse) {
  							if (catResponse.success) {
  								catResponse.data.forEach(function(category) {
  									if (category.id == restaurant.categoryID) {
  										$('#restaurant_cuisines').append($("<option selected></option>")
  											.attr("value", category.id)
  											.text(category.title));
  									} else {
  										$('#restaurant_cuisines').append($("<option></option>")
  											.attr("value", category.id)
  											.text(category.title));
  									}
  								});
  							}
  						}
  					});

  					jQuery("#data-table_processing").hide();
  				}
  			},
  			error: function(xhr, status, error) {
  				console.error('Error fetching restaurant:', error);
  				jQuery("#data-table_processing").hide();
  			}
  		});

		$(".save_restaurant_btn").click(function(){
		  	var restaurantname = $(".restaurant_name").val();
			var cuisines = $("#restaurant_cuisines option:selected").val();
			var address = $(".restaurant_address").val();
			var latitude = parseFloat($(".restaurant_latitude").val());
			var longitude = parseFloat($(".restaurant_longitude").val());
			var description = $(".restaurant_description").val();
			var phonenumber = $(".restaurant_phone").val();
			var categoryTitle = $("#restaurant_cuisines option:selected").text();

			// Update restaurant via SQL
			$.ajax({
				url: '/restaurants/' + id,
				method: 'PUT',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					title: restaurantname,
					description: description,
					latitude: latitude,
					longitude: longitude,
					location: address,
					photo: photo,
					categoryID: cuisines,
					phonenumber: phonenumber,
					categoryTitle: categoryTitle
				},
				success: function(response) {
					if (response.success) {
						window.location.href = '{{ route("restaurants")}}';
					} else {
						alert('Error updating restaurant: ' + response.message);
					}
				},
				error: function(xhr, status, error) {
					console.error('Error updating restaurant:', error);
					alert('Error updating restaurant');
				}
			});
		})
	})

	// Handle file upload using traditional method (can be upgraded to Laravel storage later)
	function handleFileSelect(evt) {
  		var f = evt.target.files[0];
  		var reader = new FileReader();

	  	reader.onload = function(e) {
		    var fileData = e.target.result;
		    jQuery("#uploding_image").text("Uploading image...");

		    // Upload via AJAX to Laravel backend
		    $.ajax({
		    	url: '/upload-image',
		    	method: 'POST',
		    	headers: {
		    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    	},
		    	data: {
		    		image: fileData,
		    		filename: f.name
		    	},
		    	success: function(response) {
		    		if (response.success) {
		    			photo = response.url;
		    			jQuery("#uploding_image").text("Upload completed");
		    		} else {
		    			jQuery("#uploding_image").text("Upload failed");
		    		}
		    	},
		    	error: function() {
		    		jQuery("#uploding_image").text("Upload failed");
		    	}
		    });
	    };

  		reader.readAsDataURL(f);
	}
</script>
@endsection
