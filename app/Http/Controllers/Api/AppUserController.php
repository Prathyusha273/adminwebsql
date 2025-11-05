<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AppUserController extends Controller
{
    /**
     * Generate a unique Firebase ID in format: user_XXX
     */
    private function generateFirebaseId()
    {
        // Get the highest existing user number
        $lastUser = AppUser::where('firebase_id', 'like', 'user_%')
            ->orderByRaw('CAST(SUBSTRING(firebase_id, 6) AS UNSIGNED) DESC')
            ->first();

        $nextNumber = 1;

        if ($lastUser && $lastUser->firebase_id) {
            // Extract number from firebase_id (e.g., "user_999" -> 999)
            preg_match('/user_(\d+)/', $lastUser->firebase_id, $matches);
            if (!empty($matches[1])) {
                $nextNumber = (int)$matches[1] + 1;
            }
        }

        // Generate new ID
        $firebaseId = 'user_' . $nextNumber;

        // Ensure uniqueness (in case of concurrent requests)
        while (AppUser::where('firebase_id', $firebaseId)->exists()) {
            $nextNumber++;
            $firebaseId = 'user_' . $nextNumber;
        }

        return $firebaseId;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'countryCode' => 'nullable|string|max:10',
            'phoneNumber' => 'nullable|string|max:30',
            'active' => 'nullable',
            'role' => 'nullable|string|max:50',
            'zoneId' => 'nullable|string|max:255',
            'photo' => 'nullable|string', // base64 data URL (optional)
            'fileName' => 'nullable|string',
        ]);

        $profileUrl = null;
        if (!empty($validated['photo'])) {
            $data = $validated['photo'];
            $data = preg_replace('#^data:image/\w+;base64,#i', '', $data);
            $binary = base64_decode($data, true);
            if ($binary !== false) {
                $name = $validated['fileName'] ?? ('user_' . time() . '.jpg');
                $path = 'users/' . $name;
                Storage::disk('public')->put($path, $binary);
                $profileUrl = asset('storage/' . $path);
            }
        }

        // Generate unique firebase_id
        $firebase_id = $this->generateFirebaseId();

        // Determine active status
        $isActive = false;
        if (isset($validated['active'])) {
            if (is_bool($validated['active'])) {
                $isActive = $validated['active'];
            } else {
                $isActive = ($validated['active'] === 'true' || $validated['active'] === true || $validated['active'] === 1);
            }
        }

        // Create new user
        $user = AppUser::create([
            'firebase_id' => $firebase_id,
            '_id' => $firebase_id, // Also set _id for compatibility
            'firstName' => $validated['firstName'],
            'lastName' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'countryCode' => $validated['countryCode'] ?? null,
            'phoneNumber' => $validated['phoneNumber'] ?? null,
            'profilePictureURL' => $profileUrl,
            'provider' => 'email',
            'role' => $validated['role'] ?? 'customer',
            'active' => $isActive ? 'true' : 'false',
            'isActive' => $isActive ? 1 : 0,
            'zoneId' => $validated['zoneId'] ?? null,
            'appIdentifier' => 'web',
            'createdAt' => now()->format('Y-m-d H:i:s'),
            'wallet_amount' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => [
                'id' => (string) $user->firebase_id,
                'firebase_id' => (string) $user->firebase_id,
                'email' => $user->email,
                'isActive' => $user->isActive
            ]
        ], 201);
    }
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);
        $status = $request->query('status'); // 'active' | 'inactive' | null
        $zoneId = $request->query('zoneId');
        $search = trim((string) $request->query('search', ''));

        $query = AppUser::query();

        // Only customers by default unless role is specified
        $role = $request->query('role', 'customer');
        if (!empty($role)) {
            $query->where('role', $role);
        }

        // Date range filter (expects Y-m-d or full datetime strings)
        $from = $request->query('from');
        $to = $request->query('to');
        if (!empty($from)) {
            $query->where('createdAt', '>=', $from);
        }
        if (!empty($to)) {
            $query->where('createdAt', '<=', $to);
        }

        // Status filter
        if ($status === 'active') {
            $query->where(function ($q) {
                $q->where('active', '1')->orWhere('active', 'true')->orWhere('isActive', 1);
            });
        } elseif ($status === 'inactive') {
            $query->where(function ($q) {
                $q->where('active', '0')->orWhere('active', 'false')->orWhereNull('active')->orWhere('isActive', 0);
            });
        }

        // Zone filter
        if (!empty($zoneId)) {
            $query->where('zoneId', $zoneId);
        }

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('firstName', 'like', "%$search%")
                  ->orWhere('lastName', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phoneNumber', 'like', "%$search%")
                  ->orWhere('createdAt', 'like', "%$search%");
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('id')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $items = $rows->map(function ($u) {
            $fullName = trim(($u->firstName ?? '') . ' ' . ($u->lastName ?? ''));
            return [
                'id' => (string) ($u->firebase_id ?: $u->id),
                'firstName' => $u->firstName,
                'lastName' => $u->lastName,
                'fullName' => $fullName,
                'email' => (string) ($u->email ?? ''),
                'phoneNumber' => (string) ($u->phoneNumber ?? ''),
                'zoneId' => (string) ($u->zoneId ?? ''),
                'createdAt' => (string) ($u->createdAt ?? ''),
                'active' => in_array((string) $u->active, ['1','true'], true) || (bool) ($u->isActive ?? 0),
                'profilePictureURL' => $u->profilePictureURL,
            ];
        })->all();

        return response()->json([
            'status' => true,
            'data' => $items,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'has_more' => ($page * $limit) < $total,
            ],
        ]);
    }

    public function destroy(string $id)
    {
        $user = AppUser::where('firebase_id', $id)->orWhere('id', $id)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['status' => true]);
    }

    public function setActive(Request $request, string $id)
    {
        $isActive = filter_var($request->input('active', false), FILTER_VALIDATE_BOOLEAN);
        $user = AppUser::where('firebase_id', $id)->orWhere('id', $id)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
        $user->active = $isActive ? 'true' : 'false';
        $user->isActive = $isActive ? 1 : 0;
        $user->save();
        return response()->json(['status' => true]);
    }
}


