@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <?php if ($id != '') { ?>
                <h3 class="text-themecolor restaurant_name_heading"></h3>
            <?php } else { ?>
                <h3 class="text-themecolor">Mart Items</h3>
            <?php } ?>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{!! route('dashboard') !!}">{{ trans('lang.dashboard') }}</a>
                </li>
                <?php if ($id != '') { ?>
                    <li class="breadcrumb-item"><a
                            href="{{ route('marts.mart-items', $id) }}">{{ trans('lang.mart_item_plural') }}</a></li>
                <?php } else { ?>
                    <li class="breadcrumb-item"><a href="{!! route('mart-items') !!}">Mart Items</a></li>
                <?php } ?>
                <li class="breadcrumb-item active">{{ trans('lang.mart_item_create') }}</li>
            </ol>
        </div>
    </div>
    <div>
        <div class="card-body">
            <div class="error_top" style="display:none"></div>
            <div class="row restaurant_payout_create">
                <div class="restaurant_payout_create-inner">
                    <fieldset>
                        <legend>Mart Item Information</legend>
                        <div class="form-group row width-100" id="admin_commision_info" style="display:none">
                            <div class="m-3">
                                <div class="form-text font-weight-bold text-danger h6">
                                    {{ trans('lang.price_instruction') }}
                                </div>
                                <div class="form-text font-weight-bold text-danger h6" id="admin_commision"></div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Item Name</label>
                            <div class="col-7">
                                <input type="text" class="form-control food_name" required>
                                <div class="form-text text-muted">
                                    {{ trans('lang.food_name_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Price</label>
                            <div class="col-7">
                                <input type="text" class="form-control food_price"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Discount Price</label>
                            <div class="col-7">
                                <input class="form-control food_discount"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                <div class="form-text text-muted">
                                    {{ trans('lang.food_discount_help') }}
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Item Features -->
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">Item Features</label>
                            <div class="col-7">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="isSpotlight">
                                            <label class="form-check-label" for="isSpotlight">Spotlight</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="isStealOfMoment">
                                            <label class="form-check-label" for="isStealOfMoment">Steal of Moment</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="isFeature">
                                            <label class="form-check-label" for="isFeature">Featured</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="isTrending">
                                            <label class="form-check-label" for="isTrending">Trending</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="isNew">
                                            <label class="form-check-label" for="isNew">New Arrival</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="isBestSeller">
                                            <label class="form-check-label" for="isBestSeller">Best Seller</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="isSeasonal">
                                            <label class="form-check-label" for="isSeasonal">Seasonal</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text text-muted">
                                    Select item features for better categorization and filtering
                                </div>
                            </div>
                        </div>
                        <?php if ($id == '') { ?>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">Mart</label>
                                <div class="col-7">
                                    <select id="food_restaurant" class="form-control" required>
                                        <option value="">Select Mart</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        Select the mart where this item will be available
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- <div class="form-group row width-100">
                            <label class="col-3 control-label">{{ trans('lang.food_category_id') }}</label>
                            <div class="col-7">
                                <select id='food_category' class="form-control" required>
                                    <option value="">{{ trans('lang.select_category') }}</option>
                                </select>
                                <div class="form-text text-muted">
                                    {{ trans('lang.food_category_id_help') }}
                                </div>
                            </div>
                        </div> -->
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">Mart Categories</label>
                            <div class="col-7">
                                <div id="selected_categories" class="mb-2"></div>
                                <input type="text" id="food_category_search" class="form-control mb-2" placeholder="Search mart categories...">
                                <select id='food_category' class="form-control" multiple required>
                                    <option value="">Select mart categories</option>
                                    <!-- options populated dynamically -->
                                </select>
                                <div class="form-text text-muted">
                                 Select the mart categories for this item
                                </div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Brand</label>
                            <div class="col-7">
                                <select id='brand_select' class="form-control">
                                    <option value="">Select Brand (Optional)</option>
                                    <!-- options populated dynamically -->
                                </select>
                                <div class="form-text text-muted">
                                    Select the brand for this item (optional)
                                </div>
                            </div>
                        </div>

                        <div class="form-group row width-100">
                            <label class="col-3 control-label">Mart Sub-Categories</label>
                            <div class="col-7">
                                <div id="selected_subcategories" class="mb-2"></div>
                                <input type="text" id="food_subcategory_search" class="form-control mb-2" placeholder="Search mart sub-categories...">
                                <select id='food_subcategory' class="form-control" multiple>
                                    <option value="">Select mart sub-categories (optional)</option>
                                    <!-- options populated dynamically -->
                                </select>
                                <div class="form-text text-muted">
                                 Select the mart sub-categories for this item (optional)
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Section</label>
                            <div class="col-7">
                                <input type="text" class="form-control" id="section_info" readonly>
                                <div class="form-text text-muted">Auto-fetched from selected subcategory</div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.item_quantity') }}</label>
                            <div class="col-7">
                                <input type="number" class="form-control item_quantity" value="-1" min="-1" step="1">
                                <div class="form-text text-muted">
                                    {{ trans('lang.item_quantity_help') }}
                                </div>
                            </div>
                        </div>
{{--                        <div class="form-group row width-100" id="attributes_div">--}}
{{--                            <label class="col-3 control-label">{{ trans('lang.item_attribute_id') }}</label>--}}
{{--                            <div class="col-7">--}}
{{--                                <select id='item_attribute' class="form-control chosen-select"--}}
{{--                                    multiple="multiple" onchange="selectAttribute();"></select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="form-group row width-100">
                            <div class="item_attributes" id="item_attributes"></div>
                            <div class="item_variants" id="item_variants"></div>
                            <input type="hidden" id="attributes" value="" />
                            <input type="hidden" id="variants" value="" />
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">Item Image</label>
                            <div class="col-7">
                                <input type="file" id="product_image">
                                <div class="placeholder_img_thumb product_image"></div>
                                <div id="uploding_image"></div>
                                <div class="form-text text-muted">
                                    {{ trans('lang.food_image_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">Item Description</label>
                            <div class="col-7">
                                <textarea rows="8" class="form-control food_description"
                                    id="food_description"></textarea>
                            </div>
                        </div>
                        <div class="form-check width-100">
                            <input type="checkbox" class="food_publish" id="food_publish">
                                                            <label class="col-3 control-label"
                                    for="food_publish">Publish</label>
                        </div>
                        <!-- Hidden non-veg field for now -->
                        <div class="form-check width-100" style="display: none;">
                            <input type="checkbox" class="food_nonveg" id="food_nonveg">
                            <label class="col-3 control-label" for="food_nonveg">{{ trans('lang.non_veg') }}</label>
                        </div>
                        <div class="form-check width-100" style="display: none">
                            <input type="checkbox" class="food_take_away_option" id="food_take_away_option">
                            <label class="col-3 control-label"
                                for="food_take_away_option">{{ trans('lang.food_take_away') }}</label>
                        </div>
                        <div class="form-check width-100">
                            <input type="checkbox" class="food_is_available" id="food_is_available">
                            <label class="col-3 control-label" for="food_is_available">Available</label>
                        </div>
                    </fieldset>

                    <!-- Options Configuration -->
                    <fieldset>
                        <legend>Options Configuration</legend>

                        <div class="form-check width-100">
                            <input type="checkbox" class="has_options" id="has_options">
                            <label class="col-3 control-label" for="has_options">
                                <strong>Enable Options for this item</strong>
                            </label>
                            <div class="form-text text-muted">
                                Enable this to create different variants/sizes for this item
                            </div>
                        </div>

                        <div id="options_config" style="display:none;">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline"></i>
                                <strong>Options will be stored as part of this item.</strong>
                                Each option can have its own price, image, and specifications.
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">Default Option</label>
                                <div class="col-7">
                                    <select id="default_option" class="form-control">
                                        <option value="">Select default option</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        The default option will be automatically selected when customers view this item.
                                        This is typically the featured option or the most popular choice.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Options Management -->
                    <fieldset id="options_fieldset" style="display:none;">
                        <legend>Item Options</legend>

                        <div class="options-list">
                            <!-- Dynamic options will be added here -->
                        </div>

                        <div class="form-group row width-100">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-primary" onclick="addNewOption()">
                                    <i class="mdi mdi-plus"></i> Add Option
                                </button>
                            </div>
                        </div>

                        <div class="options-summary" style="display:none;">
                            <h5>Options Summary</h5>
                            <div class="summary-content">
                                <!-- Will show price range and option count -->
                            </div>
                        </div>
                    </fieldset>
                    <!-- Hidden ingredients fieldset for now -->
                    <fieldset style="display: none;">
                        <legend>{{ trans('lang.ingredients') }}</legend>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.calories') }}</label>
                            <div class="col-7">
                                <input type="number" class="form-control food_calories">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.grams') }}</label>
                            <div class="col-7">
                                <input type="number" class="form-control food_grams">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.fats') }}</label>
                            <div class="col-7">
                                <input type="number" class="form-control food_fats">
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{ trans('lang.proteins') }}</label>
                            <div class="col-7">
                                <input type="number" class="form-control food_proteins">
                            </div>
                        </div>
                    </fieldset>
                    <!-- Hidden add-ons fieldset for now -->
                    <fieldset style="display: none;">
                        <legend>{{ trans('lang.food_add_one') }}</legend>
                        <div class="form-group add_ons_list extra-row">
                        </div>
                        <div class="form-group row width-100">
                            <div class="col-7">
                                <button type="button" onclick="addOneFunction()" class="btn btn-primary"
                                    id="add_one_btn">{{ trans('lang.food_add_one') }}</button>
                            </div>
                        </div>
                        <div class="form-group row width-100" id="add_ones_div" style="display:none">
                            <div class="row">
                                <div class="col-6">
                                    <label class="col-3 control-label">{{ trans('lang.food_title') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control add_ons_title">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="col-3 control-label">{{ trans('lang.food_price') }}</label>
                                    <div class="col-7">
                                        <input type="number" class="form-control add_ons_price">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row save_add_one_btn width-100" style="display:none">
                            <div class="col-7">
                                <button type="button" onclick="saveAddOneFunction()"
                                    class="btn btn-primary">{{ trans('lang.save_add_ones') }}</button>
                            </div>
                        </div>
                    </fieldset>
                    <!-- Hidden product specification fieldset for now -->
                    <fieldset style="display: none;">
                        <legend>{{ trans('lang.product_specification') }}</legend>
                        <div class="form-group product_specification extra-row">
                        </div>
                        <div class="form-group row width-100">
                            <div class="col-7">
                                <button type="button" onclick="addProductSpecificationFunction()"
                                    class="btn btn-primary" id="add_one_btn">
                                    {{ trans('lang.add_product_specification') }}</button>
                            </div>
                        </div>
                        <div class="form-group row width-100" id="add_product_specification_div" style="display:none">
                            <div class="row">
                                <div class="col-6">
                                    <label class="col-2 control-label">{{ trans('lang.lable') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control add_label">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="col-3 control-label">{{ trans('lang.value') }}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control add_value">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row save_product_specification_btn width-100" style="display:none">
                            <div class="col-7">
                                <button type="button" onclick="saveProductSpecificationFunction()"
                                    class="btn btn-primary">{{ trans('lang.save_product_specification') }}</button>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save-form-btn"><i class="fa fa-save"></i>
                    {{ trans('lang.save') }}</button>
                <?php if ($id != '') { ?>
                    <a href="{{ route('marts.mart-items', $id) }}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                <?php } else { ?>
                    <a href="{!! route('mart-items') !!}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{ trans('lang.cancel') }}</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    // Cache-busting comment: ID field fix - {{ now()->format('Y-m-d H:i:s') }}
    var database=firebase.firestore();
    var photo="";
    var addOnesTitle=[];
    var addOnesPrice=[];
    var sizeTitle=[];
    var sizePrice=[];
    var attributes_list=[];
    var categories_list=[];
    var restaurant_list=[];
    var product_specification={};
    var variant_photos=[];
    var variant_filename=[];
    var variant_vIds=[];
    var reataurantIDDirec="<?php echo $id; ?>";
    var itemLimit=-1;
    var restaurant='';
    var refCurrency=database.collection('currencies').where('isActive','==',true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData=snapshots.docs[0].data();
        currentCurrency=currencyData.symbol;
        currencyAtRight=currencyData.symbolAtRight;
        if(currencyData.decimal_degits) {
            decimal_degits=currencyData.decimal_degits;
        }
    });
    var refAdminCommission=database.collection('settings').doc("AdminCommission");
    refAdminCommission.get().then(async function(snapshots) {
        var adminCommissionSettings=snapshots.data();
        if(adminCommissionSettings) {
            var commission_type=adminCommissionSettings.commissionType;
            var commission_value=adminCommissionSettings.fix_commission;
            if(commission_type=="Percent") {
                var commission_text=commission_value+'%';
            } else {
                if(currencyAtRight) {
                    commission_text=parseFloat(commission_value).toFixed(decimal_degits)+""+
                        currentCurrency;
                } else {
                    commission_text=currentCurrency+""+parseFloat(commission_value).toFixed(
                        decimal_degits);
                }
            }
            if(adminCommissionSettings.isEnabled) {
                $("#admin_commision_info").show();
                $("#admin_commision").html('Admin Commission: '+commission_text);
            }
        }
    });
                database.collection('mart_items').where('vendorID','==',reataurantIDDirec).get().then(async function(snapshot) {
        totalProductCount=snapshot.docs.length;
    })
    //restaurant_name_heading
    $(document).ready(function() {
        jQuery(document).on("click",".mdi-cloud-upload",function() {
            var variant=jQuery(this).data('variant');
            var photo_remove=$(this).attr('data-img');
            index=variant_photos.indexOf(photo_remove);
            if(index>-1) {
                variant_photos.splice(index,1); // 2nd parameter means remove one item only
            }
            var file_remove=$(this).attr('data-file');
            fileindex=variant_filename.indexOf(file_remove);
            if(fileindex>-1) {
                variant_filename.splice(fileindex,1); // 2nd parameter means remove one item only
            }
            variantindex=variant_vIds.indexOf(variant);
            if(variantindex>-1) {
                variant_vIds.splice(variantindex,1); // 2nd parameter means remove one item only
            }
            $('[id="file_'+variant+'"]').click();
        });
        jQuery(document).on("click",".mdi-delete",function() {
            var variant=jQuery(this).data('variant');
            $('[id="variant_'+variant+'_image"]').empty();
            var photo_remove=$(this).attr('data-img');
            index=variant_photos.indexOf(photo_remove);
            if(index>-1) {
                variant_photos.splice(index,1); // 2nd parameter means remove one item only
            }
            var file_remove=$(this).attr('data-file');
            fileindex=variant_filename.indexOf(file_remove);
            if(fileindex>-1) {
                variant_filename.splice(fileindex,1); // 2nd parameter means remove one item only
            }
            variantindex=variant_vIds.indexOf(variant);
            if(variantindex>-1) {
                variant_vIds.splice(variantindex,1); // 2nd parameter means remove one item only
            }
        });
        jQuery("#data-table_processing").show();

        // Debug: Check if collections exist
        console.log('🔍 Create page - Starting data fetch...');

        // Fetch vendors from SQL database
        $.ajax({
            url: '{{ route("mart-items.vendors") }}',
            method: 'GET',
            success: function(vendors) {
                console.log('🔍 Create page - Found ' + vendors.length + ' mart vendors from SQL');
                vendors.forEach((data) => {
                    console.log('📋 Create page - Mart Vendor:', data.title, 'ID:', data.id);
                    restaurant_list.push(data);
                    $('#food_restaurant').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.title));
                    if (reataurantIDDirec == data.id) {
                        $(".restaurant_name_heading").html(data.title);
                    }
                });
                console.log('✅ Create page - Loaded ' + vendors.length + ' mart vendors from SQL');
            },
            error: function(xhr, status, error) {
                console.error('❌ Create page - Error fetching vendors:', error);
            }
        });

        // Fetch mart categories from SQL
        $.ajax({
            url: '{{ route("mart-items.categories") }}',
            method: 'GET',
            success: function(categories) {
                console.log('🔍 Create page - Found ' + categories.length + ' mart categories from SQL');
                categories.forEach((data) => {
                    console.log('📋 Create page - Category:', data.title, 'ID:', data.id);
                    categories_list.push(data);
                    $('#food_category').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.title));
                });
                updateSelectedFoodCategoryTags();
                console.log('✅ Create page - Loaded ' + categories.length + ' categories from SQL');
            },
            error: function(xhr, status, error) {
                console.error('❌ Create page - Error fetching categories:', error);
            }
        });

        // Fetch mart subcategories from SQL
        $.ajax({
            url: '{{ route("mart-items.subcategories") }}',
            method: 'GET',
            success: function(subcategories) {
                console.log('🔍 Create page - Found ' + subcategories.length + ' mart subcategories from SQL');
                subcategories.forEach((data) => {
                    console.log('📋 Create page - Subcategory:', data.title, 'ID:', data.id, 'Parent:', data.categoryID);
                    $('#food_subcategory').append($("<option></option>")
                        .attr("value", data.id)
                        .attr("data-parent", data.categoryID || data.parent_category_id)
                        .text(data.title));
                });
                updateSelectedSubcategoryTags();

                // Debug: Log the selected subcategory value
                $('#food_subcategory').on('change', function() {
                    console.log('🔍 Selected subcategory value:', $(this).val());
                    console.log('🔍 Selected subcategory text:', $(this).find('option:selected').text());

                    // Auto-fetch section from selected subcategory
                    updateSectionFromSubcategory();
                });
                console.log('✅ Create page - Loaded ' + subcategories.length + ' subcategories from SQL');
            },
            error: function(xhr, status, error) {
                console.error('❌ Create page - Error fetching subcategories:', error);
            }
        });

        // Fetch brands from SQL
        $.ajax({
            url: '{{ route("mart-items.brands") }}',
            method: 'GET',
            success: function(brands) {
                console.log('🔍 Create page - Found ' + brands.length + ' brands from SQL');
                brands.forEach((data) => {
                    console.log('📋 Create page - Brand:', data.name, 'ID:', data.id);
                    $('#brand_select').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.name));
                });
                console.log('✅ Create page - Loaded ' + brands.length + ' brands from SQL');
            },
            error: function(xhr, status, error) {
                console.error('❌ Create page - Error fetching brands:', error);
            }
        });

        // Fetch vendor attributes
        var attributes = database.collection('vendor_attributes');
        attributes.get().then(async function(snapshots) {
            console.log('🔍 Create page - Found ' + snapshots.docs.length + ' vendor attributes');
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                console.log('📋 Create page - Attribute:', data.title, 'ID:', data.id);
                attributes_list.push(data);
                $('#item_attribute').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.title));
            });
            $("#item_attribute").show().chosen({
                "placeholder_text": "{{ trans('lang.select_attribute') }}"
            });
        }).catch(function(error) {
            console.error('❌ Create page - Error fetching attributes:', error);
        });

        jQuery("#data-table_processing").hide();

        // Random review generation functions
        function generateRandomReviewCount() {
            // Generate random number between 70 and 130
            return Math.floor(Math.random() * (130 - 70 + 1)) + 70;
        }

        function generateRandomReviewSum() {
            // Generate random number between 4.8 and 5.0 with 1 decimal place
            return (Math.random() * (5.0 - 4.8) + 4.8).toFixed(1);
        }

        $(".save-form-btn").click(async function() {
            console.log('🔍 Save button clicked - starting validation...');
            console.log('🚀 NEW ID GENERATION LOGIC LOADED - Version: {{ now()->format('Y-m-d H:i:s') }}');

            // Get form values
            var name = $(".food_name").val().trim();
            var price = $(".food_price").val().trim();
            var discount = $(".food_discount").val().trim();
            var description = $("#food_description").val().trim();
            var quantity = parseInt($(".item_quantity").val()) || -1;

            <?php if ($id == '') { ?>
            var restaurant = $("#food_restaurant").val();
            <?php } else { ?>
            var restaurant = "<?php echo $id; ?>";
            <?php } ?>

            var category = $("#food_category").val();
            var subcategory = $("#food_subcategory").val();
            var brand = $("#brand_select").val();

            // Handle multiple category selection - take the first selected category
            if (Array.isArray(category) && category.length > 0) {
                category = category[0]; // Take the first selected category
            } else if (category === '') {
                category = '';
            }

            // Handle multiple subcategory selection
            if (Array.isArray(subcategory) && subcategory.length > 0) {
                subcategory = subcategory[0]; // Take the first selected subcategory
            } else if (subcategory === '') {
                subcategory = '';
            }

            // Get category, subcategory, and brand titles
            var categoryTitle = '';
            var subcategoryTitle = '';
            var vendorTitle = '';
            var brandTitle = '';

            if (category) {
                categoryTitle = $("#food_category option:selected").text() || '';
            }

            if (subcategory) {
                subcategoryTitle = $("#food_subcategory option:selected").text() || '';
            }

            if (brand) {
                brandTitle = $("#brand_select option:selected").text() || '';
            }

            // Get vendor title from restaurant_list
            if (restaurant) {
                restaurant_list.forEach((vendor) => {
                    if (vendor.id == restaurant) {
                        vendorTitle = vendor.title || '';
                    }
                });
            }

            // Get checkbox values
            var foodPublish = $(".food_publish").is(":checked");
            var foodIsAvailable = $(".food_is_available").is(":checked");
            var nonveg = $(".food_nonveg").is(":checked");
            var veg = !nonveg;
            var foodTakeaway = $(".food_take_away_option").is(":checked");

            // Get item features
            var isSpotlight = $("#isSpotlight").is(":checked");
            var isStealOfMoment = $("#isStealOfMoment").is(":checked");
            var isFeature = $("#isFeature").is(":checked");
            var isTrending = $("#isTrending").is(":checked");
            var isNew = $("#isNew").is(":checked");
            var isBestSeller = $("#isBestSeller").is(":checked");
            var isSeasonal = $("#isSeasonal").is(":checked");

            // Debug checkbox states
            console.log('🔍 Checkbox states:', {
                isSpotlight, isStealOfMoment, isFeature, isTrending, isNew, isBestSeller, isSeasonal
            });

            console.log('📝 Form values:', {
                name, price, discount, description, quantity, restaurant, vendorTitle, category, categoryTitle, subcategory, subcategoryTitle, brand, brandTitle,
                foodPublish, foodIsAvailable, nonveg, veg, foodTakeaway,
                isSpotlight, isStealOfMoment, isFeature, isTrending, isNew, isBestSeller, isSeasonal
            });

            // Validation
            if (name == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.enter_food_name_error') }}</p>");
                window.scrollTo(0, 0);
                return;
            }

            if (price == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.enter_food_price_error') }}</p>");
                window.scrollTo(0, 0);
                return;
            }

            if (restaurant == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>Please select a mart</p>");
                window.scrollTo(0, 0);
                return;
            }

            if (category == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>Please select a mart category</p>");
                window.scrollTo(0, 0);
                return;
            }

            if (parseInt(price) < parseInt(discount || 0)) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.price_should_not_less_then_discount_error') }}</p>");
                window.scrollTo(0, 0);
                return;
            }

            if (quantity < -1) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.invalid_item_quantity_error') }}</p>");
                window.scrollTo(0, 0);
                return;
            }

            if (description == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.enter_food_description_error') }}</p>");
                window.scrollTo(0, 0);
                return;
            }

            console.log('✅ Validation passed, starting save process...');
            $(".error_top").hide();

            // Process photo data first - convert base64 to Firebase URL if needed
            console.log('🖼️ Processing photo data...');
            photo = await storeProductImageData();
            console.log('🖼️ Photo processed:', photo);

            const hasOptions = $(".has_options").is(":checked");

            if (hasOptions && optionsList.length === 0) {
                alert('Please add at least one option when options are enabled.');
                return;
            }

            if (hasOptions) {
                // Validate options
                for (let option of optionsList) {
                    if (!option.title || !option.unit_price || option.unit_price <= 0) {
                        alert('Please fill all required fields for all options.');
                        return;
                    }
                }

                // Process all option images to Firebase URLs before saving
                console.log('🖼️ Processing option images to Firebase URLs...');
                for (let i = 0; i < optionsList.length; i++) {
                    const option = optionsList[i];
                    if (option.image && option.image.startsWith('data:image')) {
                        console.log(`🔄 Converting option ${i + 1} image to Firebase URL...`);
                        try {
                            const firebaseUrl = await storeOptionImageData(option.image, option.id);
                            optionsList[i].image = firebaseUrl;
                            console.log(`✅ Option ${i + 1} image converted:`, firebaseUrl);
                        } catch (error) {
                            console.error(`❌ Error converting option ${i + 1} image:`, error);
                            optionsList[i].image = ''; // Clear invalid image
                        }
                    }
                }

                // Prepare options data
                const optionsData = optionsList.map((option, index) => ({
                    id: option.id || `option_${Date.now()}_${index}`,
                    option_type: option.type || 'size',
                    option_title: option.title || '',
                    option_subtitle: option.subtitle || '',
                    unit_price: parseFloat(option.unit_price) || 0, // UI Unit Price
                    original_unit_price: parseFloat(option.original_unit_price) || 0, // UI Original Unit Price
                    price: parseFloat(option.total_price) || 0, // Calculated Total Price (for mobile app)
                    original_price: parseFloat(option.original_total_price) || 0, // Calculated Original Total Price (for mobile app)
                    discount_amount: parseFloat(option.discount_amount) || 0,
                    discount_percentage: parseFloat(option.discount_percentage) || 0,
                    unit_measure: parseFloat(option.unit_measure) || 100,
                    unit_measure_type: option.quantity_unit || 'g', // Fixed: use quantity_unit as unit_measure_type
                    quantity: parseFloat(option.quantity) || 0,
                    quantity_unit: option.quantity_unit || 'g',
                    image: option.image || '', // Now contains Firebase URL instead of base64
                    is_available: option.is_available !== false,
                    is_featured: option.is_featured === true,
                    sort_order: index + 1,
                    created_at: new Date().toISOString()
                }));

                // Calculate price range
                const prices = optionsList.map(opt => opt.total_price);
                const minPrice = Math.min(...prices);
                const maxPrice = Math.max(...prices);
                const defaultOptionId = optionsList.find(opt => opt.is_featured)?.id || optionsList[0]?.id;

                // Prepare item data for SQL save
                const documentId = 'item_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                const itemData = {
                    id: documentId,
                    name: name,
                    price: parseFloat(price) || 0,
                    disPrice: parseFloat(discount) || parseFloat(price) || 0,
                    vendorID: restaurant,
                    vendorTitle: vendorTitle,
                    categoryID: category,
                    categoryTitle: categoryTitle,
                    subcategoryID: subcategory || '',
                    subcategoryTitle: subcategoryTitle || '',
                    brandID: brand || '',
                    brandTitle: brandTitle || '',
                    section: $('#section_info').val() || 'General',
                    photo: photo || '',
                    description: description,
                    publish: foodPublish,
                    isAvailable: foodIsAvailable,
                    nonveg: nonveg,
                    veg: veg,
                    takeawayOption: foodTakeaway,
                    isSpotlight: isSpotlight,
                    isStealOfMoment: isStealOfMoment,
                    isFeature: isFeature,
                    isTrending: isTrending,
                    isNew: isNew,
                    isBestSeller: isBestSeller,
                    isSeasonal: isSeasonal,
                    has_options: true,
                    options_enabled: true,
                    options_toggle: true,
                    options_count: optionsList.length || 0,
                    min_price: minPrice || 0,
                    max_price: maxPrice || 0,
                    price_range: `₹${minPrice || 0} - ₹${maxPrice || 0}`,
                    default_option_id: defaultOptionId || '',
                    best_value_option: optionsList.find(opt => opt.total_price === Math.min(...optionsList.map(o => o.total_price)))?.id || '',
                    savings_percentage: Math.max(...optionsList.map(opt => opt.original_total_price > opt.total_price ? ((opt.original_total_price - opt.total_price) / opt.original_total_price) * 100 : 0)) || 0,
                    options: JSON.stringify(optionsData),
                    quantity: parseInt(quantity) || -1,
                    calories: parseInt($(".food_calories").val()) || 0,
                    grams: parseInt($(".food_grams").val()) || 0,
                    proteins: parseInt($(".food_proteins").val()) || 0,
                    fats: parseInt($(".food_fats").val()) || 0,
                    addOnsTitle: JSON.stringify(addOnesTitle || []),
                    addOnsPrice: JSON.stringify(addOnesPrice || []),
                    product_specification: JSON.stringify(product_specification || {}),
                    item_attribute: null
                };

                console.log('📊 Saving item with options via SQL:', itemData);

                // Save to SQL database via AJAX
                $.ajax({
                    url: '{{ route("mart-items.store") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ...itemData
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('✅ Mart item created successfully via SQL:', response);
                            
                            // Log activity
                            if (typeof logActivity === 'function') {
                                logActivity('mart_items', 'created', 'Created mart item with options: ' + itemData.name);
                            }
                            
                            <?php if ($id != '') { ?>
                                window.location.href = '{{ route("marts.mart-items", $id) }}';
                            <?php } else { ?>
                                window.location.href = '{{ route("mart-items") }}';
                            <?php } ?>
                        } else {
                            console.error('❌ Save failed:', response.message);
                            $(".error_top").show();
                            $(".error_top").html("<p>Error: " + response.message + "</p>");
                            window.scrollTo(0, 0);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ AJAX error:', error);
                        $(".error_top").show();
                        $(".error_top").html("<p>Error saving item: " + (xhr.responseJSON?.message || error) + "</p>");
                        window.scrollTo(0, 0);
                    }
                });

            } else {
                // Save regular item without options
                // Generate a temporary document reference to get the ID first
                const documentId = 'item_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                const itemData = {
                    id: documentId, // Include ID in the initial document creation
                    name: name,
                    price: parseFloat(price) || 0,
                    disPrice: parseFloat(discount) || parseFloat(price) || 0,
                    vendorID: restaurant,
                    vendorTitle: vendorTitle, // Add vendor title
                    categoryID: category,
                    categoryTitle: categoryTitle, // Add category title
                    subcategoryID: subcategory || '', // Add subcategory
                    subcategoryTitle: subcategoryTitle || '', // Add subcategory title
                    brandID: brand || '', // Add brand ID
                    brandTitle: brandTitle || '', // Add brand title
                    section: $('#section_info').val() || 'General', // Add section
                    photo: photo || '',
                    description: description,
                    publish: foodPublish,
                    isAvailable: foodIsAvailable,
                    nonveg: nonveg,
                    veg: veg,
                    takeawayOption: foodTakeaway,

                    // Review fields - Generate random realistic values
                    reviewCount: generateRandomReviewCount().toString(), // Random review count (70-130)
                    reviewSum: generateRandomReviewSum().toString(), // Random review sum (4.8-5.0)

                    // Enhanced Filter Fields
                    isSpotlight: isSpotlight,
                    isStealOfMoment: isStealOfMoment,
                    isFeature: isFeature,
                    isTrending: isTrending,
                    isNew: isNew,
                    isBestSeller: isBestSeller,
                    isSeasonal: isSeasonal,

                    // Options configuration
                    has_options: false,
                    options_enabled: false,
                    options_toggle: false,
                    options_count: 0,
                    options: [],

                    // Existing fields
                    quantity: quantity,
                    calories: parseInt($(".food_calories").val()) || 0,
                    grams: parseInt($(".food_grams").val()) || 0,
                    proteins: parseInt($(".food_proteins").val()) || 0,
                    fats: parseInt($(".food_fats").val()) || 0,
                    addOnsTitle: addOnesTitle || [],
                    addOnsPrice: addOnesPrice || [],
                    product_specification: product_specification || {},
                    item_attribute: null,
                };

                // Convert arrays to JSON strings for SQL
                itemData.options = JSON.stringify([]);
                itemData.addOnsTitle = JSON.stringify(itemData.addOnsTitle || []);
                itemData.addOnsPrice = JSON.stringify(itemData.addOnsPrice || []);
                itemData.product_specification = JSON.stringify(itemData.product_specification || {});

                console.log('📊 Saving regular item via SQL:', itemData);

                // Save to SQL database via AJAX
                $.ajax({
                    url: '{{ route("mart-items.store") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ...itemData
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('✅ Regular mart item created successfully via SQL:', response);
                            
                            // Log activity
                            if (typeof logActivity === 'function') {
                                logActivity('mart_items', 'created', 'Created mart item: ' + itemData.name);
                            }
                            
                            <?php if ($id != '') { ?>
                                window.location.href = '{{ route("marts.mart-items", $id) }}';
                            <?php } else { ?>
                                window.location.href = '{{ route("mart-items") }}';
                            <?php } ?>
                        } else {
                            console.error('❌ Save failed:', response.message);
                            $(".error_top").show();
                            $(".error_top").html("<p>Error: " + response.message + "</p>");
                            window.scrollTo(0, 0);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ AJAX error creating item:', error);
                        $(".error_top").show();
                        $(".error_top").html("<p>Error saving item: " + (xhr.responseJSON?.message || error) + "</p>");
                        window.scrollTo(0, 0);
                    }
                });
            }
        })
    })
            async function checkDocumentAndPlan(vendorId) {
        var data='';
        var documentVerify=false;
        var verificationEnabled=false;
        var documentSetting=database.collection('settings').doc("document_verification_settings");
        await documentSetting.get().then(async function(snapshots) {
            var data=snapshots.data();
            if(data.isRestaurantVerification==true) {
                verificationEnabled=true;
            }
        });
                    await database.collection('users').where('vendorID','==',vendorId).get().then(async function(snapshot) {
            if(snapshot.docs.length>0) {
                var userData=snapshot.docs[0].data();
                if(verificationEnabled==true) {
                    if(userData.isDocumentVerify==true) {
                        documentVerify=true;
                    }
                }else{
                    documentVerify=true;
                }

                var subscriptionModel=false;
                var subscriptionBusinessModel=database.collection('settings').doc("restaurant");
                await subscriptionBusinessModel.get().then(async function(snapshots) {
                    var subscriptionSetting=snapshots.data();
                    if(subscriptionSetting.subscription_model==true) {
                        subscriptionModel=true;
                    }
                });
                if(subscriptionModel) {
                    if(userData.hasOwnProperty('subscription_plan')) {
                        itemLimit=userData.subscription_plan.itemLimit;
                    }
                }
                data={
                    'itemLimit': itemLimit,
                    'documentVerify': documentVerify
                }
            }
        })
        return data;
    }
    var storageRef=firebase.storage().ref('images');
    function handleFileSelect(evt) {
        var f=evt.target.files[0];
        var reader=new FileReader();
        new Compressor(f,{
            quality: <?php echo env('IMAGE_COMPRESSOR_QUALITY', 0.8); ?>,
            success(result) {
                f=result;
                reader.onload=(function(theFile) {
                    return function(e) {
                        var filePayload=e.target.result;
                        var val=f.name;
                        var ext=val.split('.')[1];
                        var docName=val.split('fakepath')[1];
                        var filename=(f.name).replace(/C:\\fakepath\\/i,'')
                        var timestamp=Number(new Date());
                        var filename=filename.split('.')[0]+"_"+timestamp+'.'+ext;
                        var uploadTask=storageRef.child(filename).put(theFile);
                        uploadTask.on('state_changed',function(snapshot) {
                            var progress=(snapshot.bytesTransferred/snapshot
                                .totalBytes)*100;
                            console.log('Upload is '+progress+'% done');
                            jQuery("#uploding_image").text("Image is uploading...");
                        },function(error) {},function() {
                            uploadTask.snapshot.ref.getDownloadURL().then(function(
                                downloadURL) {
                                jQuery("#uploding_image").text(
                                    "Upload is completed");
                                photo=downloadURL;
                                $(".product_image").empty()
                                $(".product_image").append(
                                    '<img class="rounded" style="width:50px" src="'+
                                    photo+'" alt="image">');
                            });
                        });
                    };
                })(f);
                reader.readAsDataURL(f);
            },
            error(err) {
                console.log(err.message);
            },
        });
    }
    function handleFileSelectProduct(evt) {
        var f=evt.target.files[0];
        var reader=new FileReader();
        reader.onload=(function(theFile) {
            return function(e) {
                var filePayload=e.target.result;
                var val=f.name;
                var ext=val.split('.')[1];
                var docName=val.split('fakepath')[1];
                var filename=(f.name).replace(/C:\\fakepath\\/i,'')
                var timestamp=Number(new Date());
                var filename=filename.split('.')[0]+"_"+timestamp+'.'+ext;
                var uploadTask=storageRef.child(filename).put(theFile);
                uploadTask.on('state_changed',function(snapshot) {
                    var progress=(snapshot.bytesTransferred/snapshot.totalBytes)*100;
                    console.log('Upload is '+progress+'% done');
                    jQuery("#uploding_image").text("Image is uploading...");
                },function(error) {},function() {
                    uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                        jQuery("#uploding_image").text("Upload is completed");
                        photo=downloadURL;
                        $(".product_image").empty()
                        $(".product_image").append(
                            '<img class="rounded" style="width:50px" src="'+
                            photo+'" alt="image">');
                    });
                });
            };
        })(f);
        reader.readAsDataURL(f);
    }
    function addOneFunction() {
        $("#add_ones_div").show();
        $(".save_add_one_btn").show();
    }
    function addProductSpecificationFunction() {
        $("#add_product_specification_div").show();
        $(".save_product_specification_btn").show();
    }
    function deleteProductSpecificationSingle(index) {
        delete product_specification[index];
        $("#add_product_specification_iteam_"+index).hide();
    }
    function saveProductSpecificationFunction() {
        var optionlabel=$(".add_label").val();
        var optionvalue=$(".add_value").val();
        $(".add_label").val('');
        $(".add_value").val('');
        if(optionlabel!=''&&optionlabel!='') {
            product_specification[optionlabel]=optionvalue;
            $(".product_specification").append(
                '<div class="row" style="margin-top:5px;" id="add_product_specification_iteam_'+optionlabel+
                '"><div class="col-5"><input class="form-control" type="text" value="'+optionlabel+
                '" disabled ></div><div class="col-5"><input class="form-control" type="text" value="'+
                optionvalue+
                '" disabled ></div><div class="col-2"><button class="btn" type="button" onclick=deleteProductSpecificationSingle("'+
                optionlabel+'")><span class="mdi mdi-delete"></span></button></div></div>');
        } else {
            alert("Please enter Label and Value");
        }
    }
    function saveAddOneFunction() {
        var optiontitle=$(".add_ons_title").val();
        var optionPrice=$(".add_ons_price").val();
        $(".add_ons_price").val('');
        $(".add_ons_title").val('');
        if(optiontitle!=''&&optionPrice!='') {
            addOnesPrice.push(optionPrice.toString());
            addOnesTitle.push(optiontitle);
            var index=addOnesTitle.length-1;
            $(".add_ons_list").append('<div class="row" style="margin-top:5px;" id="add_ones_list_iteam_'+index+
                '"><div class="col-5"><input class="form-control" type="text" value="'+optiontitle+
                '" disabled ></div><div class="col-5"><input class="form-control" type="text" value="'+
                optionPrice+
                '" disabled ></div><div class="col-2"><button class="btn" type="button" onclick="deleteAddOnesSingle('+
                index+')"><span class="mdi mdi-delete"></span></button></div></div>');
        } else {
            alert("Please enter Title and Price");
        }
    }
    function deleteAddOnesSingle(index) {
        addOnesTitle.splice(index,1);
        addOnesPrice.splice(index,1);
        $("#add_ones_list_iteam_"+index).hide();
    }
    $(document).on("click",".remove-btn",function() {
        photo="";
        $(".product_image").empty();
        $("#product_image").val('');
    });
    async function storeVariantImageData() {
        var newPhoto=[];
        if(variant_photos.length>0) {
            await Promise.all(variant_photos.map(async (variantPhoto,index) => {
                variantPhoto=variantPhoto.replace(/^data:image\/[a-z]+;base64,/,"");
                var uploadTask=await storageRef.child(variant_filename[index]).putString(
                    variantPhoto,'base64',{
                    contentType: 'image/jpg'
                });
                var downloadURL=await uploadTask.ref.getDownloadURL();
                $('[id="variant_'+variant_vIds[index]+'_url"]').val(downloadURL);
                newPhoto.push(downloadURL);
            }));
        }
        return newPhoto;
    }
    function handleVariantFileSelect(evt,vid) {
        var f=evt.target.files[0];
        var reader=new FileReader();
        reader.onload=(function(theFile) {
            return function(e) {
                var filePayload=e.target.result;
                var val=f.name;
                var ext=val.split('.')[1];
                var docName=val.split('fakepath')[1];
                var timestamp=Number(new Date());
                var filename=(f.name).replace(/C:\\fakepath\\/i,'')
                var filename='variant_'+vid+'_'+timestamp+'.'+ext;
                variant_filename.push(filename);
                variant_photos.push(filePayload);
                variant_vIds.push(vid);
                $('[id="variant_'+vid+'_image"]').empty();
                $('[id="variant_'+vid+'_image"]').html('<img class="rounded" style="width:50px" src="'+
                    filePayload+'" alt="image"><i class="mdi mdi-delete" data-variant="'+vid+
                    '" data-img="'+filePayload+'" data-file="'+filename+'"></i>');
                $('#upload_'+vid).attr('data-img',filePayload);
                $('#upload_'+vid).attr('data-file',filename);
            };
        })(f);
        reader.readAsDataURL(f);
    }
    $("#food_restaurant").change(function() {
        $("#attributes_div").show();
        $("#item_attribute_chosen").css({
            'width': '100%'
        });
        var selected_vendor=this.value;
    });
    function change_categories(selected_vendor) {
        restaurant_list.forEach((vendor) => {
            if(vendor.id==selected_vendor) {
                $('#item_category').html('');
                $('#item_category').append($('<option value="">{{ trans('lang.select_category') }}</option>'));
                categories_list.forEach((data) => {
                    if(data.id) {
                        $('#food_category').html($("<option></option>")
                            .attr("value",data.id)
                            .text(data.title));
                    }
                })
            }
        });
    }
    function selectAttribute() {
        var html='';
        $("#item_attribute").find('option:selected').each(function() {
            html+='<div class="row">';
            html+='<div class="col-md-3">';
            html+='<label>'+$(this).text()+'</label>';
            html+='</div>';
            html+='<div class="col-lg-9">';
            html+='<input type="text" class="form-control" id="attribute_options_'+$(this).val()+
                '" placeholder="Add attribute values" data-role="tagsinput" onchange="variants_update()">';
            html+='</div>';
            html+='</div>';
        });

        // if ($("#item_attribute option:selected").length === 0) {
        //     item_attribute = [];
        //     $("#item_attributes").hide();
        //     $("#item_variants").hide();
        // }
        // else
        // {
        //     $("#item_attributes").show();
        //     $("#item_variants").show();
        // }
        $("#item_attributes").html(html);
        $("#item_attributes input[data-role=tagsinput]").tagsinput();
        $("#attributes").val('');
        $("#variants").val('');
        $("#item_variants").html('');
    }
    function variants_update() {
        var html='';
        variant_photos=[];
        variant_vIds=[];
        variant_filename=[];
        var item_attribute=$("#item_attribute").map(function(idx,ele) {
            return $(ele).val();
        }).get();

        var attributes=[];
        var attributeSet=[];

        if(item_attribute.length>0) {

            $.each(item_attribute,function(index,attribute) {
                var attribute_options=$("#attribute_options_"+attribute).val();
                if(attribute_options) {
                    var attribute_options=attribute_options.split(',');
                    attribute_options=$.map(attribute_options,function(value) {
                        return value.replace(/[^0-9a-zA-Z a]/g,'');
                    });
                    attributeSet.push(attribute_options);
                    attributes.push({
                        'attribute_id': attribute,
                        'attribute_options': attribute_options
                    });
                }
            });


            $('#attributes').val(JSON.stringify(attributes));
            var variants=getCombinations(attributeSet);
            $('#variants').val(JSON.stringify(variants));
            if(attributeSet.length>0) {

                html+='<table class="table table-bordered">';
                html+='<thead class="thead-light">';
                html+='<tr>';
                html+='<th class="text-center"><span class="control-label">Variant</span></th>';
                html+='<th class="text-center"><span class="control-label">Variant Price</span></th>';
                html+='<th class="text-center"><span class="control-label">Variant Quantity</span></th>';
                html+='<th class="text-center"><span class="control-label">Variant Image</span></th>';
                html+='</tr>';
                html+='</thead>';
                html+='<tbody>';
                $.each(variants,function(index,variant) {
                    var check_variant_price=$('#price_'+variant).val()? $('#price_'+variant).val():1;
                    var check_variant_qty=$('#qty_'+variant).val()? $('#qty_'+variant).val():-1;
                    html+='<tr>';
                    html+='<td><label for="" class="control-label">'+variant+'</label></td>';
                    html+='<td>';
                    html+='<input type="number" id="price_'+variant+'" value="'+check_variant_price+
                        '" min="0" class="form-control">';
                    html+='</td>';
                    html+='<td>';
                    html+='<input type="number" id="qty_'+variant+'" value="'+check_variant_qty+
                        '" min="-1" class="form-control">';
                    html+='</td>';
                    html+='<td>';
                    html+='<div class="variant-image">';
                    html+='<div class="upload">';
                    html+='<div class="image" id="variant_'+variant+'_image"></div>';
                    html+='<div class="icon"><i class="mdi mdi-cloud-upload" data-variant="'+variant+
                        '"></i></div>';
                    html+='</div>';
                    html+='<div id="variant_'+variant+'_process"></div>';
                    html+='<div class="input-file">';
                    html+='<input type="file" id="file_'+variant+
                        '" onChange="handleVariantFileSelect(event,\''+variant+
                        '\')" class="form-control" style="display:none;">';
                    html+='<input type="hidden" id="variant_'+variant+'_url" value="">';
                    html+='</div>';
                    html+='</div>';
                    html+='</td>';
                    html+='</tr>';
                });
                html+='</tbody>';
                html+='</table>';
            }
        }
        $("#item_variants").html(html);
    }

    function getCombinations(arr) {
        if(arr.length) {
            if(arr.length==1) {
                return arr[0];
            } else {
                var result=[];
                var allCasesOfRest=getCombinations(arr.slice(1));
                for(var i=0;i<allCasesOfRest.length;i++) {
                    for(var j=0;j<arr[0].length;j++) {
                        result.push(arr[0][j]+'-'+allCasesOfRest[i]);
                    }
                }
                return result;
            }
        }
    }
    function uniqid(prefix="",random=false) {
        const sec=Date.now()*1000+Math.random()*1000;
        const id=sec.toString(16).replace(/\./g,"").padEnd(14,"0");
        return `${prefix}${id}${random? `.${Math.trunc(Math.random()*100000000)}`:""}`;
    }
    async function storeProductImageData() {
        if(photo && photo.startsWith('data:image')) {
            var base64Data = photo.replace(/^data:image\/[a-z]+;base64,/, "");
            var timestamp = Number(new Date());
            var filename = 'product_' + timestamp + '.jpg';
            var uploadTask = await storageRef.child(filename).putString(base64Data, 'base64', {
                contentType: 'image/jpg'
            });
            var downloadURL = await uploadTask.ref.getDownloadURL();
            return downloadURL;
        }
        return photo || '';
    }

    // Store option image data to Firebase storage (same approach as main product image)
    async function storeOptionImageData(base64Image, optionId) {
        if(base64Image && base64Image.startsWith('data:image')) {
            var base64Data = base64Image.replace(/^data:image\/[a-z]+;base64,/, "");
            var timestamp = Number(new Date());
            var filename = 'option_' + optionId + '_' + timestamp + '.jpg';
            var uploadTask = await storageRef.child(filename).putString(base64Data, 'base64', {
                contentType: 'image/jpg'
            });
            var downloadURL = await uploadTask.ref.getDownloadURL();
            return downloadURL;
        }
        return base64Image || '';
    }
    $("#product_image").resizeImg({
        callback: function(base64str) {
            photo = base64str;
            $(".product_image").empty();
            $(".product_image").append(
                '<span class="image-item" id="photo_1">' +
                '<span class="remove-btn" data-id="1" data-img="' + base64str + '">' +
                '<i class="fa fa-remove"></i></span>' +
                '<img class="rounded" width="50px" id="" height="auto" src="' + base64str + '">' +
                '</span>'
            );
            $("#product_image").val('');
        }
    });

    // Add direct file input handler as fallback
    $("#product_image").on('change', function() {
        if (this.files && this.files[0]) {
            var file = this.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var base64str = e.target.result;
                photo = base64str;
                $(".product_image").empty();
                $(".product_image").append(
                    '<span class="image-item" id="photo_1">' +
                    '<span class="remove-btn" data-id="1" data-img="' + base64str + '">' +
                    '<i class="fa fa-remove"></i></span>' +
                    '<img class="rounded" width="50px" id="" height="auto" src="' + base64str + '">' +
                    '</span>'
                );
                $("#product_image").val('');
            };
            reader.readAsDataURL(file);
        }
    });
    function handleVariantSelect(vid) {
        $("#file_"+vid).resizeImg({
            callback: function(base64str) {
                var val=$("#file_"+vid).val().toLowerCase();
                var ext=val.split('.')[1];
                var docName=val.split('fakepath')[1];
                var filename=$("#file_"+vid).val().replace(/C:\\fakepath\\/i,'')
                var timestamp=Number(new Date());
                var filename='variant_'+vid+'_'+timestamp+'.'+ext;
                //upload base64str encoded string as a image to firebase
                var uploadTask=storageRef.child(filename).putString(base64str.split(',')[1],"base64",{
                    contentType: 'image/'+ext
                })
                uploadTask.on('state_changed',function(snapshot) {
                    var progress=(snapshot.bytesTransferred/snapshot.totalBytes)*100;
                },function(error) {},function() {
                    uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                        var oldurl=$('[id="variant_'+vid+'_url"]').val();
                        if(oldurl) {
                            firebase.storage().refFromURL(oldurl).delete();
                        }
                        $('[id="variant_'+vid+'_process"]').text("Upload is completed");
                        $('[id="variant_'+vid+'_image"]').empty();
                        $('[id="variant_'+vid+'_url"]').val(downloadURL);
                        $('[id="variant_'+vid+'_image"]').html(
                            '<img class="rounded" style="width:50px" src="'+
                            downloadURL+
                            '" alt="image"><i class="mdi mdi-delete" data-variant="'+
                            vid+'"></i>');
                        setTimeout(function() {
                            $('[id="variant_'+vid+'_process"]').empty();
                        },1000);
                    });
                });
            }
        });
    }

