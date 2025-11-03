@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Mart Sub-Categories</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('mart-categories') !!}">Mart Categories</a></li>
                    <li class="breadcrumb-item"><a href="#" id="subcategoriesLink">Sub-Categories</a></li>
                    <li class="breadcrumb-item active">Edit Sub-Category</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="cat-edite-page max-width-box">
                <div class="card  pb-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li role="presentation" class="nav-item">
                                <a href="#subcategory_information" aria-controls="description" role="tab" data-toggle="tab"
                                   class="nav-link active">Sub-Category Information</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a href="#review_attributes" aria-controls="review_attributes" role="tab"
                                   data-toggle="tab"
                                   class="nav-link">{{trans('lang.reviewattribute_plural')}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="error_top" style="display:none"></div>
                        <div class="row restaurant_payout_create" role="tabpanel">
                            <div class="restaurant_payout_create-inner tab-content">
                                <div role="tabpanel" class="tab-pane active" id="subcategory_information">
                                    <fieldset>
                                        <legend>Edit Mart Sub-Category</legend>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">Sub-Category Name</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control subcategory-name">
                                                <div class="form-text text-muted">Enter the name for this sub-category</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label ">Sub-Category Description</label>
                                            <div class="col-7">
                                <textarea rows="7" class="subcategory_description form-control"
                                          id="subcategory_description"></textarea>
                                                <div class="form-text text-muted">Enter a description for this sub-category</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">Sub-Category Image</label>
                                            <div class="col-7">
                                                <input type="file" id="subcategory_image">
                                                <div class="placeholder_img_thumb subcategory_image"></div>
                                                <div id="uploding_image"></div>
                                                <div class="form-text text-muted w-50">Upload an image for this sub-category</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">Order</label>
                                            <div class="col-7">
                                                <input type="number" class="form-control" id="subcategory_order" value="1" min="1">
                                                <div class="form-text text-muted w-50">Display order within parent category</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">Section</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control" id="section_info" readonly>
                                                <div class="form-text text-muted w-50">Inherited from parent category</div>
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-3 control-label">Parent Category</label>
                                            <div class="col-7">
                                                <input type="text" class="form-control" id="parent_category_info" readonly>
                                                <div class="form-text text-muted w-50">Parent category information</div>
                                            </div>
                                        </div>
                                       <div class="form-check row width-100">
                                        <input type="checkbox" class="item_publish" id="item_publish">
                                        <label class="col-3 control-label"
                                               for="item_publish">Publish</label>
                                       </div>
                                        <div class="form-check row width-100" id="show_in_home">
                                            <input type="checkbox" id="show_in_homepage">
                                            <label class="col-3 control-label" for="show_in_homepage">{{trans('lang.show_in_home')}}</label>
                                            <div class="form-text text-muted w-50">{{trans('lang.show_in_home_desc')}}<span id="forsection"></span></div>
                                        </div>            
                                    </fieldset>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="review_attributes">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-12 text-center btm-btn">
                        <button type="button" class="btn btn-primary edit-setting-btn"><i
                                    class="fa fa-save"></i> {{trans('lang.save')}}</button>
                        <a href="#" id="cancelLink" class="btn btn-default"><i
                                    class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
var id = "<?php echo $id;?>";
var photo = "";
var fileName="";
var subcategoryImageFile="";
var placeholderImage = '{{ asset("images/default_category.png") }}';
var subcategory = '';
var storageRef = firebase.storage().ref('images');
var storage = firebase.storage();
var categoryId = '';

$(document).ready(function () {
    jQuery("#data-table_processing").show();
    
    // Load sub-category data from SQL database
    $.ajax({
        url: '/api/mart-subcategories/' + id,
        type: 'GET',
        success: function(subcategory) {
            console.log('📊 Loading sub-category data for ID:', id);
            console.log('📝 Sub-category data loaded:', subcategory);
            
            categoryId = subcategory.parent_category_id;
            
            // Update breadcrumb link
            $('#subcategoriesLink').attr('href', '{{ url("mart-categories") }}/' + categoryId + '/subcategories');
            $('#cancelLink').attr('href', '{{ url("mart-categories") }}/' + categoryId + '/subcategories');
            
            $(".subcategory-name").val(subcategory.title);
            $(".subcategory_description").val(subcategory.description);
            $("#subcategory_order").val(subcategory.subcategory_order || 1);
            $("#section_info").val(subcategory.section || 'General');
            $("#parent_category_info").val(subcategory.parent_category_title || 'Unknown');
            
            if (subcategory.photo != '' && subcategory.photo != null) {
                photo = subcategory.photo;
                subcategoryImageFile = photo;
                $(".subcategory_image").append('<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:50px" src="' + photo + '" alt="image">');
                console.log('🖼️ Sub-category image loaded:', photo);
            } else {
                $(".subcategory_image").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
                console.log('🖼️ Using placeholder image');
            }
            
            if (subcategory.publish) {
                $("#item_publish").prop('checked', true);
                console.log('✅ Publish checkbox checked');
            }
            
            if (subcategory.show_in_homepage) {
                $("#show_in_homepage").prop('checked', true);
                console.log('✅ Show in homepage checkbox checked');
            }
            
            // Load review attributes
            loadReviewAttributes(subcategory.review_attributes || []);
            
            jQuery("#data-table_processing").hide();
            console.log('✅ Sub-category data loading completed');
        },
        error: function(xhr, status, error) {
            console.error('❌ Error loading sub-category data:', error);
            jQuery("#data-table_processing").hide();
            alert('Error loading sub-category data: ' + error);
        }
    });

    function loadReviewAttributes(selectedAttributes) {
        $.ajax({
            url: '/api/review-attributes',
            type: 'GET',
            success: function(reviewAttributes) {
                var ra_html = '';
                reviewAttributes.forEach(function(data) {
                    var checked = selectedAttributes.includes(data.id) ? 'checked' : '';
                    ra_html += '<div class="form-check width-100">';
                    ra_html += '<input type="checkbox" id="review_attribute_' + data.id + '" value="' + data.id + '" ' + checked + '>';
                    ra_html += '<label class="col-3 control-label" for="review_attribute_' + data.id + '">' + data.title + '</label>';
                    ra_html += '</div>';
                });
                $('#review_attributes').html(ra_html);
            },
            error: function(xhr, status, error) {
                console.error('Error loading review attributes:', error);
            }
        });
    }

    $(".edit-setting-btn").click(async function () {
        console.log('🔍 Save button clicked - starting update process...');
        
        var title = $(".subcategory-name").val();
        var description = $(".subcategory_description").val();
        var item_publish = $("#item_publish").is(":checked");
        var show_in_homepage = $("#show_in_homepage").is(":checked");
        var subcategory_order = parseInt($("#subcategory_order").val()) || 1;
        var review_attributes = [];
        
        console.log('📝 Form values:', {
            title: title,
            description: description,
            item_publish: item_publish,
            show_in_homepage: show_in_homepage,
            subcategory_order: subcategory_order
        });
        
        $('#review_attributes input').each(function () {
            if ($(this).is(':checked')) {
                review_attributes.push($(this).val());
            }
        });
        
        if (title == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>Please enter a sub-category name</p>");
            window.scrollTo(0, 0);
            return false;
        }
        
        try {
            jQuery("#data-table_processing").show();
            
            // Upload image to Firebase Storage if new image is selected
            let IMG = photo;
            if (photo != subcategoryImageFile && photo && fileName) {
                console.log('📤 Uploading new image...');
                IMG = await storeImageData();
            }

            // Update via SQL database
            $.ajax({
                url: '/api/mart-subcategories/' + id + '/update',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    title: title,
                    description: description,
                    photo: IMG,
                    subcategory_order: subcategory_order,
                    publish: item_publish ? 1 : 0,
                    show_in_homepage: show_in_homepage ? 1 : 0,
                    review_attributes: review_attributes
                },
                success: function(response) {
                    jQuery("#data-table_processing").hide();
                    console.log('🎉 Update completed successfully!');
                    window.location.href = '{{ url("mart-categories") }}/' + categoryId + '/subcategories';
                },
                error: function(xhr, status, error) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error 
                        ? xhr.responseJSON.error 
                        : 'Error updating sub-category';
                    $(".error_top").append("<p>" + errorMessage + "</p>");
                    window.scrollTo(0, 0);
                }
            });
            
        } catch (error) {
            console.error('❌ Error during update:', error);
            jQuery("#data-table_processing").hide();
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>Error updating sub-category: " + error.message + "</p>");
            window.scrollTo(0, 0);
        }
    });
});

