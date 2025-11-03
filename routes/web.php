<?php

use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SitemapController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CuisineController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');

Route::post('payments/razorpay/createorder', [App\Http\Controllers\RazorPayController::class, 'createOrderid']);

Route::post('payments/getpaytmchecksum', [App\Http\Controllers\PaymentController::class, 'getPaytmChecksum']);

Route::post('payments/validatechecksum', [App\Http\Controllers\PaymentController::class, 'validateChecksum']);

Route::post('payments/initiatepaytmpayment', [App\Http\Controllers\PaymentController::class, 'initiatePaytmPayment']);

Route::get('payments/paytmpaymentcallback', [App\Http\Controllers\PaymentController::class, 'paytmPaymentcallback']);

Route::post('payments/paypalclientid', [App\Http\Controllers\PaymentController::class, 'getPaypalClienttoken']);

Route::post('payments/paypaltransaction', [App\Http\Controllers\PaymentController::class, 'createBraintreePayment']);

Route::post('payments/stripepaymentintent', [App\Http\Controllers\PaymentController::class, 'createStripePaymentIntent']);

Route::middleware(['permission:terms,termsAndConditions'])->group(function () {
    Route::get('termsAndConditions', [App\Http\Controllers\TermsAndConditionsController::class, 'index'])->name('termsAndConditions');
});
Route::middleware(['permission:privacy,privacyPolicy'])->group(function () {
    Route::get('privacyPolicy', [App\Http\Controllers\TermsAndConditionsController::class, 'privacyindex'])->name('privacyPolicy');
});

Route::middleware(['permission:users,users'])->group(function () {
    Route::get('/users', [App\Http\Controllers\HomeController::class, 'users'])->name('users');
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');

});
Route::middleware(['permission:users,users.edit'])->group(function () {
    Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');

});
Route::middleware(['permission:users,users.create'])->group(function () {
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');

});
Route::middleware(['permission:users,users.view'])->group(function () {
    Route::get('/users/view/{id}', [App\Http\Controllers\UserController::class, 'view'])->name('users.view');

});
Route::middleware(['permission:vendors,vendors'])->group(function () {
    Route::get('/vendors', [App\Http\Controllers\RestaurantController::class, 'vendors'])->name('vendors');
});
Route::middleware(['permission:vendors,vendors.create'])->group(function () {
    Route::get('/vendors/create', [App\Http\Controllers\RestaurantController::class, 'vendorCreate'])->name('vendors.create');

});
Route::middleware(['permission:approve_vendors,approve.vendors.list'])->group(function () {
    Route::get('/vendors/approved', [App\Http\Controllers\RestaurantController::class, 'vendors'])->name('vendors.approved');
});
Route::middleware(['permission:pending_vendors,pending.vendors.list'])->group(function () {
    Route::get('/vendors/pending', [App\Http\Controllers\RestaurantController::class, 'vendors'])->name('vendors.pending');
});
Route::middleware(['permission:restaurants,restaurants'])->group(function () {
    Route::get('/restaurants', [App\Http\Controllers\RestaurantController::class, 'index'])->name('restaurants');

});
Route::middleware(['permission:restaurants,restaurants.create'])->group(function () {
    Route::get('/restaurants/create', [App\Http\Controllers\RestaurantController::class, 'create'])->name('restaurants.create');

});
Route::middleware(['permission:restaurants,restaurants.edit'])->group(function () {
    Route::get('/restaurants/edit/{id}', [App\Http\Controllers\RestaurantController::class, 'edit'])->name('restaurants.edit');
});
Route::middleware(['permission:restaurants,restaurants.view'])->group(function () {
    Route::get('/restaurants/view/{id}', [App\Http\Controllers\RestaurantController::class, 'view'])->name('restaurants.view');

});
Route::get('/restaurants/promos/{id}', [App\Http\Controllers\RestaurantController::class, 'promos'])->name('restaurants.promos');

// Admin Impersonation Routes
Route::middleware(['permission:restaurants,restaurants.impersonate', 'impersonation.security'])->group(function () {
    Route::post('/admin/impersonate/generate-token', [App\Http\Controllers\ImpersonationController::class, 'generateToken'])->name('admin.impersonate.generate');
    Route::get('/admin/impersonate/restaurant-info', [App\Http\Controllers\ImpersonationController::class, 'getRestaurantInfo'])->name('admin.impersonate.info');
});

// Restaurant Panel Impersonation Handler (No auth required - handles custom tokens)
Route::get('/auth/impersonate', function () {
    return view('restaurant_auth_handler');
})->name('restaurant.impersonate');

// Mart Routes
Route::middleware(['permission:marts,marts'])->group(function () {
    Route::get('/marts', [App\Http\Controllers\MartController::class, 'index'])->name('marts');
    Route::get('/marts/json/{id}', [App\Http\Controllers\MartController::class, 'showJson'])->name('marts.json');
});
Route::middleware(['permission:marts,marts.create'])->group(function () {
    Route::post('/marts', [App\Http\Controllers\MartController::class, 'store'])->name('marts.store');
});
Route::middleware(['permission:marts,marts.edit'])->group(function () {
    Route::post('/marts/{id}', [App\Http\Controllers\MartController::class, 'update'])->name('marts.update');
});
Route::middleware(['permission:marts,marts.create'])->group(function () {
    Route::get('/marts/create', [App\Http\Controllers\MartController::class, 'create'])->name('marts.create');
});
Route::middleware(['permission:marts,marts.edit'])->group(function () {
    Route::get('/marts/edit/{id}', [App\Http\Controllers\MartController::class, 'edit'])->name('marts.edit');
});
Route::middleware(['permission:marts,marts.view'])->group(function () {
    Route::get('/marts/view/{id}', [App\Http\Controllers\MartController::class, 'view'])->name('marts.view');
});
Route::get('/marts/foods/{id}', [App\Http\Controllers\MartController::class, 'foods'])->name('marts.foods');
Route::get('/marts/orders/{id}', [App\Http\Controllers\MartController::class, 'orders'])->name('marts.orders');

// Restaurant Schedule Routes have been removed - auto-schedule functionality disabled

Route::middleware(['permission:coupons,coupons'])->group(function () {
    Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons');
    Route::get('/coupon/{id}', [App\Http\Controllers\CouponController::class, 'index'])->name('restaurants.coupons');
    Route::get('/coupons/data', [App\Http\Controllers\CouponController::class, 'data'])->name('coupons.data');
    Route::get('/coupons/json/{id}', [App\Http\Controllers\CouponController::class, 'json'])->name('coupons.json');
    Route::post('/coupons/{id}/delete', [App\Http\Controllers\CouponController::class, 'destroy'])->name('coupons.delete.post');
    Route::post('/coupons/bulk-delete', [App\Http\Controllers\CouponController::class, 'bulkDelete'])->name('coupons.bulkDelete');
    // Toggle under base permission to allow broader access
    Route::post('/coupons/{id}/toggle', [App\Http\Controllers\CouponController::class, 'toggle'])->name('coupons.toggle');

});
Route::middleware(['permission:coupons,coupons.edit'])->group(function () {
    Route::get('/coupons/edit/{id}', [App\Http\Controllers\CouponController::class, 'edit'])->name('coupons.edit');
    Route::post('/coupons/{id}', [App\Http\Controllers\CouponController::class, 'update'])->name('coupons.update');

});
Route::middleware(['permission:coupons,coupons.create'])->group(function () {
    Route::get('/coupons/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupons.create');
    Route::get('/coupon/create/{id}', [App\Http\Controllers\CouponController::class, 'create']);
    Route::get('/coupons/create/{id}', [App\Http\Controllers\CouponController::class, 'create']);
    Route::post('/coupons', [App\Http\Controllers\CouponController::class, 'store'])->name('coupons.store');

});

// Documents (SQL)
// List & read
Route::middleware(['permission:documents,documents.list'])->group(function () {
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents');
    Route::get('/documents/data', [App\Http\Controllers\DocumentController::class, 'data'])->name('documents.data');
    Route::get('/documents/json/{id}', [App\Http\Controllers\DocumentController::class, 'json'])->name('documents.json');
});
// Toggle publish requires edit
Route::middleware(['permission:documents,documents.edit'])->group(function () {
    Route::post('/documents/{id}/toggle', [App\Http\Controllers\DocumentController::class, 'toggle'])->name('documents.toggle');
});
// Delete endpoints require delete
Route::middleware(['permission:documents,documents.delete'])->group(function () {
    Route::post('/documents/{id}/delete', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.delete.post');
    Route::post('/documents/bulk-delete', [App\Http\Controllers\DocumentController::class, 'bulkDelete'])->name('documents.bulkDelete');
});
Route::middleware(['permission:documents,documents.create'])->group(function () {
    Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
});
Route::middleware(['permission:documents,documents.edit'])->group(function () {
    Route::get('/documents/edit/{id}', [App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
    Route::post('/documents/{id}', [App\Http\Controllers\DocumentController::class, 'update'])->name('documents.update');
});

Route::middleware(['permission:foods,foods'])->group(function () {
    Route::get('/foods', [App\Http\Controllers\FoodController::class, 'index'])->name('foods');
    Route::get('/foods/data', [App\Http\Controllers\FoodController::class, 'data'])->name('foods.data');
    Route::get('/foods/options', [App\Http\Controllers\FoodController::class, 'options'])->name('foods.options');
    Route::get('/foods/json/{id}', [App\Http\Controllers\FoodController::class, 'showJson'])->name('foods.json');
});

// Food import routes - must be before /foods/{id} route to avoid conflicts
Route::post('/foods/import', [App\Http\Controllers\FoodController::class, 'import'])->name('foods.import');
Route::get('/foods/download-template', [App\Http\Controllers\FoodController::class, 'downloadTemplate'])->name('foods.download-template');

Route::middleware(['permission:foods,foods'])->group(function () {
    Route::get('/foods/{id}', [App\Http\Controllers\FoodController::class, 'index'])->name('restaurants.foods');
});

Route::middleware(['permission:foods,foods.edit'])->group(function () {
    Route::get('/foods/edit/{id}', [App\Http\Controllers\FoodController::class, 'edit'])->name('foods.edit');
    Route::patch('/foods/inline-update/{id}', [App\Http\Controllers\FoodController::class, 'inlineUpdate'])->name('foods.inlineUpdate');
    Route::post('/foods/{id}', [App\Http\Controllers\FoodController::class, 'update'])->name('foods.update');
    Route::post('/foods/{id}/toggle', [App\Http\Controllers\FoodController::class, 'togglePublish'])->name('foods.toggle');
});
Route::middleware(['permission:foods,foods.create'])->group(function () {
    Route::get('/food/create', [App\Http\Controllers\FoodController::class, 'create'])->name('foods.create');
    Route::get('/food/create/{id}', [App\Http\Controllers\FoodController::class, 'create']);
    Route::post('/foods', [App\Http\Controllers\FoodController::class, 'store'])->name('foods.store');

});

// Mart Items Routes
Route::middleware(['permission:mart-items,mart-items'])->group(function () {
    Route::get('/mart-items', [App\Http\Controllers\MartItemController::class, 'index'])->name('mart-items');
});

// Mart items import routes - must be before /mart-items/{id} route to avoid conflicts
Route::middleware(['permission:mart-items,mart-items'])->group(function () {
    Route::post('/mart-items/import', [App\Http\Controllers\MartItemController::class, 'import'])->name('mart-items.import');
    Route::get('/mart-items/download-template', [App\Http\Controllers\MartItemController::class, 'downloadTemplate'])->name('mart-items.download-template');
});

// IMPORTANT: Specific /mart-items/* routes MUST come BEFORE /mart-items/{id}
// Mart Items API Routes (SQL Database) - Accessible with auth
Route::middleware(['auth'])->group(function () {
    Route::get('/mart-items/data', [App\Http\Controllers\MartItemController::class, 'getMartItemsData'])->name('mart-items.data');
    Route::get('/mart-items/categories', [App\Http\Controllers\MartItemController::class, 'getCategories'])->name('mart-items.categories');
    Route::get('/mart-items/subcategories', [App\Http\Controllers\MartItemController::class, 'getSubcategories'])->name('mart-items.subcategories');
    Route::get('/mart-items/brands', [App\Http\Controllers\MartItemController::class, 'getBrands'])->name('mart-items.brands');
    Route::get('/mart-items/vendors', [App\Http\Controllers\MartItemController::class, 'getVendors'])->name('mart-items.vendors');
    Route::get('/mart-items/placeholder-image', [App\Http\Controllers\MartItemController::class, 'getPlaceholderImage'])->name('mart-items.placeholder-image');
    Route::get('/mart-items/currency-settings', [App\Http\Controllers\MartItemController::class, 'getCurrencySettings'])->name('mart-items.currency-settings');
});

// Mart Items EDIT Routes - MUST come before wildcard /mart-items/{id}
Route::middleware(['auth'])->group(function () {
    Route::get('/mart-items/edit/{id}', [App\Http\Controllers\MartItemController::class, 'edit'])->name('mart-items.edit');
    Route::post('/mart-items/update/{id}', [App\Http\Controllers\MartItemController::class, 'update'])->name('mart-items.update');
    Route::patch('/mart-items/inline-update/{id}', [App\Http\Controllers\MartItemController::class, 'inlineUpdate'])->name('mart-items.inlineUpdate');
    Route::get('/mart-items/{id}/data', [App\Http\Controllers\MartItemController::class, 'getMartItemById'])->name('mart-items.get-by-id');
});

// Mart Items CREATE Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/mart-item/create', [App\Http\Controllers\MartItemController::class, 'create'])->name('mart-items.create');
    Route::get('/mart-item/create/{id}', [App\Http\Controllers\MartItemController::class, 'create']);
    Route::post('/mart-items/store', [App\Http\Controllers\MartItemController::class, 'store'])->name('mart-items.store');
});

// Mart Items ACTION Routes (toggle, delete, etc.)
Route::middleware(['auth'])->group(function () {
    Route::post('/mart-items/{id}/toggle-publish', [App\Http\Controllers\MartItemController::class, 'togglePublish'])->name('mart-items.toggle-publish');
    Route::post('/mart-items/{id}/toggle-availability', [App\Http\Controllers\MartItemController::class, 'toggleAvailability'])->name('mart-items.toggle-availability');
    Route::delete('/mart-items/{id}', [App\Http\Controllers\MartItemController::class, 'deleteMartItem'])->name('mart-items.delete');
    Route::post('/mart-items/bulk-delete', [App\Http\Controllers\MartItemController::class, 'bulkDelete'])->name('mart-items.bulk-delete');
});

// Wildcard route MUST come LAST - This matches /mart-items/{vendorID} for listing
Route::middleware(['auth'])->group(function () {
    Route::get('/mart-items/{id}', [App\Http\Controllers\MartItemController::class, 'index'])->name('marts.mart-items');
});

// Settings API Routes (SQL Database) - Replace Firebase calls
Route::middleware(['auth'])->group(function () {
    Route::get('/api/settings/all', [App\Http\Controllers\Api\SettingsApiController::class, 'getAllSettings'])->name('api.settings.all');
    Route::get('/api/settings/global', [App\Http\Controllers\Api\SettingsApiController::class, 'getGlobalSettings'])->name('api.settings.global');
    Route::get('/api/settings/currency', [App\Http\Controllers\Api\SettingsApiController::class, 'getCurrencySettings'])->name('api.settings.currency');
    Route::get('/api/settings/restaurant', [App\Http\Controllers\Api\SettingsApiController::class, 'getRestaurantSettings'])->name('api.settings.restaurant');
    Route::get('/api/settings/AdminCommission', [App\Http\Controllers\Api\SettingsApiController::class, 'getAdminCommission'])->name('api.settings.admin-commission');
    Route::get('/api/settings/driver', [App\Http\Controllers\Api\SettingsApiController::class, 'getDriverSettings'])->name('api.settings.driver');
});

Route::middleware(['permission:orders,orders'])->group(function () {
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [App\Http\Controllers\OrderController::class, 'index'])->name('restaurants.orders');
    Route::post('/orders/{id}/assign-driver', [App\Http\Controllers\OrderController::class, 'assignDriver'])->name('orders.assign.driver');
    Route::post('/orders/{id}/remove-driver', [App\Http\Controllers\OrderController::class, 'removeDriver'])->name('orders.remove.driver');
    Route::post('/orders/{id}/update-status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.update.status');

});
Route::middleware(['permission:orders,orders.edit'])->group(function () {
    Route::get('/orders/edit/{id}', [App\Http\Controllers\OrderController::class, 'edit'])->name('orders.edit');

});

