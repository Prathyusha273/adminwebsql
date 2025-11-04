<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
class OTPController extends Controller
{
    // SMS API Configuration
    private $smsApiUrl = 'https://restapi.smscountry.com/v0.1/Accounts/g3NwQZX8qbjHARPZktFZ/SMSes/';
    private $authKey = 'Basic ZzNOd1FaWDhxYmpIQVJQWmt0Rlo6Y2lXdzBZRHUzbTFRY3hkMEFBSmZXaHNmczQ4TXRXdEs4Sk91TnR0Zg==';
    private $senderId = 'JIPPYM';


    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;

        // Check if there's a recent OTP request (rate limiting)
        $recentOtp = Otp::where('phone', $phone)
            ->where('created_at', '>', Carbon::now()->subMinutes(1))
            ->first();

        if ($recentOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait 1 minute before requesting another OTP'
            ], 429);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(10);

        // Save OTP to database
        Otp::updateOrCreate(
            ['phone' => $phone],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'verified' => false,
                'attempts' => 0
            ]
        );

        // Send SMS using multiple methods
        $smsSent = $this->sendSms($phone, $otp);

        if ($smsSent) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'expires_in' => 600 // 10 minutes in seconds
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $otp = $request->otp;

        // Find the OTP record
        $otpRecord = Otp::where('phone', $phone)
            ->where('otp', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->where('verified', false)
            ->first();

        if (!$otpRecord) {
            // Increment attempts for failed verification
            Otp::where('phone', $phone)
                ->where('verified', false)
                ->increment('attempts');

            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        // Check if too many attempts
        if ($otpRecord->attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many failed attempts. Please request a new OTP.'
            ], 429);
        }

        // Mark OTP as verified
        $otpRecord->markAsVerified();

        // Find or create user
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // Generate unique firebase_id for new user
            $firebaseId = 'user_' . Str::uuid();
            
            // Create new user
            $user = User::create([
                'firstName' => 'User',
                'lastName' => substr($phone, -4),
                'phoneNumber' => $phone,
                'phone' => $phone,
                'firebase_id' => $firebaseId,
                '_id' => $firebaseId,
                'email' => $phone . '@jippymart.in', // Temporary email
                'password' => bcrypt(Str::random(16)), // Random password
                'active' => 'true',
                'isActive' => true,
                'role' => 'customer',
                'wallet_amount' => 0,
                'orderCompleted' => 0,
                '_created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                '_updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'createdAt' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }

        // Generate API token
        $token = $user->createToken('otp-auth')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'user' => [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'phone' => $user->phone,
                'phoneNumber' => $user->phoneNumber,
                'email' => $user->email,
                'firebase_id' => $user->firebase_id,
                'wallet_amount' => $user->wallet_amount
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Send SMS using multiple HTTP client methods
     */
    private function sendSms($phone, $otp)
    {
        $smsText = "Your OTP for jippymart login is {$otp}. Please do not share this OTP with anyone. It is valid for the next 10 minutes-jippymart.in.";

        $payload = [
            "Text" => $smsText,
            "Number" => $phone,
            "SenderId" => $this->senderId,
            "DRNotifyUrl" => config('app.url') . "/api/sms-delivery-status",
            "DRNotifyHttpMethod" => "POST",
            "Tool" => "API"
        ];

        // Try multiple methods to send SMS (prioritize cURL since it's working)
        $methods = [
            'curl' => fn() => $this->sendSmsWithCurl($payload),
            'guzzle' => fn() => $this->sendSmsWithGuzzle($payload),
            'http_request2' => fn() => $this->sendSmsWithHttpRequest2($payload),
            'pecl_http' => fn() => $this->sendSmsWithPeclHttp($payload)
        ];

        foreach ($methods as $method => $callback) {
            try {
                $result = $callback();
                if ($result) {
                    Log::info("SMS sent successfully using {$method}", [
                        'phone' => $phone,
                        'method' => $method
                    ]);
                    return true;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send SMS using {$method}", [
                    'phone' => $phone,
                    'method' => $method,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return false;
    }

    /**
     * Send SMS using Guzzle HTTP Client
     */
    private function sendSmsWithGuzzle($payload)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $headers = [
                'Authorization' => $this->authKey,
                'Content-Type' => 'application/json'
            ];
            $body = json_encode($payload);

            $request = new \GuzzleHttp\Psr7\Request('POST', $this->smsApiUrl, $headers, $body);
            $res = $client->sendAsync($request)->wait();

            return $res->getStatusCode() >= 200 && $res->getStatusCode() < 300;
        } catch (\Exception $e) {
            Log::error('Guzzle SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS using cURL
     */
    private function sendSmsWithCurl($payload)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->smsApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->authKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $httpCode >= 200 && $httpCode < 300;
    }

    /**
     * Send SMS using HTTP_Request2 (if available)
     */
    private function sendSmsWithHttpRequest2($payload)
    {
        if (!class_exists('HTTP_Request2')) {
            return false;
        }

        try {
            $request = new \HTTP_Request2();
            $request->setUrl($this->smsApiUrl);
            $request->setMethod(\HTTP_Request2::METHOD_POST);
            $request->setConfig(array(
                'follow_redirects' => TRUE
            ));
            $request->setHeader(array(
                'Authorization' => $this->authKey,
                'Content-Type' => 'application/json'
            ));
            $request->setBody(json_encode($payload));

            $response = $request->send();
            return $response->getStatus() == 200;
        } catch (\HTTP_Request2_Exception $e) {
            Log::error('HTTP_Request2 SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS using PECL HTTP (if available)
     */
    private function sendSmsWithPeclHttp($payload)
    {
        if (!extension_loaded('http')) {
            return false;
        }

        try {
            $request = new \http\Client\Request('POST', $this->smsApiUrl);
            $request->setHeaders([
                'Authorization' => $this->authKey,
                'Content-Type' => 'application/json'
            ]);
            $request->getBody()->append(json_encode($payload));

            $client = new \http\Client();
            $client->enqueue($request)->send();
            $response = $client->getResponse($request);

            return $response->getResponseCode() >= 200 && $response->getResponseCode() < 300;
        } catch (\Exception $e) {
            Log::error('PECL HTTP SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * SMS Delivery Status Webhook
     */
    public function smsDeliveryStatus(Request $request)
    {
        Log::info('SMS Delivery Status', $request->all());

        return response()->json(['status' => 'received']);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete existing OTP for this phone
        Otp::where('phone', $request->phone)->delete();

        // Call sendOtp method
        return $this->sendOtp($request);
    }

    /**
     * Debug OTP - Check current OTP status for a phone number
     */
    public function debugOtp($phone)
    {
        $otps = Otp::where('phone', $phone)->get();
        
        return response()->json([
            'phone' => $phone,
            'total_records' => $otps->count(),
            'records' => $otps->map(function($otp) {
                return [
                    'id' => $otp->id,
                    'otp' => $otp->otp,
                    'expires_at' => $otp->expires_at,
                    'expires_at_formatted' => $otp->expires_at->format('Y-m-d H:i:s'),
                    'is_expired' => $otp->expires_at < Carbon::now(),
                    'verified' => $otp->verified,
                    'attempts' => $otp->attempts,
                    'created_at' => $otp->created_at,
                    'current_time' => Carbon::now()->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }
}