async function storeImageData() {
    console.log('🖼️ Starting image processing...');
    
    var newPhoto = '';
    try {
        // Delete old image if it's different from current
        if (subcategoryImageFile != "" && photo != subcategoryImageFile) {
            console.log('🗑️ Deleting old image...');
            try {
                var subcatOldImageUrlRef = await storage.refFromURL(subcategoryImageFile);
                var imageBucket = subcatOldImageUrlRef.bucket; 
                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                if (imageBucket == envBucket) {
                    await subcatOldImageUrlRef.delete();
                    console.log("✅ Old file deleted successfully!");
                }
            } catch (deleteError) {
                console.log("⚠️ Error deleting old file:", deleteError);
            }
        } 
        
        // Upload new image
        if (photo != subcategoryImageFile && photo && fileName) {
            console.log('📤 Uploading new image...');
            photo = photo.replace(/^data:image\/[a-z]+;base64,/, "");
            var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });
            var downloadURL = await uploadTask.ref.getDownloadURL();
            newPhoto = downloadURL;
            photo = downloadURL;
            console.log('✅ New image uploaded:', newPhoto);
        } else {
            newPhoto = photo;
            console.log('ℹ️ Using existing image:', newPhoto);
        }
    } catch (error) {
        console.error("❌ Error in storeImageData:", error);
        newPhoto = photo || subcategoryImageFile;
    }
    
    return newPhoto;
}  

//upload image with compression
$("#subcategory_image").resizeImg({
    callback: function(base64str) {
        try {
            var val = $('#subcategory_image').val().toLowerCase();
            var ext = val.split('.')[1];
            var filename = $('#subcategory_image').val().replace(/C:\\fakepath\\/i, '')
            var timestamp = Number(new Date());
            var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
            
            photo = base64str;
            fileName = filename;
            
            $(".subcategory_image").empty();
            $(".subcategory_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
            $("#subcategory_image").val('');
            
            console.log('✅ Image processed and displayed successfully');
        } catch (error) {
            console.error('❌ Error in image compression callback:', error);
        }
    },
    error: function(error) {
        console.error('❌ Image compression error:', error);
        alert('Error processing image: ' + error.message);
    }
});
</script>
@endsection