// Category search and multi-select tag functionality for food categories
    $(document).ready(function() {
        // Fix quantity input to prevent number rendering issues
        $('.item_quantity').on('input', function() {
            var value = parseInt($(this).val()) || -1;
            if (value < -1) {
                $(this).val(-1);
            }
        });

        // 1. Filter dropdown options based on search
        $('#food_category_search').on('keyup', function() {
        var search = $(this).val().toLowerCase();
        $('#food_category option').each(function() {
            if ($(this).val() === "") {
                $(this).show();
                return;
            }
            var text = $(this).text().toLowerCase();
            if (text.indexOf(search) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // 2. When selecting from dropdown, add tag (multi-select support)
    $('#food_category').on('change', function() {
        updateSelectedFoodCategoryTags();
        // Filter subcategories based on selected categories
        filterSubcategoriesByCategories();
    });

    // 3. Remove tag and unselect in dropdown
    $('#selected_categories').on('click', '.remove-tag', function() {
        var value = $(this).parent().data('value');
        $('#food_category option[value="' + value + '"]').prop('selected', false);
        updateSelectedFoodCategoryTags();
        // Filter subcategories based on selected categories
        filterSubcategoriesByCategories();
    });
});

// 4. Update tags display
function updateSelectedFoodCategoryTags() {
    var selected = $('#food_category').val() || [];
    var html = '';
    $('#food_category option:selected').each(function() {
        if ($(this).val() !== "") {
            html += '<span class="selected-category-tag" data-value="' + $(this).val() + '">' +
                $(this).text() +
                '<span class="remove-tag">&times;</span></span>';
        }
    });
    $('#selected_categories').html(html);
}

// Filter subcategories based on selected categories
function filterSubcategoriesByCategories() {
    var selectedCategories = $('#food_category').val() || [];
    console.log('🔍 Filtering subcategories for selected categories:', selectedCategories);

    // Show all subcategories if no categories selected
    if (selectedCategories.length === 0) {
        $('#food_subcategory option').show();
        return;
    }

    $('#food_subcategory option').each(function() {
        var $option = $(this);
        var parentCategoryId = $option.attr('data-parent');

        if ($option.val() === "") {
            // Always show the placeholder option
            $option.show();
        } else if (parentCategoryId && selectedCategories.includes(parentCategoryId)) {
            // Show subcategory if its parent category is selected
            $option.show();
            console.log('✅ Showing subcategory:', $option.text(), 'for parent:', parentCategoryId);
        } else {
            // Hide subcategory if its parent category is not selected
            $option.hide();
            console.log('❌ Hiding subcategory:', $option.text(), 'for parent:', parentCategoryId);
        }
    });

    // Clear selected subcategories that are no longer visible
    $('#food_subcategory option:selected').each(function() {
        if (!$(this).is(':visible') && $(this).val() !== "") {
            $(this).prop('selected', false);
            console.log('🗑️ Deselected hidden subcategory:', $(this).text());
        }
    });

    updateSelectedSubcategoryTags();
}

// Subcategory search and multi-select tag functionality
$(document).ready(function() {
    // 1. Filter dropdown options based on search
    $('#food_subcategory_search').on('keyup', function() {
        var search = $(this).val().toLowerCase();
        $('#food_subcategory option').each(function() {
            if ($(this).val() === "") {
                $(this).show();
                return;
            }
            var text = $(this).text().toLowerCase();
            if (text.indexOf(search) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // 2. When selecting from dropdown, add tag (multi-select support)
    $('#food_subcategory').on('change', function() {
        updateSelectedSubcategoryTags();
    });

    // 3. Remove tag and unselect in dropdown
    $('#selected_subcategories').on('click', '.remove-tag', function() {
        var value = $(this).parent().data('value');
        $('#food_subcategory option[value="' + value + '"]').prop('selected', false);
        updateSelectedSubcategoryTags();
    });
});

// Function to update section from selected subcategory
function updateSectionFromSubcategory() {
    var selectedSubcategory = $('#food_subcategory').val();
    if (selectedSubcategory && selectedSubcategory.length > 0) {
        // Get the first selected subcategory
        var subcategoryId = Array.isArray(selectedSubcategory) ? selectedSubcategory[0] : selectedSubcategory;

        if (subcategoryId && subcategoryId !== '') {
            console.log('🔍 Fetching section for subcategory ID:', subcategoryId);

            // Find the subcategory's parent from the dropdown option
            var parentCategoryId = $('#food_subcategory option[value="' + subcategoryId + '"]').attr('data-parent');
            console.log('📋 Parent category ID from option:', parentCategoryId);

            if (parentCategoryId) {
                // Find the parent category from the categories list
                var parentCategory = categories_list.find(function(cat) {
                    return cat.id === parentCategoryId;
                });
                
                if (parentCategory) {
                    var section = parentCategory.section || 'General';
                    $('#section_info').val(section);
                    console.log('✅ Section updated to:', section);
                } else {
                    console.warn('⚠️ Parent category not found in list');
                    $('#section_info').val('General');
                }
            } else {
                console.warn('⚠️ Subcategory has no parent category');
                $('#section_info').val('General');
            }
        } else {
            $('#section_info').val('');
        }
    } else {
        $('#section_info').val('');
    }
}

function updateSelectedSubcategoryTags() {
    var selected = $('#food_subcategory').val() || [];
    var html = '';
    $('#food_subcategory option:selected').each(function() {
        if ($(this).val() !== "") {
            html += '<span class="selected-subcategory-tag" data-value="' + $(this).val() + '">' +
                $(this).text() +
                '<span class="remove-tag">&times;</span></span>';
        }
    });
    $('#selected_subcategories').html(html);

    // Update section when subcategory tags change
    updateSectionFromSubcategory();
}

// Options Management Functions
let optionsList = [];
let currentOptionId = null;

// Toggle options section
$(document).ready(function() {
    $('.has_options').change(function() {
        if ($(this).is(':checked')) {
            $('#options_config').show();
            $('#options_fieldset').show();
        } else {
            $('#options_config').hide();
            $('#options_fieldset').hide();
            $('.options-list').empty();
            optionsList = [];
            updateOptionsSummary();
        }
    });
});

function addNewOption() {
    const optionId = 'option_' + Date.now();
    const template = $('#option_template .option-item').clone();

    template.attr('data-option-id', optionId);
    template.find('.option-number').text(optionsList.length + 1);

    // Update checkbox IDs to be unique
    template.find('.option-available').attr('id', 'option_available_' + optionId);
    template.find('.option-featured').attr('id', 'option_featured_' + optionId);
    template.find('label[for="option_available_"]').attr('for', 'option_available_' + optionId);
    template.find('label[for="option_featured_"]').attr('for', 'option_featured_' + optionId);

    $('.options-list').append(template);
    template.show();

    optionsList.push({
        id: optionId,
        type: 'size',
        title: $('.food_name').val() || '', // Auto-fetch from main item name
        subtitle: '',
        unit_price: 0, // UI input field
        original_unit_price: 0, // UI input field
        total_price: 0, // Calculated field
        original_total_price: 0, // Calculated field
        quantity: 0,
        quantity_unit: 'g',
        unit_measure: 100,
        unit_measure_type: 'g',
        discount_amount: 0,
        discount_percentage: 0,
        image: '',
        is_available: true,
        is_featured: false
    });

    attachOptionEventListeners(optionId);

    // Trigger calculations to populate calculated fields
    calculateOptionCalculations(optionId);

    updateOptionsSummary();
    updateDefaultOptionSelect();
}

function removeOption(button) {
    const optionItem = $(button).closest('.option-item');
    const optionId = optionItem.data('option-id');

    // Remove from array
    optionsList = optionsList.filter(opt => opt.id !== optionId);

    // Remove from DOM
    optionItem.remove();

    updateOptionNumbers();
    updateOptionsSummary();
    updateDefaultOptionSelect();
}

function attachOptionEventListeners(optionId) {
    const optionItem = $(`[data-option-id="${optionId}"]`);

    // Initialize checkbox states
    const optionData = optionsList.find(opt => opt.id === optionId);
    if (optionData) {
        optionItem.find('.option-available').prop('checked', optionData.is_available !== false);
        optionItem.find('.option-featured').prop('checked', optionData.is_featured === true);

        // Apply initial visual states
        if (optionData.is_available !== false) {
            optionItem.addClass('option-enabled');
        } else {
            optionItem.addClass('option-disabled');
        }

        if (optionData.is_featured === true) {
            optionItem.addClass('option-featured-highlight');
        }
    }

    // Update optionsList when form fields change
    // Enhanced option type change with smart defaults
    optionItem.find('.option-type').on('change', function() {
        const optionType = $(this).val();
        updateOptionInList(optionId, 'type', optionType);

        // Get smart defaults for this option type
        const defaults = getOptionTypeDefaults(optionType);

        // Auto-suggest compatible units
        const unitSelect = optionItem.find('.option-quantity-unit');
        unitSelect.empty();
        defaults.suggested_units.forEach(unit => {
            const unitLabels = {
                'g': 'Grams (g)', 'kg': 'Kilograms (kg)', 'mg': 'Milligrams (mg)',
                'L': 'Liters (L)', 'ml': 'Milliliters (ml)', 'cl': 'Centiliters (cl)',
                'pcs': 'Pieces (pcs)', 'units': 'Units', 'dozen': 'Dozen',
                'custom': 'Custom'
            };
            unitSelect.append(`<option value="${unit}">${unitLabels[unit] || unit}</option>`);
        });
        unitSelect.val(defaults.default_unit);

        // Auto-suggest unit measure
        optionItem.find('.option-unit-measure').val(defaults.unit_measure);

        // Update option data
        updateOptionInList(optionId, 'quantity_unit', defaults.default_unit);
        updateOptionInList(optionId, 'unit_measure', defaults.unit_measure);

        // Trigger calculations
        calculateOptionCalculations(optionId);
    });

    // Auto-fetch title from main item name
    optionItem.find('.option-title').val($('.food_name').val() || '');
    updateOptionInList(optionId, 'title', $('.food_name').val() || '');

    // Listen for main item name changes to update all option titles
    $('.food_name').on('input', function() {
        $('.option-title').val($(this).val());
        optionsList.forEach(opt => {
            opt.title = $(this).val();
        });
        updateOptionsSummary();
        updateDefaultOptionSelect();
    });

    // Option subtitle is now read-only and auto-generated

    optionItem.find('.option-unit-price').on('input', function() {
        const unitPrice = parseFloat($(this).val()) || 0;
        updateOptionInList(optionId, 'unit_price', unitPrice);
        calculateOptionCalculations(optionId);
        updateOptionsSummary();
    });

    optionItem.find('.option-original-unit-price').on('input', function() {
        const originalUnitPrice = parseFloat($(this).val()) || 0;
        updateOptionInList(optionId, 'original_unit_price', originalUnitPrice);
        calculateOptionCalculations(optionId);
    });

    optionItem.find('.option-quantity').on('input', function() {
        const quantity = parseFloat($(this).val()) || 0;
        updateOptionInList(optionId, 'quantity', quantity);
        calculateOptionCalculations(optionId);
    });

    // Enhanced quantity unit change with smart defaults
    optionItem.find('.option-quantity-unit').on('change', function() {
        const unit = $(this).val();
        updateOptionInList(optionId, 'quantity_unit', unit);
        updateOptionInList(optionId, 'unit_measure_type', unit);

        // Auto-suggest unit measure base
        const smartBase = getSmartUnitMeasureBase(unit);
        optionItem.find('.option-unit-measure').val(smartBase.base);
        updateOptionInList(optionId, 'unit_measure', smartBase.base);

        calculateOptionCalculations(optionId);
    });

    optionItem.find('.option-unit-measure').on('input', function() {
        const unitMeasure = parseFloat($(this).val()) || 100;
        updateOptionInList(optionId, 'unit_measure', unitMeasure);
        calculateOptionCalculations(optionId);
    });

    optionItem.find('.option-available').on('change', function() {
        const isChecked = $(this).is(':checked');
        console.log('🔍 Option Available changed for', optionId, ':', isChecked);
        updateOptionInList(optionId, 'is_available', isChecked);

        // Visual feedback
        if (isChecked) {
            $(this).closest('.option-item').removeClass('option-disabled').addClass('option-enabled');
        } else {
            $(this).closest('.option-item').removeClass('option-enabled').addClass('option-disabled');
        }
    });

    optionItem.find('.option-featured').on('change', function() {
        const isFeatured = $(this).is(':checked');
        console.log('🔍 Option Featured changed for', optionId, ':', isFeatured);
        updateOptionInList(optionId, 'is_featured', isFeatured);

        if (isFeatured) {
            // Uncheck other featured options
            $('.option-featured').not(this).prop('checked', false);
            optionsList.forEach(opt => {
                if (opt.id !== optionId) {
                    opt.is_featured = false;
                }
            });

            // Visual feedback
            $('.option-item').removeClass('option-featured-highlight');
            $(this).closest('.option-item').addClass('option-featured-highlight');
        } else {
            $(this).closest('.option-item').removeClass('option-featured-highlight');
        }

        updateOptionsSummary();
        updateDefaultOptionSelect();
    });

    // Image upload
    optionItem.find('.option-image-input').change(function() {
        handleOptionImageUpload(this, optionId);
    });
}

function updateOptionInList(optionId, field, value) {
    const optionIndex = optionsList.findIndex(opt => opt.id === optionId);
    if (optionIndex !== -1) {
        optionsList[optionIndex][field] = value;
    }
}

// Smart Auto-generation Functions
function autoGenerateTitle(optionData) {
    const { quantity, quantity_unit, option_type, unit_measure } = optionData;

    switch(option_type) {
        case 'pack':
            return `Pack of ${quantity} (${unit_measure}${quantity_unit} each)`;
        case 'bundle':
            return `Bundle - ${quantity} ${quantity_unit}`;
        case 'size':
        case 'volume':
            return `${quantity}${quantity_unit} Pack`;
        case 'quantity':
            return `${quantity} ${quantity_unit}`;
        case 'addon':
            return `Add-on: ${quantity} ${quantity_unit}`;
        case 'variant':
            return `Variant: ${quantity} ${quantity_unit}`;
        default:
            return `${quantity} ${quantity_unit}`;
    }
}

function autoGenerateSubtitle(optionData) {
    const { quantity, quantity_unit, option_type, unit_measure } = optionData;

    switch(option_type) {
        case 'pack':
            const totalQuantity = quantity * unit_measure;
            return `${unit_measure}${quantity_unit} × ${quantity} = ${totalQuantity}${quantity_unit} total`;
        case 'bundle':
            return `Mixed items bundle - ${quantity} pieces`;
        case 'size':
        case 'volume':
            return `Single unit: ${quantity}${quantity_unit}`;
        case 'quantity':
            return `Individual pieces`;
        case 'addon':
            return `Extra item`;
        case 'variant':
            return `Special variant`;
        default:
            return `${quantity}${quantity_unit}`;
    }
}

// Smart Validation System
function validateAndEnhanceOption(optionData) {
    const errors = [];
    const warnings = [];
    const autoCorrections = {};

    // Price validation
    if (optionData.price > optionData.original_price) {
        errors.push('Price cannot be greater than Original Price');
    }

    // Quantity validation
    if (optionData.quantity < 0) {
        errors.push('Quantity cannot be negative');
        autoCorrections.quantity = 0;
    }

    // Auto-disable when quantity is 0
    if (optionData.quantity === 0) {
        autoCorrections.is_available = false;
        warnings.push('Option automatically disabled due to zero quantity');
    }

    // Low stock warning
    if (optionData.quantity > 0 && optionData.quantity < 10) {
        warnings.push('Low stock warning: Only ' + optionData.quantity + ' units remaining');
    }

    // Unit compatibility check
    const compatibleUnits = getCompatibleUnits(optionData.option_type);
    if (!compatibleUnits.includes(optionData.quantity_unit)) {
        warnings.push(`Unit "${optionData.quantity_unit}" may not be ideal for "${optionData.option_type}" type`);
    }

    return { errors, warnings, autoCorrections };
}

function getCompatibleUnits(optionType) {
    const unitMap = {
        'size': ['g', 'kg', 'mg'],
        'volume': ['L', 'ml', 'cl'],
        'quantity': ['pcs', 'units'],
        'pack': ['pcs', 'units', 'dozen'],
        'bundle': ['pcs', 'units', 'custom'],
        'addon': ['pcs', 'units', 'custom'],
        'variant': ['pcs', 'units', 'custom']
    };
    return unitMap[optionType] || ['pcs', 'units'];
}

// Smart Defaults System
function getSmartUnitMeasureBase(quantity_unit) {
    const smartDefaults = {
        'g': { base: 100, description: 'per 100g' },
        'kg': { base: 1, description: 'per kg' },
        'mg': { base: 1000, description: 'per 1000mg' },
        'L': { base: 1, description: 'per liter' },
        'ml': { base: 100, description: 'per 100ml' },
        'cl': { base: 10, description: 'per 10cl' },
        'pcs': { base: 1, description: 'per piece' },
        'units': { base: 1, description: 'per unit' },
        'dozen': { base: 12, description: 'per dozen' },
        'custom': { base: 100, description: 'per 100 units' }
    };

    return smartDefaults[quantity_unit] || { base: 100, description: 'per 100 units' };
}

function getOptionTypeDefaults(optionType) {
    const defaults = {
        'size': {
            suggested_units: ['g', 'kg'],
            default_unit: 'g',
            unit_measure: 100,
            description: 'Weight-based options'
        },
        'volume': {
            suggested_units: ['L', 'ml'],
            default_unit: 'ml',
            unit_measure: 100,
            description: 'Volume-based options'
        },
        'quantity': {
            suggested_units: ['pcs', 'units'],
            default_unit: 'pcs',
            unit_measure: 1,
            description: 'Count-based options'
        },
        'pack': {
            suggested_units: ['pcs', 'dozen'],
            default_unit: 'pcs',
            unit_measure: 1,
            description: 'Packaged options'
        },
        'bundle': {
            suggested_units: ['pcs', 'custom'],
            default_unit: 'pcs',
            unit_measure: 1,
            description: 'Mixed item bundles'
        },
        'addon': {
            suggested_units: ['pcs', 'units'],
            default_unit: 'pcs',
            unit_measure: 1,
            description: 'Additional items'
        },
        'variant': {
            suggested_units: ['pcs', 'units'],
            default_unit: 'pcs',
            unit_measure: 1,
            description: 'Product variants'
        }
    };

    return defaults[optionType] || defaults['quantity'];
}

// Enhanced Calculation Engine - Unit Price Input Logic
function calculateOptionCalculations(optionId) {
    const optionItem = $(`[data-option-id="${optionId}"]`);
    const unitPrice = parseFloat(optionItem.find('.option-unit-price').val()) || 0;
    const originalUnitPrice = parseFloat(optionItem.find('.option-original-unit-price').val()) || 0;
    const quantity = parseFloat(optionItem.find('.option-quantity').val()) || 0;
    const unitMeasure = parseFloat(optionItem.find('.option-unit-measure').val()) || 100;
    const quantityUnit = optionItem.find('.option-quantity-unit').val() || 'g';
    const optionType = optionItem.find('.option-type').val();

    // Calculate total prices from unit prices
    const totalPrice = unitPrice * quantity;
    const originalTotalPrice = originalUnitPrice * quantity;

    // Calculate discount
    let discountAmount = 0;
    let discountPercentage = 0;

    if (originalTotalPrice > 0 && originalTotalPrice > totalPrice) {
        discountAmount = originalTotalPrice - totalPrice;
        discountPercentage = ((discountAmount / originalTotalPrice) * 100);
    }

    // Update calculated fields
    optionItem.find('.option-total-price').val(totalPrice.toFixed(2));
    optionItem.find('.option-original-total-price').val(originalTotalPrice.toFixed(2));
    optionItem.find('.option-discount-amount').val(discountAmount.toFixed(2));
    optionItem.find('.option-discount-percentage').val(discountPercentage.toFixed(2));

    // Update Savings Display
    let savingsDisplay = '';
    if (discountAmount > 0) {
        savingsDisplay = `Save ₹${discountAmount.toFixed(2)} (${discountPercentage.toFixed(1)}%)`;
    }
    optionItem.find('.option-savings-display').val(savingsDisplay);

    // Auto-generate subtitle in format: unit_measure + quantity_unit + ' x ' + quantity
    // Remove unnecessary .00 decimals for cleaner display
    const formattedUnitMeasure = unitMeasure % 1 === 0 ? unitMeasure.toString() : unitMeasure.toFixed(2);
    const subtitle = `${formattedUnitMeasure}${quantityUnit} x ${quantity}`;
    optionItem.find('.option-subtitle').val(subtitle);

    // Update in optionsList
    updateOptionInList(optionId, 'unit_price', unitPrice);
    updateOptionInList(optionId, 'original_unit_price', originalUnitPrice);
    updateOptionInList(optionId, 'total_price', totalPrice);
    updateOptionInList(optionId, 'original_total_price', originalTotalPrice);
    updateOptionInList(optionId, 'discount_amount', discountAmount);
    updateOptionInList(optionId, 'discount_percentage', discountPercentage);
    updateOptionInList(optionId, 'subtitle', subtitle);

    // Show validation feedback
    const optionData = {
        unit_price: unitPrice,
        original_unit_price: originalUnitPrice,
        quantity: quantity,
        quantity_unit: quantityUnit,
        option_type: optionType,
        unit_measure: unitMeasure,
        total_price: totalPrice,
        original_total_price: originalTotalPrice,
        discount_amount: discountAmount,
        discount_percentage: discountPercentage
    };

    showValidationFeedback(optionId, optionData);
}

// Validation Feedback Display
function showValidationFeedback(optionId, optionData) {
    const optionItem = $(`[data-option-id="${optionId}"]`);
    const validation = validateAndEnhanceOption(optionData);

    // Clear previous feedback
    optionItem.find('.validation-feedback').remove();

    // Create feedback container
    let feedbackHtml = '<div class="validation-feedback mt-2">';

    // Show errors
    if (validation.errors.length > 0) {
        feedbackHtml += '<div class="alert alert-danger alert-sm">';
        feedbackHtml += '<i class="mdi mdi-alert-circle"></i> <strong>Errors:</strong><ul class="mb-0">';
        validation.errors.forEach(error => {
            feedbackHtml += `<li>${error}</li>`;
        });
        feedbackHtml += '</ul></div>';
    }

    // Show warnings
    if (validation.warnings.length > 0) {
        feedbackHtml += '<div class="alert alert-warning alert-sm">';
        feedbackHtml += '<i class="mdi mdi-alert"></i> <strong>Warnings:</strong><ul class="mb-0">';
        validation.warnings.forEach(warning => {
            feedbackHtml += `<li>${warning}</li>`;
        });
        feedbackHtml += '</ul></div>';
    }

    // Show smart suggestions
    const optionType = optionData.option_type;
    if (optionType) {
        const defaults = getOptionTypeDefaults(optionType);
        feedbackHtml += '<div class="alert alert-info alert-sm">';
        feedbackHtml += '<i class="mdi mdi-lightbulb-outline"></i> <strong>Smart Suggestions:</strong>';
        feedbackHtml += `<div>${defaults.description}</div>`;
        feedbackHtml += `<div>Suggested units: ${defaults.suggested_units.join(', ')}</div>`;
        feedbackHtml += '</div>';
    }

    feedbackHtml += '</div>';

    // Append feedback after the option item
    optionItem.append(feedbackHtml);

    // Apply auto-corrections
    Object.keys(validation.autoCorrections).forEach(field => {
        const value = validation.autoCorrections[field];
        if (field === 'is_available') {
            optionItem.find('.option-available').prop('checked', value);
        } else {
            optionItem.find(`.option-${field}`).val(value);
        }
    });
}

async function handleOptionImageUpload(input, optionId) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = async function(e) {
            const optionItem = $(`[data-option-id="${optionId}"]`);
            const base64Image = e.target.result;

            // Show preview immediately
            optionItem.find('.option-image-preview').html(
                `<img src="${base64Image}" style="max-width: 100px; max-height: 100px; border-radius: 4px;">
                 <div class="mt-1"><small class="text-muted">Uploading to Firebase...</small></div>`
            );

            try {
                // Convert base64 to Firebase storage URL
                const firebaseUrl = await storeOptionImageData(base64Image, optionId);

                // Update preview with Firebase URL
                optionItem.find('.option-image-preview').html(
                    `<img src="${firebaseUrl}" style="max-width: 100px; max-height: 100px; border-radius: 4px;">
                     <div class="mt-1"><small class="text-success">✅ Uploaded successfully</small></div>`
                );

                // Store Firebase URL in optionsList (not base64)
                const optionIndex = optionsList.findIndex(opt => opt.id === optionId);
                if (optionIndex !== -1) {
                    optionsList[optionIndex].image = firebaseUrl;
                }

                console.log('✅ Option image uploaded to Firebase:', firebaseUrl);
            } catch (error) {
                console.error('❌ Error uploading option image:', error);
                optionItem.find('.option-image-preview').html(
                    `<div class="text-danger">❌ Upload failed: ${error.message}</div>`
                );
            }
        };
        reader.readAsDataURL(file);
    }
}

function updateOptionNumbers() {
    $('.option-item').each(function(index) {
        $(this).find('.option-number').text(index + 1);
    });
}

function updateOptionsSummary() {
    if (optionsList.length > 0) {
        const prices = optionsList.map(opt => opt.total_price).filter(p => p > 0);
        const minPrice = Math.min(...prices);
        const maxPrice = Math.max(...prices);

        $('.options-summary').show();
        $('.summary-content').html(`
            <div class="row">
                <div class="col-md-4">
                    <strong>Price Range:</strong> ₹${minPrice} - ₹${maxPrice}
                </div>
                <div class="col-md-4">
                    <strong>Total Options:</strong> ${optionsList.length}
                </div>
                <div class="col-md-4">
                    <strong>Featured Option:</strong> ${optionsList.find(opt => opt.is_featured)?.title || 'None'}
                </div>
            </div>
        `);
    } else {
        $('.options-summary').hide();
    }
}

function updateDefaultOptionSelect() {
    const select = $('#default_option');
    select.empty();
    select.append('<option value="">Select default option</option>');

    optionsList.forEach(option => {
        select.append(`<option value="${option.id}">${option.title}</option>`);
    });
}


</script>

<!-- Option Template (Hidden) -->
<div id="option_template" style="display:none;">
    <div class="option-item" data-option-id="">
        <div class="option-header">
            <h5>Option #<span class="option-number"></span></h5>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeOption(this)">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Option Type</label>
                    <select class="form-control option-type">
                        <option value="size">Size/Weight (kg, g, mg)</option>
                        <option value="volume">Volume (L, ml, cl)</option>
                        <option value="quantity">Quantity (pcs, units)</option>
                        <option value="pack">Pack (dozen, bundle)</option>
                        <option value="bundle">Bundle (mixed items)</option>
                        <option value="addon">Add-on (extras)</option>
                        <option value="variant">Variant (organic, premium)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Unit</label>
                    <select class="form-control option-quantity-unit">
                        <option value="g">Grams (g)</option>
                        <option value="kg">Kilograms (kg)</option>
                        <option value="mg">Milligrams (mg)</option>
                        <option value="L">Liters (L)</option>
                        <option value="ml">Milliliters (ml)</option>
                        <option value="pcs">Pieces (pcs)</option>
                        <option value="units">Units</option>
                        <option value="dozen">Dozen</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Unit Price (₹)</label>
                    <input type="number" class="form-control option-unit-price" step="0.01" min="0" placeholder="Price per unit">
                    <small class="form-text text-muted">Price per unit (will calculate total price)</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Original Unit Price (₹)</label>
                    <input type="number" class="form-control option-original-unit-price" step="0.01" min="0" placeholder="Original price per unit">
                    <small class="form-text text-muted">Original price per unit (for discount calculation)</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" class="form-control option-quantity" min="0">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Unit Measure Base</label>
                    <input type="number" class="form-control option-unit-measure" value="100">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Option Title</label>
                    <input type="text" class="form-control option-title" placeholder="Auto-filled from item name" readonly>
                    <small class="form-text text-muted">Auto-filled from main item name</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Option Subtitle</label>
                    <input type="text" class="form-control option-subtitle" placeholder="Auto-generated: unit_measure + quantity_unit + x + quantity" readonly>
                    <small class="form-text text-muted">Auto-generated format: 500ml x 2</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Total Price (₹)</label>
                    <input type="number" class="form-control option-total-price" step="0.01" readonly>
                    <small class="form-text text-muted">Auto-calculated: Unit Price × Quantity</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Original Total Price (₹)</label>
                    <input type="number" class="form-control option-original-total-price" step="0.01" readonly>
                    <small class="form-text text-muted">Auto-calculated: Original Unit Price × Quantity</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Discount Amount (₹)</label>
                    <input type="number" class="form-control option-discount-amount" step="0.01" readonly>
                    <small class="form-text text-muted">Auto-calculated: Original Total - Total Price</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Discount Percentage (%)</label>
                    <input type="number" class="form-control option-discount-percentage" step="0.01" readonly>
                    <small class="form-text text-muted">Auto-calculated discount percentage</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Savings Display</label>
                    <input type="text" class="form-control option-savings-display" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Smart Suggestions</label>
                    <div class="smart-suggestions-display">
                        <small class="text-muted">Select option type for smart defaults</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Option Image</label>
            <input type="file" class="option-image-input" accept="image/*">
            <div class="option-image-preview"></div>
        </div>

        <div class="form-check">
            <input type="checkbox" class="option-available" id="option_available_" checked>
            <label class="form-check-label" for="option_available_">Available</label>
        </div>

        <div class="form-check">
            <input type="checkbox" class="option-featured" id="option_featured_">
            <label class="form-check-label" for="option_featured_">Featured (Show first)</label>
        </div>
    </div>
</div>

<style>
.option-item {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #f9f9f9;
}

.option-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.option-header h5 {
    margin: 0;
    color: #333;
}

.option-image-preview {
    margin-top: 10px;
}

.option-image-preview img {
    border: 1px solid #ddd;
}

/* Visual feedback for option states */
.option-enabled {
    border-color: #28a745 !important;
    background: #f8fff9 !important;
}

.option-disabled {
    border-color: #dc3545 !important;
    background: #fff8f8 !important;
    opacity: 0.7;
}

.option-featured-highlight {
    border-color: #ffc107 !important;
    background: #fffdf0 !important;
    box-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
}

/* Enhanced checkbox styling */
.option-item .form-check {
    margin: 15px 0;
    padding: 10px;
    border-radius: 4px;
    background: #f8f9fa;
}

.option-item .form-check input[type="checkbox"] {
    margin-right: 8px;
    transform: scale(1.2);
}

.option-item .form-check label {
    font-weight: 500;
    cursor: pointer;
    margin-bottom: 0;
}

.option-item .form-check:hover {
    background: #e9ecef;
}

.options-summary {
    background: #e8f5e8;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    padding: 15px;
    margin-top: 20px;
}

.summary-content {
    margin: 0;
}

#options_config {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 15px;
    margin-top: 15px;
}

.selected-subcategory-tag {
    display: inline-block;
    background: #007bff;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    margin: 2px;
    font-size: 12px;
    position: relative;
}

.selected-subcategory-tag .remove-tag {
    margin-left: 5px;
    cursor: pointer;
    font-weight: bold;
}
</style>

@endsection