// Catering Requests Routes
Route::middleware(['permission:catering,catering'])->group(function () {
    Route::get('/catering', [App\Http\Controllers\CateringController::class, 'index'])->name('catering');
});
Route::middleware(['permission:catering,catering.edit'])->group(function () {
    Route::get('/catering/edit/{id}', [App\Http\Controllers\CateringController::class, 'edit'])->name('catering.edit');
});
Route::middleware(['permission:orders,vendors.orderprint'])->group(function () {
    Route::get('/orders/print/{id}', [App\Http\Controllers\OrderController::class, 'orderprint'])->name('vendors.orderprint');

});

Route::middleware(['permission:category,categories'])->group(function () {

    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/data', [App\Http\Controllers\CategoryController::class, 'data'])->name('categories.data');
});
Route::middleware(['permission:category,categories.edit'])->group(function () {
    Route::get('/categories/edit/{id}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/{id}', [App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');

});
Route::middleware(['permission:category,categories.create'])->group(function () {
    Route::get('/categories/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');

});
Route::middleware(['permission:category,categories.delete'])->group(function () {
    Route::get('/categories/delete/{id}', [App\Http\Controllers\CategoryController::class, 'delete'])->name('categories.delete');
    Route::post('/categories/{id}/toggle', [App\Http\Controllers\CategoryController::class, 'togglePublish'])->name('categories.toggle');
});
Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');
Route::get('/categories/download-template', [CategoryController::class, 'downloadTemplate'])->name('categories.download-template');

// Mart Categories Routes
Route::middleware(['permission:mart-categories,mart-categories'])->group(function () {
    Route::get('/mart-categories', [App\Http\Controllers\MartCategoryController::class, 'index'])->name('mart-categories');
});
Route::middleware(['permission:mart-categories,mart-categories.edit'])->group(function () {
    Route::get('/mart-categories/edit/{id}', [App\Http\Controllers\MartCategoryController::class, 'edit'])->name('mart-categories.edit');
});
Route::middleware(['permission:mart-categories,mart-categories.create'])->group(function () {
    Route::get('/mart-categories/create', [App\Http\Controllers\MartCategoryController::class, 'create'])->name('mart-categories.create');
});
Route::post('/mart-categories/import', [App\Http\Controllers\MartCategoryController::class, 'import'])->name('mart-categories.import');
Route::get('/mart-categories/download-template', [App\Http\Controllers\MartCategoryController::class, 'downloadTemplate'])->name('mart-categories.download-template');

// Mart Categories API Routes (for AJAX)
// IMPORTANT: Specific routes must come BEFORE wildcard {id} routes
Route::post('/api/mart-categories/get-data', [App\Http\Controllers\MartCategoryController::class, 'getData'])->name('api.mart-categories.get-data');
Route::post('/api/mart-categories/store', [App\Http\Controllers\MartCategoryController::class, 'store'])->name('api.mart-categories.store');
Route::post('/api/mart-categories/bulk-delete', [App\Http\Controllers\MartCategoryController::class, 'bulkDelete'])->name('api.mart-categories.bulk-delete');
// Wildcard routes must come AFTER specific routes
Route::get('/api/mart-categories/{id}', [App\Http\Controllers\MartCategoryController::class, 'getCategory'])->name('api.mart-categories.get');
Route::post('/api/mart-categories/{id}/update', [App\Http\Controllers\MartCategoryController::class, 'update'])->name('api.mart-categories.update');
Route::delete('/api/mart-categories/{id}', [App\Http\Controllers\MartCategoryController::class, 'destroy'])->name('api.mart-categories.destroy');
Route::post('/api/mart-categories/{id}/toggle-publish', [App\Http\Controllers\MartCategoryController::class, 'togglePublish'])->name('api.mart-categories.toggle-publish');

// Mart Sub-Categories Routes (Temporary - without permissions for testing)
Route::get('/mart-categories/{category_id}/subcategories', [App\Http\Controllers\MartSubcategoryController::class, 'index'])->name('mart-subcategories.index');
Route::get('/mart-categories/{category_id}/subcategories/create', [App\Http\Controllers\MartSubcategoryController::class, 'create'])->name('mart-subcategories.create');
Route::get('/mart-subcategories/{id}/edit', [App\Http\Controllers\MartSubcategoryController::class, 'edit'])->name('mart-subcategories.edit');
Route::post('/mart-subcategories/import', [App\Http\Controllers\MartSubcategoryController::class, 'import'])->name('mart-subcategories.import');
Route::get('/mart-subcategories/download-template', [App\Http\Controllers\MartSubcategoryController::class, 'downloadTemplate'])->name('mart-subcategories.download-template');

// Mart Sub-Categories API Routes (for AJAX)
Route::post('/api/mart-subcategories/{category_id}/get-data', [App\Http\Controllers\MartSubcategoryController::class, 'getData'])->name('api.mart-subcategories.get-data');
Route::get('/api/mart-subcategories/{id}', [App\Http\Controllers\MartSubcategoryController::class, 'getSubcategory'])->name('api.mart-subcategories.get');
Route::get('/api/mart-categories/{category_id}/info', [App\Http\Controllers\MartSubcategoryController::class, 'getParentCategory'])->name('api.mart-categories.info');
Route::post('/api/mart-subcategories/store', [App\Http\Controllers\MartSubcategoryController::class, 'store'])->name('api.mart-subcategories.store');
Route::post('/api/mart-subcategories/{id}/update', [App\Http\Controllers\MartSubcategoryController::class, 'update'])->name('api.mart-subcategories.update');
Route::delete('/api/mart-subcategories/{id}', [App\Http\Controllers\MartSubcategoryController::class, 'destroy'])->name('api.mart-subcategories.destroy');
Route::post('/api/mart-subcategories/bulk-delete', [App\Http\Controllers\MartSubcategoryController::class, 'bulkDelete'])->name('api.mart-subcategories.bulk-delete');
Route::post('/api/mart-subcategories/{id}/toggle-publish', [App\Http\Controllers\MartSubcategoryController::class, 'togglePublish'])->name('api.mart-subcategories.toggle-publish');

// Original routes with permissions (commented out for now)
/*
Route::middleware(['permission:mart-subcategories,mart-subcategories'])->group(function () {
    Route::get('/mart-categories/{category_id}/subcategories', [App\Http\Controllers\MartSubcategoryController::class, 'index'])->name('mart-subcategories.index');
});
Route::middleware(['permission:mart-subcategories,mart-subcategories.create'])->group(function () {
    Route::get('/mart-categories/{category_id}/subcategories/create', [App\Http\Controllers\MartSubcategoryController::class, 'create'])->name('mart-subcategories.create');
});
Route::middleware(['permission:mart-subcategories,mart-subcategories.edit'])->group(function () {
    Route::get('/mart-subcategories/{id}/edit', [App\Http\Controllers\MartSubcategoryController::class, 'edit'])->name('mart-subcategories.edit');
});
Route::post('/mart-subcategories/import', [App\Http\Controllers\MartSubcategoryController::class, 'import'])->name('mart-subcategories.import');
Route::get('/mart-subcategories/download-template', [App\Http\Controllers\MartSubcategoryController::class, 'downloadTemplate'])->name('mart-subcategories.download-template');
*/
Route::post('/cuisines/import', [CuisineController::class, 'import'])->name('cuisines.import');
Route::get('/cuisines/download-template', [CuisineController::class, 'downloadTemplate'])->name('cuisines.download-template');

Route::middleware(['permission:cuisines,cuisines'])->group(function () {
    Route::get('/cuisines', [App\Http\Controllers\CuisineController::class, 'index'])->name('cuisines');
    Route::get('/cuisines/data', [App\Http\Controllers\CuisineController::class, 'data'])->name('cuisines.data');
});
Route::middleware(['permission:cuisines,cuisines.edit'])->group(function () {
    Route::get('/cuisines/edit/{id}', [App\Http\Controllers\CuisineController::class, 'edit'])->name('cuisines.edit');
    Route::post('/cuisines/{id}', [App\Http\Controllers\CuisineController::class, 'update'])->name('cuisines.update');
});
Route::middleware(['permission:cuisines,cuisines.create'])->group(function () {
    Route::get('/cuisines/create', [App\Http\Controllers\CuisineController::class, 'create'])->name('cuisines.create');
    Route::post('/cuisines', [App\Http\Controllers\CuisineController::class, 'store'])->name('cuisines.store');
});
Route::middleware(['permission:cuisines,cuisines.delete'])->group(function () {
    Route::get('/cuisines/delete/{id}', [App\Http\Controllers\CuisineController::class, 'delete'])->name('cuisines.delete');
    Route::post('/cuisines/{id}/toggle', [App\Http\Controllers\CuisineController::class, 'togglePublish'])->name('cuisines.toggle');
});

Route::middleware(['permission:promotions,promotions'])->group(function () {
    Route::get('/promotions', [App\Http\Controllers\PromotionController::class, 'index'])->name('promotions');
});
Route::middleware(['permission:promotions,promotions.edit'])->group(function () {
    Route::get('/promotions/edit/{id}', [App\Http\Controllers\PromotionController::class, 'edit'])->name('promotions.edit');
});
Route::middleware(['permission:promotions,promotions.create'])->group(function () {
    Route::get('/promotions/create', [App\Http\Controllers\PromotionController::class, 'create'])->name('promotions.create');
});
Route::middleware(['permission:promotions,promotions.delete'])->group(function () {
    Route::get('/promotions/delete/{id}', [App\Http\Controllers\PromotionController::class, 'delete'])->name('promotions.delete');
});

Route::middleware(['permission:menu-periods,menu-periods'])->group(function () {
    Route::get('/menu-periods', [App\Http\Controllers\MenuPeriodController::class, 'index'])->name('menu-periods');
    Route::get('/menu-periods/data', [App\Http\Controllers\MenuPeriodController::class, 'data'])->name('menu-periods.data');
    Route::get('/menu-periods/json/{id}', [App\Http\Controllers\MenuPeriodController::class, 'showJson'])->name('menu-periods.json');
    // Friendly POST delete under base permission
    Route::post('/menu-periods/{id}/delete', [App\Http\Controllers\MenuPeriodController::class, 'destroy'])->name('menu-periods.delete.post');
});
Route::middleware(['permission:menu-periods,menu-periods.create'])->group(function () {
    Route::get('/menu-periods/create', [App\Http\Controllers\MenuPeriodController::class, 'create'])->name('menu-periods.create');
    Route::post('/menu-periods', [App\Http\Controllers\MenuPeriodController::class, 'store'])->name('menu-periods.store');
});
Route::middleware(['permission:menu-periods,menu-periods.edit'])->group(function () {
    Route::get('/menu-periods/edit/{id}', [App\Http\Controllers\MenuPeriodController::class, 'edit'])->name('menu-periods.edit');
    Route::post('/menu-periods/{id}', [App\Http\Controllers\MenuPeriodController::class, 'update'])->name('menu-periods.update');
});
Route::middleware(['permission:menu-periods,menu-periods.delete'])->group(function () {
    Route::delete('/menu-periods/{id}', [App\Http\Controllers\MenuPeriodController::class, 'destroy'])->name('menu-periods.delete');
});


// Brands Routes
Route::middleware(['permission:brands,brands'])->group(function () {
    Route::get('/brands', [App\Http\Controllers\BrandController::class, 'index'])->name('brands');
    Route::post('/brands', [App\Http\Controllers\BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/data', [App\Http\Controllers\BrandController::class, 'getData'])->name('brands.data');
});
Route::middleware(['permission:brands,brands.edit'])->group(function () {
    Route::get('/brands/edit/{id}', [App\Http\Controllers\BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{id}', [App\Http\Controllers\BrandController::class, 'update'])->name('brands.update');
});
Route::middleware(['permission:brands,brands.create'])->group(function () {
    Route::get('/brands/create', [App\Http\Controllers\BrandController::class, 'create'])->name('brands.create');
});
Route::middleware(['permission:brands,brands.delete'])->group(function () {
    Route::get('/brands/delete/{id}', [App\Http\Controllers\BrandController::class, 'delete'])->name('brands.delete');
});
Route::post('/brands/import', [App\Http\Controllers\BrandController::class, 'import'])->name('brands.import');
Route::get('/brands/download-template', [App\Http\Controllers\BrandController::class, 'downloadTemplate'])->name('brands.download-template');


Route::middleware(['permission:drivers,drivers'])->group(function () {

    Route::get('/drivers', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers');
});
Route::middleware(['permission:approve_drivers,approve.driver.list'])->group(function () {

    Route::get('/drivers/approved', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers.approved');
});
Route::middleware(['permission:pending_drivers,pending.driver.list'])->group(function () {

    Route::get('/drivers/pending', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers.pending');
});
Route::middleware(['permission:drivers,drivers.edit'])->group(function () {
    Route::get('/drivers/edit/{id}', [App\Http\Controllers\DriverController::class, 'edit'])->name('drivers.edit');

});
Route::middleware(['permission:drivers,drivers.create'])->group(function () {
    Route::get('/drivers/create', [App\Http\Controllers\DriverController::class, 'create'])->name('drivers.create');

});
Route::middleware(['permission:drivers,drivers.view'])->group(function () {
    Route::get('/drivers/view/{id}', [App\Http\Controllers\DriverController::class, 'view'])->name('drivers.view');

});
Route::middleware(['permission:drivers,drivers.edit'])->group(function () {
    Route::post('/drivers/clear-order-request-data/{id}', [App\Http\Controllers\DriverController::class, 'clearOrderRequestData'])->name('drivers.clearOrderRequestData');
    Route::post('/drivers/clear-all-order-request-data', [App\Http\Controllers\DriverController::class, 'clearAllOrderRequestData'])->name('drivers.clearAllOrderRequestData');
});

// Driver SQL API routes
Route::middleware(['permission:drivers,drivers'])->group(function () {
    Route::get('/drivers/data', [App\Http\Controllers\DriverController::class, 'getDriversData'])->name('drivers.data');
    Route::get('/drivers/zones', [App\Http\Controllers\DriverController::class, 'getZones'])->name('drivers.zones');
    Route::get('/drivers/debug/{id}', [App\Http\Controllers\DriverController::class, 'debugDriver'])->name('drivers.debug');
    Route::get('/drivers/{id}/data', [App\Http\Controllers\DriverController::class, 'getDriverById'])->name('drivers.getById');
    Route::get('/drivers/{id}/stats', [App\Http\Controllers\DriverController::class, 'getDriverStats'])->name('drivers.stats');
    Route::get('/drivers/{id}/documents', [App\Http\Controllers\DriverController::class, 'getDriverDocuments'])->name('drivers.documents');
    Route::get('/drivers/{id}/payouts', [App\Http\Controllers\DriverController::class, 'getDriverPayouts'])->name('drivers.payouts');
    Route::get('/api/document-types/driver', [App\Http\Controllers\DriverController::class, 'getDocumentTypes'])->name('drivers.document-types');
    Route::get('/drivers/{id}/document-verification', [App\Http\Controllers\DriverController::class, 'getDocumentVerification'])->name('drivers.document-verification');
});

Route::middleware(['permission:drivers,drivers.create'])->group(function () {
    Route::post('/drivers', [App\Http\Controllers\DriverController::class, 'createDriver'])->name('drivers.create.post');
});

Route::middleware(['permission:drivers,drivers.edit'])->group(function () {
    Route::put('/drivers/{id}', [App\Http\Controllers\DriverController::class, 'updateDriver'])->name('drivers.update');
    Route::post('/drivers/{id}/toggle-status', [App\Http\Controllers\DriverController::class, 'toggleDriverStatus'])->name('drivers.toggle-status');
    Route::post('/drivers/{id}/clear-order-request-sql', [App\Http\Controllers\DriverController::class, 'clearOrderRequestDataSQL'])->name('drivers.clearOrderRequestDataSQL');
    Route::post('/drivers/clear-all-order-request-sql', [App\Http\Controllers\DriverController::class, 'clearAllOrderRequestDataSQL'])->name('drivers.clearAllOrderRequestDataSQL');
    Route::put('/drivers/{id}/document-verification', [App\Http\Controllers\DriverController::class, 'updateDocumentVerification'])->name('drivers.document-verification.update');
});

Route::middleware(['permission:drivers,drivers.delete'])->group(function () {
    Route::delete('/drivers/{id}', [App\Http\Controllers\DriverController::class, 'deleteDriver'])->name('drivers.delete');
});

Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
Route::post('/users/profile/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.profile.update');

Route::get('usersorders/{type}', [App\Http\Controllers\OrderController::class, 'index'])->name('usersorders');

Route::middleware(['permission:payments,payments'])->group(function () {
    Route::get('/payments', [App\Http\Controllers\AdminPaymentsController::class, 'index'])->name('payments');
    Route::get('/payments/currency', [App\Http\Controllers\AdminPaymentsController::class, 'getCurrency'])->name('payments.currency');
    Route::get('/payments/data', [App\Http\Controllers\AdminPaymentsController::class, 'getPaymentsData'])->name('payments.data');
});
// Public route for dashboard to access payment summary
Route::get('/payments/summary', [App\Http\Controllers\AdminPaymentsController::class, 'getPaymentsSummary'])->name('payments.summary');
Route::middleware(['permission:driver-payments,driver.driverpayments'])->group(function () {
    Route::get('driverpayments', [App\Http\Controllers\AdminPaymentsController::class, 'driverIndex'])->name('driver.driverpayments');
});
Route::middleware(['permission:restaurant-payouts,restaurantsPayouts'])->group(function () {
    Route::get('restaurantsPayouts', [App\Http\Controllers\RestaurantsPayoutController::class, 'index'])->name('restaurantsPayouts');
    Route::get('/restaurantsPayout/{id}', [App\Http\Controllers\RestaurantsPayoutController::class, 'index'])->name('restaurants.payout');

});
Route::middleware(['permission:restaurant-payouts,restaurantsPayouts.create'])->group(function () {
    Route::get('restaurantsPayouts/create', [App\Http\Controllers\RestaurantsPayoutController::class, 'create'])->name('restaurantsPayouts.create');
    Route::get('/restaurantsPayouts/create/{id}', [App\Http\Controllers\RestaurantsPayoutController::class, 'create']);

});

Route::middleware(['permission:driver-payouts,driversPayouts'])->group(function () {
    Route::get('driversPayouts', [App\Http\Controllers\DriversPayoutController::class, 'index'])->name('driversPayouts');
    Route::get('driverPayout/{id}', [App\Http\Controllers\DriversPayoutController::class, 'index'])->name('driver.payout');

});
Route::middleware(['permission:driver-payouts,driversPayouts.create'])->group(function () {
    Route::get('driversPayouts/create', [App\Http\Controllers\DriversPayoutController::class, 'create'])->name('driversPayouts.create');
    Route::get('driverPayout/create/{id}', [App\Http\Controllers\DriversPayoutController::class, 'create'])->name('driver.payout.create');

});

// Driver Wallet Management Routes
//Route::middleware(['permission:driver-wallets,driverWallets'])->group(function () {
//    Route::get('driver-wallets', [App\Http\Controllers\DriverWalletController::class, 'index'])->name('driver.wallets');
//    Route::get('api/driver-wallets', [App\Http\Controllers\DriverWalletController::class, 'getDriverWallets'])->name('api.driver.wallets');
//    Route::get('api/driver-wallets/{driverId}', [App\Http\Controllers\DriverWalletController::class, 'getDriverWallet'])->name('api.driver.wallet');
//    Route::post('api/driver-wallets/sync-all', [App\Http\Controllers\DriverWalletController::class, 'syncAllWallets'])->name('api.driver.wallets.sync');
//    Route::post('api/driver-wallets/add-credit', [App\Http\Controllers\DriverWalletController::class, 'addCredit'])->name('api.driver.wallets.add.credit');
//    Route::post('api/driver-wallets/{driverId}/refresh', [App\Http\Controllers\DriverWalletController::class, 'refreshDriverWallet'])->name('api.driver.wallet.refresh');
//    Route::put('api/driver-wallets/{driverId}', [App\Http\Controllers\DriverWalletController::class, 'updateDriverWallet'])->name('api.driver.wallet.update');
//});

Route::middleware(['permission:wallet-transaction,walletstransaction'])->group(function () {
    Route::get('walletstransaction', [App\Http\Controllers\TransactionController::class, 'index'])->name('walletstransaction');
    Route::get('/walletstransaction/{id}', [App\Http\Controllers\TransactionController::class, 'index'])->name('users.walletstransaction');
});
Route::post('order-status-notification', [App\Http\Controllers\OrderController::class, 'sendNotification'])->name('order-status-notification');
// Email notification route disabled to prevent resource issues on shared hosting

Route::middleware(['permission:dynamic-notifications,dynamic-notification.index'])->group(function () {
    Route::get('dynamic-notification', [App\Http\Controllers\DynamicNotificationController::class, 'index'])->name('dynamic-notification.index');
});
Route::middleware(['permission:dynamic-notifications,dynamic-notification.save'])->group(function () {
    Route::get('dynamic-notification/save/{id?}', [App\Http\Controllers\DynamicNotificationController::class, 'save'])->name('dynamic-notification.save');

});
Route::middleware(['permission:dynamic-notifications,dynamic-notification.delete'])->group(function () {
    Route::get('dynamic-notification/delete/{id}', [App\Http\Controllers\DynamicNotificationController::class, 'delete'])->name('dynamic-notification.delete');
});
Route::middleware(['permission:god-eye,map'])->group(function () {
    Route::get('/map', [App\Http\Controllers\MapController::class, 'index'])->name('map');
    Route::post('/map/get_order_info', [App\Http\Controllers\MapController::class, 'getOrderInfo'])->name('map.getOrderInfo');
});
Route::prefix('settings')->group(function () {
    Route::middleware(['permission:currency,currencies'])->group(function () {
        Route::get('/currencies', [App\Http\Controllers\CurrencyController::class, 'index'])->name('currencies');
    });
    Route::middleware(['permission:currency,currencies.edit'])->group(function () {
        Route::get('/currencies/edit/{id}', [App\Http\Controllers\CurrencyController::class, 'edit'])->name('currencies.edit');
    });
    Route::middleware(['permission:currency,currencies.create'])->group(function () {
        Route::get('/currencies/create', [App\Http\Controllers\CurrencyController::class, 'create'])->name('currencies.create');
    });
    Route::middleware(['permission:global-setting,settings.app.globals'])->group(function () {
        Route::get('app/globals', [App\Http\Controllers\SettingsController::class, 'globals'])->name('settings.app.globals');
    });
    Route::middleware(['permission:admin-commission,settings.app.adminCommission'])->group(function () {
        Route::get('app/adminCommission', [App\Http\Controllers\SettingsController::class, 'adminCommission'])->name('settings.app.adminCommission');
    });
    Route::middleware(['permission:radius,settings.app.radiusConfiguration'])->group(function () {
        Route::get('app/radiusConfiguration', [App\Http\Controllers\SettingsController::class, 'radiosConfiguration'])->name('settings.app.radiusConfiguration');
    });
    Route::middleware(['permission:dinein,settings.app.bookTable'])->group(function () {
        Route::get('app/bookTable', [App\Http\Controllers\SettingsController::class, 'bookTable'])->name('settings.app.bookTable');
    });
    Route::middleware(['permission:delivery-charge,settings.app.deliveryCharge'])->group(function () {
        Route::get('app/deliveryCharge', [App\Http\Controllers\SettingsController::class, 'deliveryCharge'])->name('settings.app.deliveryCharge');
    });
    Route::middleware(['permission:mart-settings,settings.app.martSettings'])->group(function () {
        Route::get('app/martSettings', [App\Http\Controllers\SettingsController::class, 'martSettings'])->name('settings.app.martSettings');
    });
    Route::middleware(['permission:surge-rules,settings.app.surgeRules'])->group(function () {
        Route::get('app/surgeRules', [App\Http\Controllers\SettingsController::class, 'surgeRules'])->name('settings.app.surgeRules');
    });
    Route::middleware(['permission:app-settings,settings.app.appSettings'])->group(function () {
        Route::get('app/appSettings', [App\Http\Controllers\SettingsController::class, 'appSettings'])->name('settings.app.appSettings');
    });
    // Route::middleware(['permission:price-setting,settings.app.priceSetting'])->group(function () {
    Route::get('app/priceSetting', [App\Http\Controllers\SettingsController::class, 'priceSetting'])->name('settings.app.priceSettings');
    // });
    Route::middleware(['permission:document-verification,settings.app.documentVerification'])->group(function () {
        Route::get('app/documentVerification', [App\Http\Controllers\SettingsController::class, 'documentVerification'])->name('settings.app.documentVerification');
    });

    // Zone Bonus Settings Routes
    Route::middleware(['permission:zone-bonus-settings,settings.zone.bonus'])->group(function () {
        Route::get('zone/bonus-settings', [App\Http\Controllers\ZoneBonusController::class, 'index'])->name('settings.zone.bonus');
        Route::get('api/zone-bonus-settings', [App\Http\Controllers\ZoneBonusController::class, 'getZoneBonusSettings'])->name('api.zone.bonus.settings');
        Route::post('api/zone-bonus-settings', [App\Http\Controllers\ZoneBonusController::class, 'store'])->name('api.zone.bonus.store');
        Route::put('api/zone-bonus-settings/{id}', [App\Http\Controllers\ZoneBonusController::class, 'update'])->name('api.zone.bonus.update');
        Route::delete('api/zone-bonus-settings/{id}', [App\Http\Controllers\ZoneBonusController::class, 'destroy'])->name('api.zone.bonus.destroy');
        Route::get('api/zone-bonus-settings/zone/{zoneId}', [App\Http\Controllers\ZoneBonusController::class, 'getZoneBonusSetting'])->name('api.zone.bonus.get');
        Route::get('api/zones', [App\Http\Controllers\ZoneBonusController::class, 'getZones'])->name('api.zones');
    });

    Route::middleware(['permission:payment-method,payment-method'])->group(function () {
        Route::get('payment/stripe', [App\Http\Controllers\SettingsController::class, 'stripe'])->name('payment.stripe');
        Route::get('payment/applepay', [App\Http\Controllers\SettingsController::class, 'applepay'])->name('payment.applepay');
        Route::get('payment/razorpay', [App\Http\Controllers\SettingsController::class, 'razorpay'])->name('payment.razorpay');
        Route::get('payment/cod', [App\Http\Controllers\SettingsController::class, 'cod'])->name('payment.cod');
        Route::get('payment/paypal', [App\Http\Controllers\SettingsController::class, 'paypal'])->name('payment.paypal');
        Route::get('payment/paytm', [App\Http\Controllers\SettingsController::class, 'paytm'])->name('payment.paytm');
        Route::get('payment/wallet', [App\Http\Controllers\SettingsController::class, 'wallet'])->name('payment.wallet');
        Route::get('payment/payfast', [App\Http\Controllers\SettingsController::class, 'payfast'])->name('payment.payfast');
        Route::get('payment/paystack', [App\Http\Controllers\SettingsController::class, 'paystack'])->name('payment.paystack');
        Route::get('payment/flutterwave', [App\Http\Controllers\SettingsController::class, 'flutterwave'])->name('payment.flutterwave');
        Route::get('payment/mercadopago', [App\Http\Controllers\SettingsController::class, 'mercadopago'])->name('payment.mercadopago');
        Route::get('payment/xendit', [App\Http\Controllers\SettingsController::class, 'xendit'])->name('payment.xendit');
        Route::get('payment/orangepay', [App\Http\Controllers\SettingsController::class, 'orangepay'])->name('payment.orangepay');
        Route::get('payment/midtrans', [App\Http\Controllers\SettingsController::class, 'midtrans'])->name('payment.midtrans');
    });

    Route::middleware(['permission:language,settings.app.languages'])->group(function () {
        Route::get('app/languages', [App\Http\Controllers\SettingsController::class, 'languages'])->name('settings.app.languages');

    });
    Route::middleware(['permission:language,settings.app.languages.create'])->group(function () {
        Route::get('app/languages/create', [App\Http\Controllers\SettingsController::class, 'languagescreate'])->name('settings.app.languages.create');

    });
    Route::middleware(['permission:language,settings.app.languages.edit'])->group(function () {
        Route::get('app/languages/edit/{id}', [App\Http\Controllers\SettingsController::class, 'languagesedit'])->name('settings.app.languages.edit');

    });
    Route::middleware(['permission:special-offer,setting.specialOffer'])->group(function () {
        Route::get('app/specialOffer', [App\Http\Controllers\SettingsController::class, 'specialOffer'])->name('setting.specialOffer');
    });

    Route::get('app/story', [App\Http\Controllers\SettingsController::class, 'story'])->name('setting.story');
    Route::get('app/notifications', [App\Http\Controllers\SettingsController::class, 'notifications'])->name('settings.app.notifications');
    Route::get('mobile/globals', [App\Http\Controllers\SettingsController::class, 'mobileGlobals'])->name('settings.mobile.globals');

});
Route::middleware(['permission:dinein-orders,restaurants.booktable'])->group(function () {
    Route::get('/booktable/{id}', [App\Http\Controllers\BookTableController::class, 'index'])->name('restaurants.booktable');

});
Route::middleware(['permission:dinein-orders,booktable.edit'])->group(function () {
    Route::get('/booktable/edit/{id}', [App\Http\Controllers\BookTableController::class, 'edit'])->name('booktable.edit');
});
Route::post('/sendnotification', [App\Http\Controllers\BookTableController::class, 'sendnotification'])->name('sendnotification');

Route::middleware(['permission:general-notifications,notification'])->group(function () {
    Route::get('/notification', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification');
});
Route::middleware(['permission:general-notifications,notification.send'])->group(function () {
    Route::get('/notification/send', [App\Http\Controllers\NotificationController::class, 'send'])->name('notification.send');

});
Route::post('broadcastnotification', [App\Http\Controllers\NotificationController::class, 'broadcastnotification'])->name('broadcastnotification');

// Debug route for testing notifications (remove in production)
Route::get('debug/notification-test', function () {
    return view('debug.notification-test');
})->name('debug.notification-test');

Route::middleware(['permission:payout-request,payoutRequests.drivers'])->group(function () {
    Route::get('/payoutRequests/drivers', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers');
    Route::get('/payoutRequests/drivers/{id}', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers.view');
});
Route::middleware(['permission:payout-request,payoutRequests.restaurants'])->group(function () {
    Route::get('/payoutRequests/restaurants', [App\Http\Controllers\PayoutRequestController::class, 'restaurant'])->name('payoutRequests.restaurants');
    Route::get('/payoutRequests/restaurants/{id}', [App\Http\Controllers\PayoutRequestController::class, 'restaurant'])->name('payoutRequests.restaurants.view');

});
Route::get('order_transactions', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions');
Route::get('/order_transactions/{id}', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions.index');


// Activity Log Routes
Route::middleware(['permission:activity-logs,activity-logs'])->group(function () {
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs');
    Route::get('/api/activity-logs/data', [App\Http\Controllers\ActivityLogController::class, 'getActivityLogsData'])->name('api.activity-logs.data');
    Route::get('/api/activity-logs/count', [App\Http\Controllers\ActivityLogController::class, 'getLogsCount'])->name('api.activity-logs.count');
});
Route::post('/api/activity-logs/log', [App\Http\Controllers\ActivityLogController::class, 'logActivity'])->name('api.activity-logs.log');
Route::get('/api/activity-logs/module/{module}', [App\Http\Controllers\ActivityLogController::class, 'getModuleLogs'])->name('api.activity-logs.module');
Route::get('/api/activity-logs/all', [App\Http\Controllers\ActivityLogController::class, 'getAllLogs'])->name('api.activity-logs.all');
Route::get('/api/activity-logs/cuisines', [App\Http\Controllers\ActivityLogController::class, 'getCuisinesLogs'])->name('api.activity-logs.cuisines');

Route::get('payment/success', [App\Http\Controllers\PaymentController::class, 'paymentsuccess'])->name('payment.success');
Route::get('payment/failed', [App\Http\Controllers\PaymentController::class, 'paymentfailed'])->name('payment.failed');
Route::get('payment/pending', [App\Http\Controllers\PaymentController::class, 'paymentpending'])->name('payment.pending');

Route::middleware(['permission:banners,setting.banners'])->group(function () {
    Route::get('/banners', [App\Http\Controllers\MenuItemController::class, 'index'])->name('setting.banners');
});
Route::middleware(['permission:banners,setting.banners.create'])->group(function () {
    Route::get('/banners/create', [App\Http\Controllers\MenuItemController::class, 'create'])->name('setting.banners.create');

});
Route::middleware(['permission:banners,setting.banners.edit'])->group(function () {
    Route::get('/banners/edit/{id}', [App\Http\Controllers\MenuItemController::class, 'edit'])->name('setting.banners.edit');
});

// Mart Banner Items Routes
Route::middleware(['permission:mart_banners,mart_banners'])->group(function () {
    Route::get('/mart-banners', [App\Http\Controllers\MartBannerController::class, 'index'])->name('mart.banners');
    Route::get('/mart-banners/data', [App\Http\Controllers\MartBannerController::class, 'data'])->name('mart.banners.data');
    Route::get('/mart-banners/json/{id}', [App\Http\Controllers\MartBannerController::class, 'json'])->name('mart.banners.json');
});

Route::middleware(['permission:mart_banners,mart_banners.create'])->group(function () {
    Route::get('/mart-banners/create', [App\Http\Controllers\MartBannerController::class, 'create'])->name('mart.banners.create');
    Route::post('/mart-banners', [App\Http\Controllers\MartBannerController::class, 'store'])->name('mart.banners.store');
});

Route::middleware(['permission:mart_banners,mart_banners.edit'])->group(function () {
    Route::get('/mart-banners/edit/{id}', [App\Http\Controllers\MartBannerController::class, 'edit'])->name('mart.banners.edit');
    Route::put('/mart-banners/{id}', [App\Http\Controllers\MartBannerController::class, 'update'])->name('mart.banners.update');
    Route::post('/mart-banners/{id}/toggle-publish', [App\Http\Controllers\MartBannerController::class, 'togglePublish'])->name('mart.banners.togglePublish');
});

Route::middleware(['permission:mart_banners,mart_banners.delete'])->group(function () {
    Route::delete('/mart-banners/{id}', [App\Http\Controllers\MartBannerController::class, 'destroy'])->name('mart.banners.destroy');
    Route::post('/mart-banners/bulk-delete', [App\Http\Controllers\MartBannerController::class, 'bulkDelete'])->name('mart.banners.bulkDelete');
});

// Settings → Menu Items (SQL)
// Use the same route key as the index page (setting.banners) to avoid 403s
Route::middleware(['permission:banners,setting.banners'])->group(function () {
    Route::get('/settings/menu-items', [App\Http\Controllers\MenuItemController::class, 'index'])->name('setting.banners');
    Route::get('/menu-items/data', [App\Http\Controllers\MenuItemController::class, 'data'])->name('menu-items.data');
    Route::get('/menu-items/json/{id}', [App\Http\Controllers\MenuItemController::class, 'json'])->name('menu-items.json');
});
Route::middleware(['permission:banners,setting.banners.create'])->group(function () {
    Route::get('/settings/menu-items/create', [App\Http\Controllers\MenuItemController::class, 'create'])->name('setting.banners.create');
    Route::post('/menu-items', [App\Http\Controllers\MenuItemController::class, 'store'])->name('menu-items.store');
});
Route::middleware(['permission:banners,setting.banners.edit'])->group(function () {
    Route::get('/settings/menu-items/edit/{id}', [App\Http\Controllers\MenuItemController::class, 'edit'])->name('setting.banners.edit');
    Route::post('/menu-items/{id}/toggle', [App\Http\Controllers\MenuItemController::class, 'togglePublish'])->name('menu-items.toggle');
    Route::post('/menu-items/{id}', [App\Http\Controllers\MenuItemController::class, 'update'])->name('menu-items.update');
});
Route::middleware(['permission:banners,banners.delete'])->group(function () {
    Route::post('/menu-items/{id}/delete', [App\Http\Controllers\MenuItemController::class, 'destroy'])->name('menu-items.delete');
    Route::post('/menu-items/bulk-delete', [App\Http\Controllers\MenuItemController::class, 'bulkDelete'])->name('menu-items.bulkDelete');
});
Route::middleware(['permission:item-attribute,attributes'])->group(function () {
    Route::get('/attributes', [App\Http\Controllers\AttributeController::class, 'index'])->name('attributes');
});
Route::middleware(['permission:item-attribute,attributes.edit'])->group(function () {
    Route::get('/attributes/edit/{id}', [App\Http\Controllers\AttributeController::class, 'edit'])->name('attributes.edit');
});
Route::middleware(['permission:item-attribute,attributes.create'])->group(function () {
    Route::get('/attributes/create', [App\Http\Controllers\AttributeController::class, 'create'])->name('attributes.create');
});

Route::middleware(['permission:review-attribute,reviewattributes'])->group(function () {
    Route::get('/reviewattributes', [App\Http\Controllers\ReviewAttributeController::class, 'index'])->name('reviewattributes');
});
Route::middleware(['permission:review-attribute,reviewattributes.edit'])->group(function () {
    Route::get('/reviewattributes/edit/{id}', [App\Http\Controllers\ReviewAttributeController::class, 'edit'])->name('reviewattributes.edit');
});
Route::middleware(['permission:review-attribute,reviewattributes.create'])->group(function () {
    Route::get('/reviewattributes/create', [App\Http\Controllers\ReviewAttributeController::class, 'create'])->name('reviewattributes.create');
});

// Review Attributes API Route
Route::get('/api/review-attributes', [App\Http\Controllers\ReviewAttributeController::class, 'getAll'])->name('api.review-attributes.get-all');

Route::middleware(['permission:footer,footerTemplate'])->group(function () {
    Route::get('footerTemplate', [App\Http\Controllers\SettingsController::class, 'footerTemplate'])->name('footerTemplate');
});
Route::middleware(['permission:home-page,homepageTemplate'])->group(function () {
    Route::get('/homepageTemplate', [App\Http\Controllers\SettingsController::class, 'homepageTemplate'])->name('homepageTemplate');
});
Route::middleware(['permission:cms,cms'])->group(function () {
    Route::get('cms', [App\Http\Controllers\CmsController::class, 'index'])->name('cms');
    Route::get('/cms/data', [App\Http\Controllers\CmsController::class, 'data'])->name('cms.data');
    Route::get('/cms/json/{id}', [App\Http\Controllers\CmsController::class, 'json'])->name('cms.json');
});
Route::middleware(['permission:cms,cms.edit'])->group(function () {
    Route::get('/cms/edit/{id}', [App\Http\Controllers\CmsController::class, 'edit'])->name('cms.edit');
    Route::post('/cms/{id}', [App\Http\Controllers\CmsController::class, 'update'])->name('cms.update');
    Route::post('/cms/{id}/toggle', [App\Http\Controllers\CmsController::class, 'toggle'])->name('cms.toggle');
});
Route::middleware(['permission:cms,cms.create'])->group(function () {
    Route::get('/cms/create', [App\Http\Controllers\CmsController::class, 'create'])->name('cms.create');
    Route::post('/cms', [App\Http\Controllers\CmsController::class, 'store'])->name('cms.store');
});
Route::middleware(['permission:cms,cms.delete'])->group(function () {
    Route::post('/cms/{id}/delete', [App\Http\Controllers\CmsController::class, 'destroy'])->name('cms.delete.post');
    Route::post('/cms/bulk-delete', [App\Http\Controllers\CmsController::class, 'bulkDelete'])->name('cms.bulkDelete');
});
Route::middleware(['permission:reports,report.index'])->group(function () {
    Route::get('report/{type}', [App\Http\Controllers\ReportController::class, 'index'])->name('report.index');
Route::get('/reports/sales/options', [App\Http\Controllers\ReportController::class, 'salesOptions'])->name('reports.sales.options');
Route::post('/reports/sales/data', [App\Http\Controllers\ReportController::class, 'salesData'])->name('reports.sales.data');

});

Route::middleware(['permission:tax,tax'])->group(function () {
    Route::get('/tax', [App\Http\Controllers\TaxController::class, 'index'])->name('tax');
});
Route::middleware(['permission:tax,tax.edit'])->group(function () {
    Route::get('/tax/edit/{id}', [App\Http\Controllers\TaxController::class, 'edit'])->name('tax.edit');
});
Route::middleware(['permission:tax,tax.create'])->group(function () {
    Route::get('/tax/create', [App\Http\Controllers\TaxController::class, 'create'])->name('tax.create');
});

Route::middleware(['permission:email-template,email-templates.index'])->group(function () {
    Route::get('email-templates', [App\Http\Controllers\SettingsController::class, 'emailTemplatesIndex'])->name('email-templates.index');
    Route::get('email-templates/data', [App\Http\Controllers\SettingsController::class, 'emailTemplatesData'])->name('email-templates.data');
    Route::get('email-templates/json/{id}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesJson'])->name('email-templates.json');
});
Route::middleware(['permission:email-template,email-templates.edit'])->group(function () {
    Route::get('email-templates/save/{id?}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesSave'])->name('email-templates.save');
    Route::post('email-templates/{id}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesUpdate'])->name('email-templates.update');

});
Route::middleware(['permission:email-template,email-templates.delete'])->group(function () {
    Route::get('email-templates/delete/{id}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesDelete'])->name('email-templates.delete');
    Route::post('email-templates/{id}/delete', [App\Http\Controllers\SettingsController::class, 'emailTemplatesDelete'])->name('email-templates.delete.post');

});
Route::post('send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');

Route::middleware(['permission:gift-cards,gift-card.index'])->group(function () {
    Route::get('gift-card', [App\Http\Controllers\GiftCardController::class, 'index'])->name('gift-card.index');
    Route::get('gift-card/data', [App\Http\Controllers\GiftCardController::class, 'data'])->name('gift-card.data');
    Route::get('gift-card/json/{id}', [App\Http\Controllers\GiftCardController::class, 'json'])->name('gift-card.json');
    Route::post('gift-card/{id}/delete', [App\Http\Controllers\GiftCardController::class, 'destroy'])->name('gift-card.delete.post');
    Route::post('gift-card/bulk-delete', [App\Http\Controllers\GiftCardController::class, 'bulkDelete'])->name('gift-card.bulkDelete');
});
Route::middleware(['permission:gift-cards,gift-card.save'])->group(function () {
    Route::get('gift-card/save/{id?}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.save');
    Route::post('gift-card', [App\Http\Controllers\GiftCardController::class, 'store'])->name('gift-card.store');
});
Route::middleware(['permission:gift-cards,gift-card.edit'])->group(function () {
    Route::get('gift-card/edit/{id}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.edit');
    Route::post('gift-card/{id}', [App\Http\Controllers\GiftCardController::class, 'update'])->name('gift-card.update');
    Route::post('gift-card/{id}/toggle', [App\Http\Controllers\GiftCardController::class, 'toggle'])->name('gift-card.toggle');
});

Route::middleware(['permission:roles,role.index'])->group(function () {
    Route::get('role', [App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
});
Route::middleware(['permission:roles,role.save'])->group(function () {
    Route::get('role/save', [App\Http\Controllers\RoleController::class, 'save'])->name('role.save');
});
Route::middleware(['permission:roles,role.store'])->group(function () {
    Route::post('role/store', [App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
});
Route::middleware(['permission:roles,role.delete'])->group(function () {
    Route::get('role/delete/{id}', [App\Http\Controllers\RoleController::class, 'delete'])->name('role.delete');
});
Route::middleware(['permission:roles,role.edit'])->group(function () {
    Route::get('role/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit');
});

Route::middleware(['permission:roles,role.update'])->group(function () {
    Route::post('role/update/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('role.update');

});
Route::middleware(['permission:admins,admin.users'])->group(function () {

    Route::get('admin-users', [App\Http\Controllers\UserController::class, 'adminUsers'])->name('admin.users');
});
Route::middleware(['permission:admins,admin.users.create'])->group(function () {
    Route::get('admin-users/create', [App\Http\Controllers\UserController::class, 'createAdminUsers'])->name('admin.users.create');
});
Route::middleware(['permission:admins,admin.users.store'])->group(function () {
    Route::post('admin-users/store', [App\Http\Controllers\UserController::class, 'storeAdminUsers'])->name('admin.users.store');

});
Route::middleware(['permission:admins,admin.users.delete'])->group(function () {
    Route::get('admin-users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteAdminUsers'])->name('admin.users.delete');

});
Route::middleware(['permission:admins,admin.users.edit'])->group(function () {
    Route::get('admin-users/edit/{id}', [App\Http\Controllers\UserController::class, 'editAdminUsers'])->name('admin.users.edit');

});
Route::middleware(['permission:admins,admin.users.update'])->group(function () {
    Route::post('admin-users/update/{id}', [App\Http\Controllers\UserController::class, 'updateAdminUsers'])->name('admin.users.update');

});
Route::middleware(['permission:admins,admin.users.delete'])->group(function () {
    Route::get('admin-users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteAdminUsers'])->name('admin.users.delete');

});
Route::middleware(['permission:zone,zone.list'])->group(function () {
    Route::get('zone', [App\Http\Controllers\ZoneController::class, 'index'])->name('zone');
});
Route::middleware(['permission:zone,zone.create'])->group(function () {
    Route::get('/zone/create', [App\Http\Controllers\ZoneController::class, 'create'])->name('zone.create');
});
Route::middleware(['permission:zone,zone.edit'])->group(function () {
    Route::get('/zone/edit/{id}', [App\Http\Controllers\ZoneController::class, 'edit'])->name('zone.edit');
});

// Zone API routes for SQL database
Route::middleware(['permission:zone,zone.list'])->group(function () {
    Route::get('/zone/data', [App\Http\Controllers\ZoneController::class, 'getZonesData'])->name('zone.data');
});

Route::middleware(['permission:zone,zone'])->group(function () {
    Route::get('/zone/{id}/data', [App\Http\Controllers\ZoneController::class, 'getZoneById'])->name('zone.getById');
});

Route::middleware(['permission:zone,zone.create'])->group(function () {
    Route::post('/zone', [App\Http\Controllers\ZoneController::class, 'store'])->name('zone.store');
});

Route::middleware(['permission:zone,zone.edit'])->group(function () {
    Route::put('/zone/{id}', [App\Http\Controllers\ZoneController::class, 'update'])->name('zone.update');
    Route::post('/zone/{id}/toggle-status', [App\Http\Controllers\ZoneController::class, 'toggleStatus'])->name('zone.toggle-status');
});

Route::middleware(['permission:zone,zone.delete'])->group(function () {
    Route::delete('/zone/{id}', [App\Http\Controllers\ZoneController::class, 'destroy'])->name('zone.delete');
    Route::post('/zone/delete-multiple', [App\Http\Controllers\ZoneController::class, 'deleteMultiple'])->name('zone.delete-multiple');
});

Route::middleware(['permission:documents,documents.edit'])->group(function () {
    Route::get('/documents/edit/{id}', [App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
});
Route::middleware(['permission:documents,documents.create'])->group(function () {
    Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
});
Route::middleware(['permission:documents,documents.list'])->group(function () {
    Route::get('documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents');
});
Route::middleware(['permission:vendors-document,vendor.document.list'])->group(function () {
    Route::get('vendors/document-list/{id}', [App\Http\Controllers\RestaurantController::class, 'DocumentList'])->name('vendors.document');
});
Route::middleware(['permission:vendors-document,vendor.document.edit'])->group(function () {
    Route::get('/vendors/document/upload/{driverId}/{id}', [App\Http\Controllers\RestaurantController::class, 'DocumentUpload'])->name('vendors.document.upload');
});
Route::middleware(['permission:drivers-document,driver.document.list'])->group(function () {
    Route::get('drivers/document-list/{id}', [App\Http\Controllers\DriverController::class, 'DocumentList'])->name('drivers.document');
});
Route::middleware(['permission:drivers-document,driver.document.edit'])->group(function () {
    Route::get('/drivers/document/upload/{driverId}/{id}', [App\Http\Controllers\DriverController::class, 'DocumentUpload'])->name('drivers.document.upload');
});
Route::post('send-notification', [App\Http\Controllers\NotificationController::class, 'sendNotification'])->name('send-notification');

Route::post('store-firebase-service', [App\Http\Controllers\HomeController::class, 'storeFirebaseService'])->name('store-firebase-service');

Route::post('pay-to-user', [App\Http\Controllers\UserController::class, 'payToUser'])->name('pay.user');
Route::post('check-payout-status', [App\Http\Controllers\UserController::class, 'checkPayoutStatus'])->name('check.payout.status');

Route::middleware(['permission:on-board,onboard.list'])->group(function () {
    Route::get('/on-board', [App\Http\Controllers\OnBoardController::class, 'index'])->name('on-board');
});
Route::middleware(['permission:on-board,onboard.edit'])->group(function () {
    Route::get('/on-board/save/{id}', [App\Http\Controllers\OnBoardController::class, 'show'])->name('on-board.save');
});
Route::middleware(['permission:subscription-plans,subscription-plans'])->group(function () {
    Route::get('/subscription-plans', [App\Http\Controllers\SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
    Route::get('/current-subscriber/{id}', [App\Http\Controllers\RestaurantController::class, 'currentSubscriberList'])->name('current-subscriber.list');

});
Route::middleware(['permission:subscription-plans,subscription-plans.' . ((str_contains(Request::url(), 'save')) ? (explode("save", Request::url())[1] ? "edit" : "create") : Request::url())])->group(function () {
    Route::get('/subscription-plans/save/{id?}', [App\Http\Controllers\SubscriptionPlanController::class, 'save'])->name('subscription-plans.save');
});
Route::middleware(['permission:vendors,vendors.edit'])->group(function () {
    Route::get('/vendor/edit/{id}', [App\Http\Controllers\RestaurantController::class, 'vendorEdit'])->name('vendor.edit');
});
Route::middleware(['permission:subscription-history,subscription.history'])->group(function () {
    Route::get('/vendor/subscription-plan/history/{id?}', [App\Http\Controllers\RestaurantController::class, 'vendorSubscriptionPlanHistory'])->name('vendor.subscriptionPlanHistory');
});
Route::get('/restaurantFilters', [App\Http\Controllers\RestaurantFiltersController::class, 'index'])->name('restaurantFilters');
Route::get('/restaurantFilters/create', [App\Http\Controllers\RestaurantFiltersController::class, 'create'])->name('restaurantFilters.create');
Route::get('/restaurantFilters/edit/{id}', [App\Http\Controllers\RestaurantFiltersController::class, 'edit'])->name('restaurantFilters.edit');


Route::middleware(['permission:media,media'])->group(function () {
    Route::get('/media', [App\Http\Controllers\MediaController::class, 'index'])->name('media.index');
    Route::get('/media/data', [App\Http\Controllers\MediaController::class, 'data'])->name('media.data');
});
Route::middleware(['permission:media,media.edit'])->group(function () {
    Route::get('/media/edit/{id}', [App\Http\Controllers\MediaController::class, 'edit'])->name('media.edit');
    Route::get('/media/json/{id}', [App\Http\Controllers\MediaController::class, 'json'])->name('media.json');
    Route::post('/media/{id}', [App\Http\Controllers\MediaController::class, 'update'])->name('media.update');
});
Route::middleware(['permission:media,media.create'])->group(function () {
    Route::get('/media/create', [App\Http\Controllers\MediaController::class, 'create'])->name('media.create');
    Route::post('/media', [App\Http\Controllers\MediaController::class, 'store'])->name('media.store');
});
Route::middleware(['permission:media,media.delete'])->group(function () {
    Route::get('/media/delete/{id}', [App\Http\Controllers\MediaController::class, 'delete'])->name('media.delete');
    Route::post('/media/{id}/delete', [App\Http\Controllers\MediaController::class, 'destroy'])->name('media.delete.post');
    Route::post('/media/bulk-delete', [App\Http\Controllers\MediaController::class, 'bulkDelete'])->name('media.bulkDelete');
});


Route::get('/create-package', function () {
    return view('new_ui.create-package');
});
Route::get('/add-subscription', function () {
    return view('new_ui.add-subscription');
});
Route::get('/change-subscription', function () {
    return view('new_ui.change-subscription');
});
Route::get('/edit-subscription', function () {
    return view('new_ui.edit-subscription');
});
Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');
Route::get('/users/download-template', [App\Http\Controllers\UserController::class, 'downloadTemplate'])->name('users.download-template');
Route::post('/vendors/import', [App\Http\Controllers\RestaurantController::class, 'importVendors'])->name('vendors.import');
Route::get('/vendors/download-template', [App\Http\Controllers\RestaurantController::class, 'downloadVendorsTemplate'])->name('vendors.download-template');

// Restaurant bulk import routes
Route::post('/restaurants/bulk-import', [App\Http\Controllers\RestaurantController::class, 'bulkUpdate'])->name('restaurants.bulk-import');
Route::get('/restaurants/download-template', [App\Http\Controllers\RestaurantController::class, 'downloadBulkUpdateTemplate'])->name('restaurants.download-template');

// Local Performance Optimization Routes
Route::prefix('performance')->group(function () {
    Route::get('/', [App\Http\Controllers\LocalPerformanceController::class, 'index'])->name('performance.index');
    Route::get('/dashboard-stats', [App\Http\Controllers\LocalPerformanceController::class, 'getDashboardStats'])->name('performance.dashboard-stats');
    Route::post('/clear-cache', [App\Http\Controllers\LocalPerformanceController::class, 'clearCacheByCategory'])->name('performance.clear-cache');
    Route::post('/optimize', [App\Http\Controllers\LocalPerformanceController::class, 'optimizeApplication'])->name('performance.optimize');
    Route::get('/cache-stats', [App\Http\Controllers\LocalPerformanceController::class, 'getCacheStats'])->name('performance.cache-stats');
    Route::get('/test-cache', [App\Http\Controllers\LocalPerformanceController::class, 'testCachePerformance'])->name('performance.test-cache');
});

// Database Cache Testing Routes
Route::prefix('cache-test')->group(function () {
    Route::get('/database', [App\Http\Controllers\CacheTestController::class, 'testDatabaseCache'])->name('cache-test.database');
    Route::get('/session', [App\Http\Controllers\CacheTestController::class, 'testSessionStorage'])->name('cache-test.session');
    Route::get('/config', [App\Http\Controllers\CacheTestController::class, 'getCacheConfig'])->name('cache-test.config');
});


Route::middleware(['permission:admin-commission,settings.app.adminCommission'])->group(function () {

    Route::get('app/adminCommission', [App\Http\Controllers\SettingsController::class, 'adminCommission'])->name('settings.app.adminCommission');

});

Route::middleware(['permission:radius,settings.app.radiusConfiguration'])->group(function () {

    Route::get('app/radiusConfiguration', [App\Http\Controllers\SettingsController::class, 'radiosConfiguration'])->name('settings.app.radiusConfiguration');

});

Route::middleware(['permission:dinein,settings.app.bookTable'])->group(function () {

    Route::get('app/bookTable', [App\Http\Controllers\SettingsController::class, 'bookTable'])->name('settings.app.bookTable');

});

Route::middleware(['permission:delivery-charge,settings.app.deliveryCharge'])->group(function () {

    Route::get('app/deliveryCharge', [App\Http\Controllers\SettingsController::class, 'deliveryCharge'])->name('settings.app.deliveryCharge');

});

// Route::middleware(['permission:price-setting,settings.app.priceSetting'])->group(function () {

Route::get('app/priceSetting', [App\Http\Controllers\SettingsController::class, 'priceSetting'])->name('settings.app.priceSettings');

// });

Route::middleware(['permission:document-verification,settings.app.documentVerification'])->group(function () {

    Route::get('app/documentVerification', [App\Http\Controllers\SettingsController::class, 'documentVerification'])->name('settings.app.documentVerification');

});


Route::middleware(['permission:payment-method,payment-method'])->group(function () {

    Route::get('payment/stripe', [App\Http\Controllers\SettingsController::class, 'stripe'])->name('payment.stripe');

    Route::get('payment/applepay', [App\Http\Controllers\SettingsController::class, 'applepay'])->name('payment.applepay');

    Route::get('payment/razorpay', [App\Http\Controllers\SettingsController::class, 'razorpay'])->name('payment.razorpay');

    Route::get('payment/cod', [App\Http\Controllers\SettingsController::class, 'cod'])->name('payment.cod');

    Route::get('payment/paypal', [App\Http\Controllers\SettingsController::class, 'paypal'])->name('payment.paypal');

    Route::get('payment/paytm', [App\Http\Controllers\SettingsController::class, 'paytm'])->name('payment.paytm');

    Route::get('payment/wallet', [App\Http\Controllers\SettingsController::class, 'wallet'])->name('payment.wallet');

    Route::get('payment/payfast', [App\Http\Controllers\SettingsController::class, 'payfast'])->name('payment.payfast');

    Route::get('payment/paystack', [App\Http\Controllers\SettingsController::class, 'paystack'])->name('payment.paystack');

    Route::get('payment/flutterwave', [App\Http\Controllers\SettingsController::class, 'flutterwave'])->name('payment.flutterwave');

    Route::get('payment/mercadopago', [App\Http\Controllers\SettingsController::class, 'mercadopago'])->name('payment.mercadopago');

    Route::get('payment/xendit', [App\Http\Controllers\SettingsController::class, 'xendit'])->name('payment.xendit');

    Route::get('payment/orangepay', [App\Http\Controllers\SettingsController::class, 'orangepay'])->name('payment.orangepay');

    Route::get('payment/midtrans', [App\Http\Controllers\SettingsController::class, 'midtrans'])->name('payment.midtrans');

});


Route::middleware(['permission:language,settings.app.languages'])->group(function () {

    Route::get('app/languages', [App\Http\Controllers\SettingsController::class, 'languages'])->name('settings.app.languages');


});

Route::middleware(['permission:language,settings.app.languages.create'])->group(function () {

    Route::get('app/languages/create', [App\Http\Controllers\SettingsController::class, 'languagescreate'])->name('settings.app.languages.create');


});

Route::middleware(['permission:language,settings.app.languages.edit'])->group(function () {

    Route::get('app/languages/edit/{id}', [App\Http\Controllers\SettingsController::class, 'languagesedit'])->name('settings.app.languages.edit');


});

Route::middleware(['permission:special-offer,setting.specialOffer'])->group(function () {

    Route::get('app/specialOffer', [App\Http\Controllers\SettingsController::class, 'specialOffer'])->name('setting.specialOffer');

});


Route::get('app/story', [App\Http\Controllers\SettingsController::class, 'story'])->name('setting.story');

Route::get('app/notifications', [App\Http\Controllers\SettingsController::class, 'notifications'])->name('settings.app.notifications');

Route::get('mobile/globals', [App\Http\Controllers\SettingsController::class, 'mobileGlobals'])->name('settings.mobile.globals');


Route::middleware(['permission:dinein-orders,restaurants.booktable'])->group(function () {

    Route::get('/booktable/{id}', [App\Http\Controllers\BookTableController::class, 'index'])->name('restaurants.booktable');


});

Route::middleware(['permission:dinein-orders,booktable.edit'])->group(function () {

    Route::get('/booktable/edit/{id}', [App\Http\Controllers\BookTableController::class, 'edit'])->name('booktable.edit');

});

Route::post('/sendnotification', [App\Http\Controllers\BookTableController::class, 'sendnotification'])->name('sendnotification');


Route::middleware(['permission:general-notifications,notification'])->group(function () {

    Route::get('/notification', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification');

});

Route::middleware(['permission:general-notifications,notification.send'])->group(function () {

    Route::get('/notification/send', [App\Http\Controllers\NotificationController::class, 'send'])->name('notification.send');

});

Route::post('broadcastnotification', [App\Http\Controllers\NotificationController::class, 'broadcastnotification'])->name('broadcastnotification');


Route::middleware(['permission:payout-request,payoutRequests.drivers'])->group(function () {

    Route::get('/payoutRequests/drivers', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers');

    Route::get('/payoutRequests/drivers/{id}', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers.view');

});

Route::middleware(['permission:payout-request,payoutRequests.restaurants'])->group(function () {

    Route::get('/payoutRequests/restaurants', [App\Http\Controllers\PayoutRequestController::class, 'restaurant'])->name('payoutRequests.restaurants');

    Route::get('/payoutRequests/restaurants/{id}', [App\Http\Controllers\PayoutRequestController::class, 'restaurant'])->name('payoutRequests.restaurants.view');


});

Route::get('order_transactions', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions');

Route::get('/order_transactions/{id}', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions.index');

// Test routes removed to prevent resource issues on shared hosting


// Activity Log Routes

Route::middleware(['permission:activity-logs,activity-logs'])->group(function () {
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs');
});

Route::post('/api/activity-logs/log', [App\Http\Controllers\ActivityLogController::class, 'logActivity'])->name('api.activity-logs.log');

Route::get('/api/activity-logs/module/{module}', [App\Http\Controllers\ActivityLogController::class, 'getModuleLogs'])->name('api.activity-logs.module');

Route::get('/api/activity-logs/all', [App\Http\Controllers\ActivityLogController::class, 'getAllLogs'])->name('api.activity-logs.all');

Route::get('/api/activity-logs/cuisines', [App\Http\Controllers\ActivityLogController::class, 'getCuisinesLogs'])->name('api.activity-logs.cuisines');


Route::get('payment/success', [App\Http\Controllers\PaymentController::class, 'paymentsuccess'])->name('payment.success');

Route::get('payment/failed', [App\Http\Controllers\PaymentController::class, 'paymentfailed'])->name('payment.failed');

Route::get('payment/pending', [App\Http\Controllers\PaymentController::class, 'paymentpending'])->name('payment.pending');


Route::middleware(['permission:banners,setting.banners'])->group(function () {
    Route::get('/banners', [App\Http\Controllers\MenuItemController::class, 'index'])->name('setting.banners');
});

Route::middleware(['permission:banners,setting.banners.create'])->group(function () {
    Route::get('/banners/create', [App\Http\Controllers\MenuItemController::class, 'create'])->name('setting.banners.create');
});

Route::middleware(['permission:banners,setting.banners.edit'])->group(function () {
    Route::get('/banners/edit/{id}', [App\Http\Controllers\MenuItemController::class, 'edit'])->name('setting.banners.edit');
});

// Mart Banner Items Routes
Route::middleware(['permission:mart_banners,mart_banners'])->group(function () {
    Route::get('/mart-banners', [App\Http\Controllers\MartBannerController::class, 'index'])->name('mart.banners');
});

Route::middleware(['permission:mart_banners,mart_banners.create'])->group(function () {
    Route::get('/mart-banners/create', [App\Http\Controllers\MartBannerController::class, 'create'])->name('mart.banners.create');
    Route::post('/mart-banners', [App\Http\Controllers\MartBannerController::class, 'store'])->name('mart.banners.store');
});

Route::middleware(['permission:mart_banners,mart_banners.edit'])->group(function () {
    Route::get('/mart-banners/edit/{id}', [App\Http\Controllers\MartBannerController::class, 'edit'])->name('mart.banners.edit');
    Route::put('/mart-banners/{id}', [App\Http\Controllers\MartBannerController::class, 'update'])->name('mart.banners.update');
    Route::post('/mart-banners/{id}/toggle-publish', [App\Http\Controllers\MartBannerController::class, 'togglePublish'])->name('mart.banners.togglePublish');
});

Route::middleware(['permission:mart_banners,mart_banners.delete'])->group(function () {
    Route::delete('/mart-banners/{id}', [App\Http\Controllers\MartBannerController::class, 'destroy'])->name('mart.banners.destroy');
});
Route::middleware(['permission:item-attribute,attributes'])->group(function () {
    Route::get('/attributes', [App\Http\Controllers\AttributeController::class, 'index'])->name('attributes');
});
Route::middleware(['permission:item-attribute,attributes.edit'])->group(function () {
    Route::get('/attributes/edit/{id}', [App\Http\Controllers\AttributeController::class, 'edit'])->name('attributes.edit');
});
Route::middleware(['permission:item-attribute,attributes.create'])->group(function () {
    Route::get('/attributes/create', [App\Http\Controllers\AttributeController::class, 'create'])->name('attributes.create');
});

Route::middleware(['permission:review-attribute,reviewattributes'])->group(function () {
    Route::get('/reviewattributes', [App\Http\Controllers\ReviewAttributeController::class, 'index'])->name('reviewattributes');
});
Route::middleware(['permission:review-attribute,reviewattributes.edit'])->group(function () {
    Route::get('/reviewattributes/edit/{id}', [App\Http\Controllers\ReviewAttributeController::class, 'edit'])->name('reviewattributes.edit');
});
Route::middleware(['permission:review-attribute,reviewattributes.create'])->group(function () {
    Route::get('/reviewattributes/create', [App\Http\Controllers\ReviewAttributeController::class, 'create'])->name('reviewattributes.create');
});

// Review Attributes API Route
Route::get('/api/review-attributes', [App\Http\Controllers\ReviewAttributeController::class, 'getAll'])->name('api.review-attributes.get-all');

Route::middleware(['permission:footer,footerTemplate'])->group(function () {
    Route::get('footerTemplate', [App\Http\Controllers\SettingsController::class, 'footerTemplate'])->name('footerTemplate');
});
Route::middleware(['permission:home-page,homepageTemplate'])->group(function () {
    Route::get('/homepageTemplate', [App\Http\Controllers\SettingsController::class, 'homepageTemplate'])->name('homepageTemplate');
});
Route::middleware(['permission:cms,cms'])->group(function () {
    Route::get('cms', [App\Http\Controllers\CmsController::class, 'index'])->name('cms');
});
Route::middleware(['permission:cms,cms.edit'])->group(function () {
    Route::get('/cms/edit/{id}', [App\Http\Controllers\CmsController::class, 'edit'])->name('cms.edit');
});
Route::middleware(['permission:cms,cms.create'])->group(function () {
    Route::get('/cms/create', [App\Http\Controllers\CmsController::class, 'create'])->name('cms.create');
});
Route::middleware(['permission:reports,report.index'])->group(function () {
    Route::get('report/{type}', [App\Http\Controllers\ReportController::class, 'index'])->name('report.index');
});

Route::middleware(['permission:tax,tax'])->group(function () {
    Route::get('/tax', [App\Http\Controllers\TaxController::class, 'index'])->name('tax');
});
Route::middleware(['permission:tax,tax.edit'])->group(function () {
    Route::get('/tax/edit/{id}', [App\Http\Controllers\TaxController::class, 'edit'])->name('tax.edit');
});
Route::middleware(['permission:tax,tax.create'])->group(function () {
    Route::get('/tax/create', [App\Http\Controllers\TaxController::class, 'create'])->name('tax.create');
});

Route::middleware(['permission:email-template,email-templates.index'])->group(function () {
    Route::get('email-templates', [App\Http\Controllers\SettingsController::class, 'emailTemplatesIndex'])->name('email-templates.index');
});
Route::middleware(['permission:email-template,email-templates.edit'])->group(function () {
    Route::get('email-templates/save/{id?}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesSave'])->name('email-templates.save');

});
Route::middleware(['permission:email-template,email-templates.delete'])->group(function () {
    Route::get('email-templates/delete/{id}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesDelete'])->name('email-templates.delete');

});
Route::post('send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');

Route::middleware(['permission:gift-cards,gift-card.index'])->group(function () {
    Route::get('gift-card', [App\Http\Controllers\GiftCardController::class, 'index'])->name('gift-card.index');
});
Route::middleware(['permission:gift-cards,gift-card.save'])->group(function () {
    Route::get('gift-card/save/{id?}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.save');

});
Route::middleware(['permission:gift-cards,gift-card.edit'])->group(function () {
    Route::get('gift-card/edit/{id}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.edit');
});

Route::middleware(['permission:roles,role.index'])->group(function () {
    Route::get('role', [App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
});
Route::middleware(['permission:roles,role.save'])->group(function () {
    Route::get('role/save', [App\Http\Controllers\RoleController::class, 'save'])->name('role.save');
});
Route::middleware(['permission:roles,role.store'])->group(function () {
    Route::post('role/store', [App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
});
Route::middleware(['permission:roles,role.delete'])->group(function () {
    Route::get('role/delete/{id}', [App\Http\Controllers\RoleController::class, 'delete'])->name('role.delete');
});
Route::middleware(['permission:roles,role.edit'])->group(function () {
    Route::get('role/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit');
});

Route::middleware(['permission:roles,role.update'])->group(function () {
    Route::post('role/update/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('role.update');

});
Route::middleware(['permission:admins,admin.users'])->group(function () {

    Route::get('admin-users', [App\Http\Controllers\UserController::class, 'adminUsers'])->name('admin.users');
});
Route::middleware(['permission:admins,admin.users.create'])->group(function () {
    Route::get('admin-users/create', [App\Http\Controllers\UserController::class, 'createAdminUsers'])->name('admin.users.create');
});
Route::middleware(['permission:admins,admin.users.store'])->group(function () {
    Route::post('admin-users/store', [App\Http\Controllers\UserController::class, 'storeAdminUsers'])->name('admin.users.store');

});
Route::middleware(['permission:admins,admin.users.delete'])->group(function () {
    Route::get('admin-users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteAdminUsers'])->name('admin.users.delete');

});
Route::middleware(['permission:admins,admin.users.edit'])->group(function () {
    Route::get('admin-users/edit/{id}', [App\Http\Controllers\UserController::class, 'editAdminUsers'])->name('admin.users.edit');

});
Route::middleware(['permission:admins,admin.users.update'])->group(function () {
    Route::post('admin-users/update/{id}', [App\Http\Controllers\UserController::class, 'updateAdminUsers'])->name('admin.users.update');

});
Route::middleware(['permission:admins,admin.users.delete'])->group(function () {
    Route::get('admin-users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteAdminUsers'])->name('admin.users.delete');

});
Route::middleware(['permission:documents,documents.edit'])->group(function () {
    Route::get('/documents/edit/{id}', [App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
});
Route::middleware(['permission:documents,documents.create'])->group(function () {
    Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
});
Route::middleware(['permission:documents,documents.list'])->group(function () {
    Route::get('documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents');
});
Route::middleware(['permission:vendors-document,vendor.document.list'])->group(function () {
    Route::get('vendors/document-list/{id}', [App\Http\Controllers\RestaurantController::class, 'DocumentList'])->name('vendors.document');
});
Route::middleware(['permission:vendors-document,vendor.document.edit'])->group(function () {
    Route::get('/vendors/document/upload/{driverId}/{id}', [App\Http\Controllers\RestaurantController::class, 'DocumentUpload'])->name('vendors.document.upload');
});
Route::middleware(['permission:drivers-document,driver.document.list'])->group(function () {
    Route::get('drivers/document-list/{id}', [App\Http\Controllers\DriverController::class, 'DocumentList'])->name('drivers.document');
});
Route::middleware(['permission:drivers-document,driver.document.edit'])->group(function () {
    Route::get('/drivers/document/upload/{driverId}/{id}', [App\Http\Controllers\DriverController::class, 'DocumentUpload'])->name('drivers.document.upload');
});
// Duplicate routes removed


Route::middleware(['permission:on-board,onboard.list'])->group(function () {

    Route::get('/on-board', [App\Http\Controllers\OnBoardController::class, 'index'])->name('on-board');
    Route::get('/on-board/data', [App\Http\Controllers\OnBoardController::class, 'data'])->name('on-board.data');
    Route::get('/on-board/json/{id}', [App\Http\Controllers\OnBoardController::class, 'json'])->name('on-board.json');

});

Route::middleware(['permission:on-board,onboard.edit'])->group(function () {

    Route::get('/on-board/save/{id}', [App\Http\Controllers\OnBoardController::class, 'show'])->name('on-board.save');
    Route::post('/on-board/{id}', [App\Http\Controllers\OnBoardController::class, 'update'])->name('on-board.update');

});
Route::middleware(['permission:on-board,onboard.edit'])->group(function () {
    Route::post('/on-board/{id}/delete', [App\Http\Controllers\OnBoardController::class, 'destroy'])->name('on-board.delete.post');
    Route::post('/on-board/bulk-delete', [App\Http\Controllers\OnBoardController::class, 'bulkDelete'])->name('on-board.bulkDelete');
});

Route::middleware(['permission:subscription-plans,subscription-plans'])->group(function () {

    Route::get('/subscription-plans', [App\Http\Controllers\SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');

    Route::get('/current-subscriber/{id}', [App\Http\Controllers\RestaurantController::class, 'currentSubscriberList'])->name('current-subscriber.list');


});

Route::middleware(['permission:subscription-plans,subscription-plans.' . ((str_contains(Request::url(), 'save')) ? (explode("save", Request::url())[1] ? "edit" : "create") : Request::url())])->group(function () {

    Route::get('/subscription-plans/save/{id?}', [App\Http\Controllers\SubscriptionPlanController::class, 'save'])->name('subscription-plans.save');

});

Route::middleware(['permission:vendors,vendors.edit'])->group(function () {

    Route::get('/vendor/edit/{id}', [App\Http\Controllers\RestaurantController::class, 'vendorEdit'])->name('vendor.edit');

});

Route::middleware(['permission:subscription-history,subscription.history'])->group(function () {

    Route::get('/vendor/subscription-plan/history/{id?}', [App\Http\Controllers\RestaurantController::class, 'vendorSubscriptionPlanHistory'])->name('vendor.subscriptionPlanHistory');

});

Route::get('/restaurantFilters', [App\Http\Controllers\RestaurantFiltersController::class, 'index'])->name('restaurantFilters');

Route::get('/restaurantFilters/create', [App\Http\Controllers\RestaurantFiltersController::class, 'create'])->name('restaurantFilters.create');

Route::get('/restaurantFilters/edit/{id}', [App\Http\Controllers\RestaurantFiltersController::class, 'edit'])->name('restaurantFilters.edit');


Route::middleware(['permission:media,media'])->group(function () {

    Route::get('/media', [App\Http\Controllers\MediaController::class, 'index'])->name('media.index');

});

Route::middleware(['permission:media,media.edit'])->group(function () {

    Route::get('/media/edit/{id}', [App\Http\Controllers\MediaController::class, 'edit'])->name('media.edit');

});

Route::middleware(['permission:media,media.create'])->group(function () {

    Route::get('/media/create', [App\Http\Controllers\MediaController::class, 'create'])->name('media.create');

});

Route::middleware(['permission:media,media.delete'])->group(function () {

    Route::get('/media/delete/{id}', [App\Http\Controllers\MediaController::class, 'delete'])->name('media.delete');

});


Route::get('/create-package', function () {

    return view('new_ui.create-package');

});

Route::get('/add-subscription', function () {

    return view('new_ui.add-subscription');

});

Route::get('/change-subscription', function () {

    return view('new_ui.change-subscription');

});

Route::get('/edit-subscription', function () {

    return view('new_ui.edit-subscription');

});

Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');

Route::get('/users/download-template', [App\Http\Controllers\UserController::class, 'downloadTemplate'])->name('users.download-template');

Route::post('/vendors/import', [App\Http\Controllers\RestaurantController::class, 'importVendors'])->name('vendors.import');

Route::get('/vendors/download-template', [App\Http\Controllers\RestaurantController::class, 'downloadVendorsTemplate'])->name('vendors.download-template');

// Vendor API routes for SQL database
// Note: Specific routes must come BEFORE wildcard routes
Route::middleware(['permission:vendors,vendors'])->group(function () {
    Route::get('/vendors/data', [App\Http\Controllers\RestaurantController::class, 'getVendorsData'])->name('vendors.data');
    Route::get('/vendors/zones', [App\Http\Controllers\RestaurantController::class, 'getZones'])->name('vendors.zones');
    Route::get('/vendors/subscription-plans', [App\Http\Controllers\RestaurantController::class, 'getSubscriptionPlans'])->name('vendors.subscription-plans');
    Route::get('/vendors/placeholder-image', [App\Http\Controllers\RestaurantController::class, 'getPlaceholderImage'])->name('vendors.placeholder-image');
});

// Debug route (place before wildcard routes)
Route::get('/vendors/debug/{id}', [App\Http\Controllers\RestaurantController::class, 'debugVendor'])->name('vendors.debug');

// Vendor data endpoint - accessible by vendors module access (used by both view and edit pages)
Route::middleware(['permission:vendors,vendors'])->group(function () {
    Route::get('/vendors/{id}/data', [App\Http\Controllers\RestaurantController::class, 'getVendorById'])->name('vendors.getById');
});

Route::middleware(['permission:vendors,vendors.edit'])->group(function () {
    Route::put('/vendors/{id}', [App\Http\Controllers\RestaurantController::class, 'updateVendor'])->name('vendors.update');
    Route::post('/vendors/{id}/toggle-status', [App\Http\Controllers\RestaurantController::class, 'toggleVendorStatus'])->name('vendors.toggle-status');
});

Route::middleware(['permission:vendors,vendors.create'])->group(function () {
    Route::post('/vendors', [App\Http\Controllers\RestaurantController::class, 'createVendor'])->name('vendors.create.post');
});

Route::middleware(['permission:vendors,vendors.delete'])->group(function () {
    Route::delete('/vendors/{id}', [App\Http\Controllers\RestaurantController::class, 'deleteVendor'])->name('vendors.delete');
});

// Restaurant API routes for SQL database
Route::middleware(['permission:restaurants,restaurants'])->group(function () {
    Route::get('/restaurants/data', [App\Http\Controllers\RestaurantController::class, 'getRestaurantsData'])->name('restaurants.data');
    Route::get('/restaurants/categories', [App\Http\Controllers\RestaurantController::class, 'getCategories'])->name('restaurants.categories');
    Route::get('/restaurants/cuisines', [App\Http\Controllers\RestaurantController::class, 'getCuisines'])->name('restaurants.cuisines');
    Route::get('/restaurants/{id}/data', [App\Http\Controllers\RestaurantController::class, 'getRestaurantById'])->name('restaurants.getById');
    Route::get('/restaurants/{id}/stats', [App\Http\Controllers\RestaurantController::class, 'getRestaurantStats'])->name('restaurants.stats');
    Route::get('/api/users/{id}', [App\Http\Controllers\RestaurantController::class, 'getUserById'])->name('users.getById');
    Route::get('/api/users/{id}/wallet-balance', [App\Http\Controllers\RestaurantController::class, 'getWalletBalance'])->name('users.wallet-balance');
    Route::post('/api/users/wallet/add', [App\Http\Controllers\RestaurantController::class, 'addWalletAmount'])->name('users.wallet.add');
    Route::get('/api/users/{id}/subscription-history', [App\Http\Controllers\RestaurantController::class, 'getSubscriptionHistory'])->name('users.subscription-history');
    Route::get('/api/email-templates/{type}', [App\Http\Controllers\RestaurantController::class, 'getEmailTemplate'])->name('email-templates.get');
});

// Zone API endpoint (public or with minimal permission)
Route::get('/api/zone/{id}', [App\Http\Controllers\RestaurantController::class, 'getZoneById'])->name('zone.getById');

// Restaurant create endpoint
Route::middleware(['permission:restaurants,restaurants.create'])->group(function () {
    Route::post('/api/restaurants/create', [App\Http\Controllers\RestaurantController::class, 'createRestaurant'])->name('restaurants.create.api');
});

Route::middleware(['permission:restaurants,restaurants.edit'])->group(function () {
    Route::put('/restaurants/{id}', [App\Http\Controllers\RestaurantController::class, 'updateRestaurant'])->name('restaurants.update');
    Route::post('/restaurants/{id}/toggle-status', [App\Http\Controllers\RestaurantController::class, 'toggleRestaurantStatus'])->name('restaurants.toggle-status');
    Route::post('/restaurants/{id}/toggle-open', [App\Http\Controllers\RestaurantController::class, 'toggleRestaurantOpenStatus'])->name('restaurants.toggle-open');
});

Route::middleware(['permission:restaurants,restaurants.delete'])->group(function () {
    Route::delete('/restaurants/{id}', [App\Http\Controllers\RestaurantController::class, 'deleteRestaurant'])->name('restaurants.delete');
});

// Restaurant bulk import routes

Route::post('/restaurants/bulk-import', [App\Http\Controllers\RestaurantController::class, 'bulkUpdate'])->name('restaurants.bulk-import');

Route::get('/restaurants/download-template', [App\Http\Controllers\RestaurantController::class, 'downloadBulkUpdateTemplate'])->name('restaurants.download-template');


// Local Performance Optimization Routes

Route::prefix('performance')->group(function () {

    Route::get('/', [App\Http\Controllers\LocalPerformanceController::class, 'index'])->name('performance.index');

    Route::get('/dashboard-stats', [App\Http\Controllers\LocalPerformanceController::class, 'getDashboardStats'])->name('performance.dashboard-stats');

    Route::post('/clear-cache', [App\Http\Controllers\LocalPerformanceController::class, 'clearCacheByCategory'])->name('performance.clear-cache');

    Route::post('/optimize', [App\Http\Controllers\LocalPerformanceController::class, 'optimizeApplication'])->name('performance.optimize');

    Route::get('/cache-stats', [App\Http\Controllers\LocalPerformanceController::class, 'getCacheStats'])->name('performance.cache-stats');

    Route::get('/test-cache', [App\Http\Controllers\LocalPerformanceController::class, 'testCachePerformance'])->name('performance.test-cache');

});

Route::prefix('admin/seo')->middleware('')->group(function () {
    Route::get('/', [SeoController::class, 'index'])->name('seo.index');
    Route::get('/{id}/edit', [SeoController::class, 'edit'])->name('seo.edit');
    Route::put('/{id}', [SeoController::class, 'update'])->name('seo.update');
    Route::get('/sitemap', [SitemapController::class, 'index'])->name('seo.sitemap');
});


// Database Cache Testing Routes
Route::prefix('cache-test')->group(function () {

    Route::get('/database', [App\Http\Controllers\CacheTestController::class, 'testDatabaseCache'])->name('cache-test.database');

    Route::get('/session', [App\Http\Controllers\CacheTestController::class, 'testSessionStorage'])->name('cache-test.session');

    Route::get('/config', [App\Http\Controllers\CacheTestController::class, 'getCacheConfig'])->name('cache-test.config');

});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::any('/dashboard/clear-cache', [DashboardController::class, 'clearCache']);
Route::get('/dashboard/cache-stats', [DashboardController::class, 'getCacheStats']);
