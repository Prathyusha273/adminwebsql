@extends('layouts.app')
@section('content')
	<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.special_offer')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.special_offer')}}</li>
            </ol>
        </div>
    </div>
        <div class="card-body">
      	  <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
              <fieldset>
                <legend>{{trans('lang.special_offer')}}</legend>
                    <div class="form-check width-100">
                      <input type="checkbox" class="form-check-inline" id="enable_special_discount">
                        <label class="col-5 control-label" for="enable_special_discount">{{ trans('lang.enable_special_discount_offer')}}</label>
                    </div>
              </fieldset>
            </div>
          </div>
          <div class="form-group col-12 text-center">
            <button type="button" class="btn btn-primary edit-setting-btn" ><i class="fa fa-save"></i> {{trans('lang.save')}}</button>
            <a href="{{url('/dashboard')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
          </div>
        </div>
 @endsection
@section('scripts')
<script>
    $(document).ready(function(){
        jQuery("#data-table_processing").show();

        // Fetch setting from SQL database
        $.ajax({
            url: '{{ route("settings.get", "specialDiscountOffer") }}',
            type: 'GET',
            success: function(response) {
                if(response.success && response.data && response.data.isEnable){
                    $("#enable_special_discount").prop('checked', true);
                }
                jQuery("#data-table_processing").hide();
            },
            error: function() {
                jQuery("#data-table_processing").hide();
            }
        });

        // Save setting
        var isSaving = false;
        $(".edit-setting-btn").click(function(){
            if (isSaving) {
                return false; // Prevent double-click
            }

            isSaving = true;
            var checkboxValue = $("#enable_special_discount").is(":checked");

            jQuery("#data-table_processing").show();
            $(this).prop('disabled', true);

            $.ajax({
                url: '{{ route("settings.update", "specialDiscountOffer") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    isEnable: checkboxValue
                },
                success: function(response) {
                    if(response.success) {
                        // Log the activity (optional - don't block on failure)
                        if (typeof logActivity === 'function') {
                            try {
                                logActivity('settings', 'updated', 'Updated special discount offer setting: ' + (checkboxValue ? 'Enabled' : 'Disabled'))
                                    .catch(function(err) {
                                        console.warn('Activity logging failed:', err);
                                    })
                                    .finally(function() {
                                        window.location.href = '{{ url("settings/app/specialOffer")}}';
                                    });
                            } catch(e) {
                                console.warn('Activity logging error:', e);
                                window.location.href = '{{ url("settings/app/specialOffer")}}';
                            }
                        } else {
                            window.location.href = '{{ url("settings/app/specialOffer")}}';
                        }
                    } else {
                        jQuery("#data-table_processing").hide();
                        $(".edit-setting-btn").prop('disabled', false);
                        isSaving = false;
                        alert('Error: ' + (response.message || 'Failed to update setting'));
                    }
                },
                error: function() {
                    jQuery("#data-table_processing").hide();
                    $(".edit-setting-btn").prop('disabled', false);
                    isSaving = false;
                    alert('Error updating setting');
                }
            });
        });
    });
</script>
@endsection
