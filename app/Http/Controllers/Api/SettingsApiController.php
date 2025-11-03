<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all settings needed for the layout
     */
    public function getAllSettings()
    {
        try {
            $settings = [
                'globalSettings' => $this->getGlobalSettings(),
                'distanceSettings' => $this->getDistanceSettings(),
                'languages' => $this->getLanguages(),
                'version' => $this->getVersion(),
                'mapSettings' => $this->getMapSettings(),
                'notificationSettings' => $this->getNotificationSettings(),
                'currency' => $this->getCurrencySettings(),
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching settings'
            ], 500);
        }
    }

    /**
     * Get global settings
     */
    public function getGlobalSettings()
    {
        try {
            // Settings table structure: doc_id, document_name, fields
            $setting = DB::table('settings')
                ->where('document_name', 'globalSettings')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                return $data ?? [];
            }

            // Return defaults if not found
            return [
                'appLogo' => '',
                'meta_title' => 'Jippy Mart',
                'applicationName' => 'Jippy Mart',
                'web_panel_color' => '#FF683A',
                'order_ringtone_url' => ''
            ];

        } catch (\Exception $e) {
            Log::error('Error fetching global settings: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get distance settings
     */
    public function getDistanceSettings()
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', 'RestaurantNearBy')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                return $data ?? [];
            }

            return [
                'distanceType' => 'km',
                'radios' => '15'
            ];

        } catch (\Exception $e) {
            Log::error('Error fetching distance settings: ' . $e->getMessage());
            return ['distanceType' => 'km'];
        }
    }

    /**
     * Get languages
     */
    public function getLanguages()
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', 'languages')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                if (isset($data['list'])) {
                    return $data['list'];
                }
            }

            // Return default English if not found
            return [[
                'title' => 'English',
                'slug' => 'en',
                'isActive' => true,
                'is_rtl' => false
            ]];

        } catch (\Exception $e) {
            Log::error('Error fetching languages: ' . $e->getMessage());
            return [[
                'title' => 'English',
                'slug' => 'en',
                'isActive' => true
            ]];
        }
    }

    /**
     * Get version information
     */
    public function getVersion()
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', 'Version')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                return $data ?? [];
            }

            return [
                'web_version' => '2.5.0',
                'app_version' => '2.5.0'
            ];

        } catch (\Exception $e) {
            Log::error('Error fetching version: ' . $e->getMessage());
            return ['web_version' => '2.5.0'];
        }
    }

    /**
     * Get map settings
     */
    public function getMapSettings()
    {
        try {
            $driverNearBy = DB::table('settings')
                ->where('document_name', 'DriverNearBy')
                ->first();

            $googleMapKey = DB::table('settings')
                ->where('document_name', 'googleMapKey')
                ->first();

            $data = [];

            if ($driverNearBy && !empty($driverNearBy->fields)) {
                $driverData = json_decode($driverNearBy->fields, true);
                $data['selectedMapType'] = $driverData['selectedMapType'] ?? 'google';
            }

            if ($googleMapKey && !empty($googleMapKey->fields)) {
                $keyData = json_decode($googleMapKey->fields, true);
                $data['googleMapKey'] = $keyData['key'] ?? '';
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Error fetching map settings: ' . $e->getMessage());
            return ['selectedMapType' => 'google'];
        }
    }

    /**
     * Get notification settings
     */
    public function getNotificationSettings()
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', 'notification_setting')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                return $data ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Error fetching notification settings: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get currency settings
     */
    public function getCurrencySettings()
    {
        try {
            $currency = DB::table('currencies')
                ->where('isActive', 1)
                ->first();

            if ($currency) {
                return response()->json([
                    'symbol' => $currency->symbol ?? '₹',
                    'code' => $currency->code ?? 'INR',
                    'name' => $currency->name ?? 'Indian Rupee',
                    'symbolAtRight' => $currency->symbolAtRight ?? false,
                    'decimal_degits' => $currency->decimal_degits ?? 2,
                ]);
            }

            return response()->json([
                'symbol' => '₹',
                'code' => 'INR',
                'symbolAtRight' => false,
                'decimal_degits' => 2
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching currency settings: ' . $e->getMessage());
            return response()->json(['symbol' => '₹']);
        }
    }

    /**
     * Get restaurant nearby settings
     */
    public function getRestaurantSettings()
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', 'RestaurantNearBy')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                return response()->json($data ?? []);
            }

            return response()->json([
                'distanceType' => 'km',
                'radios' => '15',
                'driverRadios' => '50000'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching restaurant settings: ' . $e->getMessage());
            return response()->json(['distanceType' => 'km']);
        }
    }

    /**
     * Get admin commission settings
     */
    public function getAdminCommission()
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', 'AdminCommission')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                return response()->json($data ?? []);
            }

            return response()->json([
                'isEnabled' => false,
                'commissionType' => 'Percent',
                'fix_commission' => 0
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching admin commission: ' . $e->getMessage());
            return response()->json(['isEnabled' => false]);
        }
    }

    /**
     * Get driver nearby settings
     */
    public function getDriverSettings()
    {
        try {
            $setting = DB::table('settings')
                ->where('document_name', 'DriverNearBy')
                ->first();

            if ($setting && !empty($setting->fields)) {
                $data = json_decode($setting->fields, true);
                return response()->json($data ?? []);
            }

            return response()->json([
                'driverRadios' => '5',
                'mapType' => 'inappmap',
                'selectedMapType' => 'google'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching driver settings: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}

