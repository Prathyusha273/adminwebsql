<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Vendor;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function social()
    {
        return view("settings.app.social");
    }

    public function globals()
    {
        return view("settings.app.global");
    }

    public function notifications()
    {
        return view("settings.app.notification");
    }

    public function cod()
    {
        return view('settings.app.cod');
    }

    public function applePay()
    {
        return view('settings.app.applepay');
    }

    public function stripe()
    {
        return view('settings.app.stripe');
    }

    public function mobileGlobals()
    {
        return view('settings.mobile.globals');
    }

    public function razorpay()
    {
        return view('settings.app.razorpay');
    }

    public function paytm()
    {
        return view('settings.app.paytm');
    }

    public function payfast()
    {
        return view('settings.app.payfast');
    }

    public function paypal()
    {
        return view('settings.app.paypal');
    }

    public function orangepay()
    {
        return view('settings.app.orangepay');
    }

    public function xendit()
    {
        return view('settings.app.xendit');
    }

    public function midtrans()
    {
        return view('settings.app.midtrans');
    }

    public function adminCommission()
    {
        return view("settings.app.adminCommission");
    }

    public function radiosConfiguration()
    {
        return view("settings.app.radiosConfiguration");
    }

    public function wallet()
    {
        return view('settings.app.wallet');
    }

    public function bookTable()
    {
        return view('settings.app.bookTable');
    }


    public function paystack()
    {
        return view('settings.app.paystack');
    }

    public function flutterwave()
    {
        return view('settings.app.flutterwave');
    }

    public function mercadopago()
    {
        return view('settings.app.mercadopago');
    }

    public function deliveryCharge()
    {
        return view("settings.app.deliveryCharge");
    }
    public function martSettings()
    {
        return view("settings.app.martSettings");
    }
    public function appSettings()
    {
        return view("settings.app.appSettings");
    }
    public function priceSetting()
    {
        return view("settings.app.priceSettings");
    }
    public function languages()
    {
        return view('settings.languages.index');
    }

    public function languagesedit($id)
    {
        return view('settings.languages.edit')->with('id', $id);
    }

    public function languagescreate()
    {
        return view('settings.languages.create');
    }

    public function specialOffer()
    {
        return view('settings.app.specialDiscountOffer');
    }

    public function story()
    {
        return view('settings.app.story');

    }

    public function footerTemplate()
    {
        return view('footerTemplate.index');
    }

    public function homepageTemplate()
    {
        return view('homepage_Template.index');
    }

    public function emailTemplatesIndex()
    {
        return view('email_templates.index');
    }

    public function emailTemplatesSave($id = '')
    {

        return view('email_templates.save')->with('id', $id);
    }
    public function documentVerification()
    {
        return view('settings.app.documentVerificationSetting');
    }

    public function surgeRules()
    {
      return view('settings.app.surgeRules');
    }

    // Email templates SQL endpoints
    public function emailTemplatesData()
    {
        $start = (int) request('start', 0);
        $length = (int) request('length', 10);
        $draw = (int) request('draw', 1);
        $search = strtolower((string) data_get(request('search'), 'value', ''));

        $q = DB::table('email_templates');
        if ($search !== '') {
            $q->where(function($qq) use ($search){
                $qq->where('type','like','%'.$search.'%')
                   ->orWhere('subject','like','%'.$search.'%');
            });
        }
        $total = (clone $q)->count();
        $rows = $q->orderBy('type','asc')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $editUrl = route('email-templates.save', $r->id);
            $typeLabel = $r->type; // mapping done in view if needed
            $actions = '<span class="action-btn"><a href="'.$editUrl.'"><i class="mdi mdi-lead-pencil" title="Edit"></i></a> '
                    .'<a href="javascript:void(0)" class="delete-template" data-id="'.$r->id.'"><i class="mdi mdi-delete" title="Delete"></i></a></span>';
            $data[] = [ $typeLabel, e($r->subject ?: ''), $actions ];
        }
        return response()->json(['draw'=>$draw,'recordsTotal'=>$total,'recordsFiltered'=>$total,'data'=>$data]);
    }

    public function emailTemplatesJson($id)
    {
        $rec = DB::table('email_templates')->where('id',$id)->first();
        if(!$rec) return response()->json(['error'=>'Not found'],404);
        return response()->json($rec);
    }

    public function emailTemplatesUpdate($id)
    {
        request()->validate([
            'subject'=>'required|string',
            'message'=>'required|string',
            'isSendToAdmin'=>'nullable'
        ]);
        $updated = DB::table('email_templates')->where('id',$id)->update([
            'subject'=>request('subject'),
            'message'=>request('message'),
            'isSendToAdmin'=>request()->boolean('isSendToAdmin') ? 1 : 0,
        ]);
        if ($updated === false) return response()->json(['success'=>false],500);
        return response()->json(['success'=>true]);
    }

    public function emailTemplatesDelete($id)
    {
        $exists = DB::table('email_templates')->where('id',$id)->exists();
        if(!$exists) return response()->json(['success'=>false],404);
        DB::table('email_templates')->where('id',$id)->delete();
        return response()->json(['success'=>true]);
    }

    /**
     * Admin Commission: SQL-backed settings stored in `settings` table as JSON in `fields`.
     */
    public function getAdminCommissionSettings()
    {
        $admin = DB::table('settings')->where('document_name', 'AdminCommission')->first();
        $restaurant = DB::table('settings')->where('document_name', 'restaurant')->first();

        $adminFields = $admin && $admin->fields ? json_decode($admin->fields, true) : [];
        $restaurantFields = $restaurant && $restaurant->fields ? json_decode($restaurant->fields, true) : [];

        return response()->json([
            'adminCommission' => [
                'isEnabled' => (bool) ($adminFields['isEnabled'] ?? false),
                'fix_commission' => (string) ($adminFields['fix_commission'] ?? ''),
                'commissionType' => (string) ($adminFields['commissionType'] ?? 'Percent'),
            ],
            'restaurant' => [
                'subscription_model' => (bool) ($restaurantFields['subscription_model'] ?? false),
            ],
        ]);
    }

    public function updateAdminCommission(Request $request)
    {
        $payload = $request->validate([
            'isEnabled' => 'nullable|boolean',
            'fix_commission' => 'nullable|string',
            'commissionType' => 'nullable|string|in:Percent,Fixed',
        ]);

        $fields = [
            'isEnabled' => (bool) ($payload['isEnabled'] ?? false),
            'fix_commission' => (string) ($payload['fix_commission'] ?? '0'),
            'commissionType' => (string) ($payload['commissionType'] ?? 'Percent'),
        ];

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'AdminCommission'],
            ['fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['success' => true]);
    }

    public function updateSubscriptionModel(Request $request)
    {
        $payload = $request->validate([
            'subscription_model' => 'required|boolean',
        ]);

        $restaurant = DB::table('settings')->where('document_name', 'restaurant')->first();
        $existing = $restaurant && $restaurant->fields ? json_decode($restaurant->fields, true) : [];
        $existing['subscription_model'] = (bool) $payload['subscription_model'];

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'restaurant'],
            ['fields' => json_encode($existing, JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['success' => true]);
    }

    public function getVendorsForCommission(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $query = Vendor::query()->select(['id','title'])->orderBy('title','asc');
        if ($search !== '') {
            $query->where('title','like','%'.$search.'%');
        }
        $vendors = $query->limit(100)->get()->map(function($v){
            return ['id' => $v->id, 'text' => $v->title];
        });
        return response()->json(['results' => $vendors]);
    }

    public function bulkUpdateVendorCommission(Request $request)
    {
        $payload = $request->validate([
            'scope' => 'required|in:all,custom',
            'vendor_ids' => 'array',
            'vendor_ids.*' => 'string',
            'commissionType' => 'required|in:Percent,Fixed',
            'fix_commission' => 'required|string',
        ]);

        $adminCommission = [
            'commissionType' => $payload['commissionType'],
            'fix_commission' => $payload['fix_commission'],
            'isEnabled' => true,
        ];

        $q = Vendor::query();
        if ($payload['scope'] === 'custom') {
            $ids = $payload['vendor_ids'] ?? [];
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No vendors selected'], 422);
            }
            $q->whereIn('id', $ids);
        }

        $updated = $q->update(['adminCommission' => json_encode($adminCommission, JSON_UNESCAPED_UNICODE)]);
        return response()->json(['success' => true, 'updated' => $updated]);
    }

    /**
     * Radius Configuration: SQL-backed via `settings` table.
     */
    public function getRadiusSettings()
    {
        $restaurant = DB::table('settings')->where('document_name', 'RestaurantNearBy')->first();
        $driver = DB::table('settings')->where('document_name', 'DriverNearBy')->first();

        $restaurantFields = $restaurant && $restaurant->fields ? json_decode($restaurant->fields, true) : [];
        $driverFields = $driver && $driver->fields ? json_decode($driver->fields, true) : [];

        return response()->json([
            'distanceType' => (string) ($restaurantFields['distanceType'] ?? 'km'),
            'restaurantNearBy' => (string) ($restaurantFields['radios'] ?? ''),
            'driverNearBy' => (string) ($driverFields['driverRadios'] ?? ''),
            'driverOrderAcceptRejectDuration' => (int) ($driverFields['driverOrderAcceptRejectDuration'] ?? 0),
        ]);
    }

    public function updateRadiusSettings(Request $request)
    {
        $payload = $request->validate([
            'restaurantNearBy' => 'required|numeric|min:0',
            'driverNearBy' => 'required|numeric|min:0',
            'driverOrderAcceptRejectDuration' => 'required|integer|min:0',
            'distanceType' => 'required|string|in:km,miles',
        ]);

        $restaurantFields = [
            'radios' => (string) $payload['restaurantNearBy'],
            'distanceType' => (string) $payload['distanceType'],
        ];

        $driverFields = [
            'driverRadios' => (string) $payload['driverNearBy'],
            'driverOrderAcceptRejectDuration' => (int) $payload['driverOrderAcceptRejectDuration'],
        ];

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'RestaurantNearBy'],
            ['fields' => json_encode($restaurantFields, JSON_UNESCAPED_UNICODE)]
        );

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'DriverNearBy'],
            ['fields' => json_encode($driverFields, JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Dine-in Future Settings: SQL-backed via `settings` table.
     */
    public function getDineInSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'DineinForRestaurant')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json([
            'isEnabled' => (bool) ($fields['isEnabled'] ?? false),
            'isEnabledForCustomer' => (bool) ($fields['isEnabledForCustomer'] ?? false),
        ]);
    }

    public function updateDineInSettings(Request $request)
    {
        $payload = $request->validate([
            'isEnabled' => 'nullable|boolean',
            'isEnabledForCustomer' => 'nullable|boolean',
        ]);

        $fields = [
            'isEnabled' => (bool) ($payload['isEnabled'] ?? false),
            'isEnabledForCustomer' => (bool) ($payload['isEnabledForCustomer'] ?? false),
        ];

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'DineinForRestaurant'],
            ['fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Delivery Charge Settings: SQL-backed via `settings` table.
     */
    public function getDeliveryChargeSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'DeliveryCharge')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];

        return response()->json([
            'vendor_can_modify' => (bool) ($fields['vendor_can_modify'] ?? false),
            'delivery_charges_per_km' => $fields['delivery_charges_per_km'] ?? null,
            'minimum_delivery_charges' => $fields['minimum_delivery_charges'] ?? null,
            'minimum_delivery_charges_within_km' => $fields['minimum_delivery_charges_within_km'] ?? null,
            'base_delivery_charge' => $fields['base_delivery_charge'] ?? 23,
            'free_delivery_distance_km' => $fields['free_delivery_distance_km'] ?? 5,
            'per_km_charge_above_free_distance' => $fields['per_km_charge_above_free_distance'] ?? 7,
            'item_total_threshold' => $fields['item_total_threshold'] ?? 199,
        ]);
    }

    public function updateDeliveryChargeSettings(Request $request)
    {
        // We allow optional numeric fields; merge with existing values to preserve unspecified ones
        $payload = $request->all();

        $rec = DB::table('settings')->where('document_name', 'DeliveryCharge')->first();
        $existing = $rec && $rec->fields ? json_decode($rec->fields, true) : [];

        $merged = $existing;
        $merged['vendor_can_modify'] = $request->boolean('vendor_can_modify');

        $numericKeys = [
            'delivery_charges_per_km',
            'minimum_delivery_charges',
            'minimum_delivery_charges_within_km',
            'base_delivery_charge',
            'free_delivery_distance_km',
            'per_km_charge_above_free_distance',
            'item_total_threshold',
        ];

        foreach ($numericKeys as $key) {
            if (array_key_exists($key, $payload) && $payload[$key] !== '' && $payload[$key] !== null) {
                $merged[$key] = (int) $payload[$key];
            }
        }

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'DeliveryCharge'],
            ['fields' => json_encode($merged, JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['success' => true]);
    }

    /**
     * App Settings: SQL-backed via `app_settings` table.
     */
    public function getAppSettingsData()
    {
        $row = DB::table('app_settings')->where('id', 'version_info')->first();
        if (!$row) {
            return response()->json([
                'force_update' => true,
                'latest_version' => '2.3.4',
                'min_required_version' => '1.0.0',
                'update_message' => 'Please Update',
                'update_url' => null,
                'android_version' => null,
                'android_build' => null,
                'android_update_url' => null,
                'ios_version' => null,
                'ios_build' => null,
                'ios_update_url' => null,
                'last_updated' => null,
            ]);
        }
        return response()->json([
            'force_update' => (bool) ($row->force_update ?? false),
            'latest_version' => $row->latest_version,
            'min_required_version' => $row->min_required_version,
            'update_message' => $row->update_message,
            'update_url' => $row->update_url,
            'android_version' => $row->android_version,
            'android_build' => $row->android_build,
            'android_update_url' => $row->android_update_url,
            'ios_version' => $row->ios_version,
            'ios_build' => $row->ios_build,
            'ios_update_url' => $row->ios_update_url,
            'last_updated' => $row->last_updated,
        ]);
    }

    public function updateAppSettingsData(Request $request)
    {
        $payload = $request->validate([
            'force_update' => 'nullable|boolean',
            'latest_version' => 'required|string',
            'min_required_version' => 'required|string',
            'update_message' => 'required|string',
            'update_url' => 'required|string',
            'android_version' => 'required|string',
            'android_build' => 'required|string',
            'android_update_url' => 'required|string',
            'ios_version' => 'required|string',
            'ios_build' => 'required|string',
            'ios_update_url' => 'required|string',
        ]);

        $data = [
            'force_update' => $request->boolean('force_update'),
            'latest_version' => $payload['latest_version'],
            'min_required_version' => $payload['min_required_version'],
            'update_message' => $payload['update_message'],
            'update_url' => $payload['update_url'],
            'android_version' => $payload['android_version'],
            'android_build' => $payload['android_build'],
            'android_update_url' => $payload['android_update_url'],
            'ios_version' => $payload['ios_version'],
            'ios_build' => $payload['ios_build'],
            'ios_update_url' => $payload['ios_update_url'],
            'last_updated' => now()->format('Y-m-d H:i:s'),
        ];

        DB::table('app_settings')->updateOrInsert(['id' => 'version_info'], $data);
        return response()->json(['success' => true]);
    }

    /**
     * Mart Settings: SQL-backed via `mart_settings` table.
     */
    public function getMartSettingsData()
    {
        $row = DB::table('mart_settings')->where('id', 'delivery_settings')->first();
        if (!$row) {
            return response()->json([
                'is_active' => true,
                'free_delivery_distance_km' => 3,
                'free_delivery_threshold' => 199,
                'per_km_charge_above_free_distance' => 7,
                'min_order_value' => 99,
                'min_order_message' => 'Min Item value is ₹99',
                'delivery_promotion_text' => 'Daily',
            ]);
        }
        return response()->json([
            'is_active' => (bool) ($row->is_active ?? false),
            'free_delivery_distance_km' => (int) ($row->free_delivery_distance_km ?? 0),
            'free_delivery_threshold' => (int) ($row->free_delivery_threshold ?? 0),
            'per_km_charge_above_free_distance' => (int) ($row->per_km_charge_above_free_distance ?? 0),
            'min_order_value' => (int) ($row->min_order_value ?? 0),
            'min_order_message' => $row->min_order_message,
            'delivery_promotion_text' => $row->delivery_promotion_text,
        ]);
    }

    public function updateMartSettingsData(Request $request)
    {
        $payload = $request->validate([
            'is_active' => 'nullable|boolean',
            'free_delivery_distance_km' => 'required|integer|min:0',
            'free_delivery_threshold' => 'required|integer|min:0',
            'per_km_charge_above_free_distance' => 'required|integer|min:0',
            'min_order_value' => 'required|integer|min:0',
            'min_order_message' => 'required|string',
            'delivery_promotion_text' => 'required|string',
        ]);

        $data = [
            'is_active' => $request->boolean('is_active'),
            'free_delivery_distance_km' => (int) $payload['free_delivery_distance_km'],
            'free_delivery_threshold' => (int) $payload['free_delivery_threshold'],
            'per_km_charge_above_free_distance' => (int) $payload['per_km_charge_above_free_distance'],
            'min_order_value' => (int) $payload['min_order_value'],
            'min_order_message' => $payload['min_order_message'],
            'delivery_promotion_text' => $payload['delivery_promotion_text'],
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];

        DB::table('mart_settings')->updateOrInsert(['id' => 'delivery_settings'], $data);
        return response()->json(['success' => true]);
    }

    /**
     * Surge Rules: SQL-backed via `surge_rules` table for main fields, plus 'settings' JSON for admin_surge_fee.
     */
    public function getSurgeRulesData()
    {
        $row = DB::table('surge_rules')->where('firestore_id', 'surge_settings')->first();
        $extra = DB::table('settings')->where('document_name', 'surge_rules_config')->first();
        $extraFields = $extra && $extra->fields ? json_decode($extra->fields, true) : [];

        return response()->json([
            'bad_weather' => (int) ($row->bad_weather ?? 0),
            'rain' => (int) ($row->rain ?? 0),
            'summer' => (int) ($row->summer ?? 0),
            'admin_surge_fee' => (int) ($extraFields['admin_surge_fee'] ?? 0),
        ]);
    }

    public function updateSurgeRulesData(Request $request)
    {
        $payload = $request->validate([
            'bad_weather' => 'required|integer|min:0',
            'rain' => 'required|integer|min:0',
            'summer' => 'required|integer|min:0',
            'admin_surge_fee' => 'required|integer|min:0',
        ]);

        DB::table('surge_rules')->updateOrInsert(
            ['firestore_id' => 'surge_settings'],
            [
                'bad_weather' => (string) $payload['bad_weather'],
                'rain' => (string) $payload['rain'],
                'summer' => (string) $payload['summer'],
                'updated_at' => now(),
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'surge_rules_config'],
            ['fields' => json_encode(['admin_surge_fee' => (int) $payload['admin_surge_fee']], JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Document Verification Settings: SQL-backed via `settings` table.
     */
    public function getDocumentVerificationSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'document_verification_settings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json([
            'isDriverVerification' => (bool) ($fields['isDriverVerification'] ?? false),
            'isRestaurantVerification' => (bool) ($fields['isRestaurantVerification'] ?? false),
        ]);
    }

    public function updateDocumentVerificationSettings(Request $request)
    {
        $payload = $request->validate([
            'isDriverVerification' => 'nullable|boolean',
            'isRestaurantVerification' => 'nullable|boolean',
        ]);

        $fields = [
            'isDriverVerification' => (bool) ($payload['isDriverVerification'] ?? false),
            'isRestaurantVerification' => (bool) ($payload['isRestaurantVerification'] ?? false),
        ];

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'document_verification_settings'],
            ['fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['success' => true]);
    }

    /**
     * COD Settings
     */
    public function getCODSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'CODSettings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json([
            'isEnabled' => (bool) ($fields['isEnabled'] ?? false),
        ]);
    }

    public function updateCODSettings(Request $request)
    {
        $fields = ['isEnabled' => $request->boolean('isEnabled')];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'CODSettings'],
            ['document_name' => 'CODSettings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Stripe Settings
     */
    public function getStripeSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'stripeSettings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnabled' => false,
            'isSandboxEnabled' => false,
            'isWithdrawEnabled' => false,
            'stripeSecret' => '',
            'stripeKey' => '',
            'clientpublishableKey' => ''
        ]);
    }

    public function updateStripeSettings(Request $request)
    {
        $fields = [
            'isEnabled' => $request->boolean('isEnabled'),
            'isSandboxEnabled' => $request->boolean('isSandboxEnabled'),
            'isWithdrawEnabled' => $request->boolean('isWithdrawEnabled'),
            'stripeSecret' => $request->input('stripeSecret', ''),
            'stripeKey' => $request->input('stripeKey', ''),
            'clientpublishableKey' => $request->input('clientpublishableKey', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'stripeSettings'],
            ['document_name' => 'stripeSettings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Razorpay Settings
     */
    public function getRazorpaySettings()
    {
        $rec = DB::table('settings')->where('document_name', 'razorpaySettings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnabled' => false,
            'isSandboxEnabled' => false,
            'isWithdrawEnabled' => false,
            'razorpayKey' => '',
            'razorpaySecret' => ''
        ]);
    }

    public function updateRazorpaySettings(Request $request)
    {
        $fields = [
            'isEnabled' => $request->boolean('isEnabled'),
            'isSandboxEnabled' => $request->boolean('isSandboxEnabled'),
            'isWithdrawEnabled' => $request->boolean('isWithdrawEnabled'),
            'razorpayKey' => $request->input('razorpayKey', ''),
            'razorpaySecret' => $request->input('razorpaySecret', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'razorpaySettings'],
            ['document_name' => 'razorpaySettings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * PayPal Settings
     */
    public function getPayPalSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'paypalSettings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnabled' => false,
            'isLive' => false,
            'isWithdrawEnabled' => false,
            'paypalClient' => '',
            'paypalSecret' => '',
            'paypalAppId' => ''
        ]);
    }

    public function updatePayPalSettings(Request $request)
    {
        $fields = [
            'isEnabled' => $request->boolean('isEnabled'),
            'isLive' => $request->boolean('isLive'),
            'isWithdrawEnabled' => $request->boolean('isWithdrawEnabled'),
            'paypalClient' => $request->input('paypalClient', ''),
            'paypalSecret' => $request->input('paypalSecret', ''),
            'paypalAppId' => $request->input('paypalAppId', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'paypalSettings'],
            ['document_name' => 'paypalSettings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * PayStack Settings
     */
    public function getPayStackSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'payStack')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnable' => false,
            'isSandbox' => false,
            'publicKey' => '',
            'secretKey' => '',
            'callbackURL' => '',
            'webhookURL' => ''
        ]);
    }

    public function updatePayStackSettings(Request $request)
    {
        $fields = [
            'isEnable' => $request->boolean('isEnable'),
            'isSandbox' => $request->boolean('isSandbox'),
            'publicKey' => $request->input('publicKey', ''),
            'secretKey' => $request->input('secretKey', ''),
            'callbackURL' => $request->input('callbackURL', ''),
            'webhookURL' => $request->input('webhookURL', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'payStack'],
            ['document_name' => 'payStack', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * FlutterWave Settings
     */
    public function getFlutterWaveSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'flutterWave')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnable' => false,
            'isSandbox' => false,
            'isWithdrawEnabled' => false,
            'publicKey' => '',
            'secretKey' => '',
            'encryptionKey' => ''
        ]);
    }

    public function updateFlutterWaveSettings(Request $request)
    {
        $fields = [
            'isEnable' => $request->boolean('isEnable'),
            'isSandbox' => $request->boolean('isSandbox'),
            'isWithdrawEnabled' => $request->boolean('isWithdrawEnabled'),
            'publicKey' => $request->input('publicKey', ''),
            'secretKey' => $request->input('secretKey', ''),
            'encryptionKey' => $request->input('encryptionKey', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'flutterWave'],
            ['document_name' => 'flutterWave', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * PayFast Settings
     */
    public function getPayFastSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'payFastSettings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnable' => false,
            'isSandbox' => false,
            'merchant_id' => '',
            'merchant_key' => '',
            'return_url' => '',
            'cancel_url' => '',
            'notify_url' => ''
        ]);
    }

    public function updatePayFastSettings(Request $request)
    {
        $fields = [
            'isEnable' => $request->boolean('isEnable'),
            'isSandbox' => $request->boolean('isSandbox'),
            'merchant_id' => $request->input('merchant_id', ''),
            'merchant_key' => $request->input('merchant_key', ''),
            'return_url' => $request->input('return_url', ''),
            'cancel_url' => $request->input('cancel_url', ''),
            'notify_url' => $request->input('notify_url', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'payFastSettings'],
            ['document_name' => 'payFastSettings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Paytm Settings
     */
    public function getPaytmSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'PaytmSettings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnabled' => false,
            'isSandboxEnabled' => false,
            'PaytmMID' => '',
            'PAYTM_MERCHANT_KEY' => ''
        ]);
    }

    public function updatePaytmSettings(Request $request)
    {
        $fields = [
            'isEnabled' => $request->boolean('isEnabled'),
            'isSandboxEnabled' => $request->boolean('isSandboxEnabled'),
            'PaytmMID' => $request->input('PaytmMID', ''),
            'PAYTM_MERCHANT_KEY' => $request->input('PAYTM_MERCHANT_KEY', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'PaytmSettings'],
            ['document_name' => 'PaytmSettings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Mercado Pago Settings
     */
    public function getMercadoPagoSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'MercadoPago')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnabled' => false,
            'isSandboxEnabled' => false,
            'PublicKey' => '',
            'AccessToken' => ''
        ]);
    }

    public function updateMercadoPagoSettings(Request $request)
    {
        $fields = [
            'isEnabled' => $request->boolean('isEnabled'),
            'isSandboxEnabled' => $request->boolean('isSandboxEnabled'),
            'PublicKey' => $request->input('PublicKey', ''),
            'AccessToken' => $request->input('AccessToken', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'MercadoPago'],
            ['document_name' => 'MercadoPago', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Wallet Settings
     */
    public function getWalletSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'WalletSetting')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json([
            'isEnabled' => (bool) ($fields['isEnabled'] ?? false)
        ]);
    }

    public function updateWalletSettings(Request $request)
    {
        $fields = ['isEnabled' => $request->boolean('isEnabled')];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'WalletSetting'],
            ['document_name' => 'WalletSetting', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Global Settings
     */
    public function getGlobalSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'globalSettings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: []);
    }

    public function updateGlobalSettings(Request $request)
    {
        $rec = DB::table('settings')->where('document_name', 'globalSettings')->first();
        $existing = $rec && $rec->fields ? json_decode($rec->fields, true) : [];

        // Merge all input with existing
        $fields = array_merge($existing, $request->except(['_token']));

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'globalSettings'],
            ['document_name' => 'globalSettings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Notification Settings
     */
    public function getNotificationSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'notification_setting')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'senderId' => '',
            'projectId' => '',
            'serviceJson' => ''
        ]);
    }

    public function updateNotificationSettings(Request $request)
    {
        $fields = [
            'senderId' => $request->input('senderId', ''),
            'projectId' => $request->input('projectId', ''),
            'serviceJson' => $request->input('serviceJson', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'notification_setting'],
            ['document_name' => 'notification_setting', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Xendit Settings
     */
    public function getXenditSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'xendit_settings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'enable' => false,
            'isSandbox' => false,
            'apiKey' => '',
            'name' => 'Xendit',
            'image' => ''
        ]);
    }

    public function updateXenditSettings(Request $request)
    {
        $fields = [
            'enable' => $request->boolean('enable'),
            'isSandbox' => $request->boolean('isSandbox'),
            'apiKey' => $request->input('apiKey', ''),
            'name' => $request->input('name', 'Xendit'),
            'image' => $request->input('image', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'xendit_settings'],
            ['document_name' => 'xendit_settings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Midtrans Settings
     */
    public function getMidtransSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'midtrans_settings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'enable' => false,
            'isSandbox' => false,
            'serverKey' => '',
            'name' => 'MidTrans',
            'image' => ''
        ]);
    }

    public function updateMidtransSettings(Request $request)
    {
        $fields = [
            'enable' => $request->boolean('enable'),
            'isSandbox' => $request->boolean('isSandbox'),
            'serverKey' => $request->input('serverKey', ''),
            'name' => $request->input('name', 'MidTrans'),
            'image' => $request->input('image', '')
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'midtrans_settings'],
            ['document_name' => 'midtrans_settings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Orange Pay Settings
     */
    public function getOrangePaySettings()
    {
        $rec = DB::table('settings')->where('document_name', 'orange_money_settings')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'enable' => false,
            'isSandbox' => false,
            'merchantKey' => '',
            'auth' => '',
            'clientId' => '',
            'clientSecret' => '',
            'returnUrl' => '',
            'cancelUrl' => '',
            'notifyUrl' => ''
        ]);
    }

    public function updateOrangePaySettings(Request $request)
    {
        $rec = DB::table('settings')->where('document_name', 'orange_money_settings')->first();
        $existing = $rec && $rec->fields ? json_decode($rec->fields, true) : [];

        $fields = array_merge($existing, [
            'enable' => $request->boolean('enable'),
            'isSandbox' => $request->boolean('isSandbox'),
            'merchantKey' => $request->input('merchantKey', ''),
            'auth' => $request->input('auth', ''),
            'clientId' => $request->input('clientId', ''),
            'clientSecret' => $request->input('clientSecret', ''),
            'returnUrl' => $request->input('returnUrl', ''),
            'cancelUrl' => $request->input('cancelUrl', ''),
            'notifyUrl' => $request->input('notifyUrl', '')
        ]);

        DB::table('settings')->updateOrInsert(
            ['document_name' => 'orange_money_settings'],
            ['document_name' => 'orange_money_settings', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Story Settings
     */
    public function getStorySettings()
    {
        $rec = DB::table('settings')->where('document_name', 'story')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json($fields ?: [
            'isEnabled' => false,
            'videoDuration' => 30
        ]);
    }

    public function updateStorySettings(Request $request)
    {
        $fields = [
            'isEnabled' => $request->boolean('isEnabled'),
            'videoDuration' => (int) $request->input('videoDuration', 30)
        ];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'story'],
            ['document_name' => 'story', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Special Discount Offer Settings
     */
    public function getSpecialOfferSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'specialDiscountOffer')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json([
            'isEnable' => (bool) ($fields['isEnable'] ?? false)
        ]);
    }

    public function updateSpecialOfferSettings(Request $request)
    {
        $fields = ['isEnable' => $request->boolean('isEnable')];
        DB::table('settings')->updateOrInsert(
            ['document_name' => 'specialDiscountOffer'],
            ['document_name' => 'specialDiscountOffer', 'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE)]
        );
        return response()->json(['success' => true]);
    }

    /**
     * Upload image (base64)
     */
    public function uploadImage(Request $request)
    {
        try {
            $image = $request->input('image');
            $filename = $request->input('filename', 'image_' . time() . '.jpg');

            // Remove base64 prefix if exists
            if (strpos($image, 'data:image') === 0) {
                $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            }

            $imageData = base64_decode($image);
            $path = 'images/' . $filename;
            \Storage::disk('public')->put($path, $imageData);

            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path
            ]);
        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload audio (base64)
     */
    public function uploadAudio(Request $request)
    {
        try {
            $audio = $request->input('audio');
            $filename = $request->input('filename', 'audio_' . time() . '.mp3');

            // Remove base64 prefix if exists
            if (strpos($audio, 'data:audio') === 0) {
                $audio = preg_replace('/^data:audio\/\w+;base64,/', '', $audio);
            }

            $audioData = base64_decode($audio);
            $path = 'audio/' . $filename;
            \Storage::disk('public')->put($path, $audioData);

            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path
            ]);
        } catch (\Exception $e) {
            \Log::error('Audio upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Audio upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload JSON file
     */
    public function uploadJson(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:json|max:2048'
            ]);

            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $timestamp = time();
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $timestamp . '.json';

            $path = $file->storeAs('json', $filename, 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path
            ]);
        } catch (\Exception $e) {
            \Log::error('JSON upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'JSON upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Languages Settings
     */
    public function getLanguagesSettings()
    {
        $rec = DB::table('settings')->where('document_name', 'languages')->first();
        $fields = $rec && $rec->fields ? json_decode($rec->fields, true) : [];
        return response()->json([
            'success' => true,
            'list' => $fields['list'] ?? []
        ]);
    }

    /**
     * Update Languages Settings
     */
    public function updateLanguagesSettings(Request $request)
    {
        try {
            $languages = $request->input('list', []);
            \Log::info('Updating languages:', ['count' => count($languages), 'data' => $languages]);

            $fields = ['list' => $languages];
            $jsonFields = json_encode($fields, JSON_UNESCAPED_UNICODE);

            \Log::info('JSON to save:', ['json' => $jsonFields]);

            DB::table('settings')->updateOrInsert(
                ['document_name' => 'languages'],
                [
                    'document_name' => 'languages',
                    'fields' => $jsonFields
                ]
            );

            // Verify the update
            $updated = DB::table('settings')->where('document_name', 'languages')->first();
            \Log::info('After update:', ['fields' => $updated->fields]);

            return response()->json([
                'success' => true,
                'message' => 'Languages updated successfully',
                'count' => count($languages)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating languages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating languages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a setting from the database
     */
    public function getSetting($documentName)
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', $documentName)
                ->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                    'data' => null
                ]);
            }

            $data = json_decode($setting->fields, true);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a setting in the database
     */
    public function updateSetting(Request $request, $documentName)
    {
        try {
            // Get existing setting
            $setting = DB::table('settings')
                ->where('document_name', $documentName)
                ->first();

            $currentData = $setting ? json_decode($setting->fields, true) : [];

            // Merge new data with existing data
            $newData = array_merge($currentData, $request->except('_token'));

            // Update or create
            DB::table('settings')->updateOrInsert(
                ['document_name' => $documentName],
                [
                    'document_name' => $documentName,
                    'fields' => json_encode($newData)
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'data' => $newData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating setting: ' . $e->getMessage()
            ], 500);
        }
    }
}
