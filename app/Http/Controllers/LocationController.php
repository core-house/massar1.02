<?php

namespace App\Http\Controllers;

use App\Models\UserLocationTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LocationController extends Controller
{
    public function storeTracking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'session_id' => 'required|string',
            'type' => 'nullable|string|in:login,tracking,attendance',
            'address' => 'nullable|string|max:500',
            'place_id' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // استخدام الوقت الحالي بالـ timezone المحلي للتطبيق
            $currentTime = Carbon::now(config('app.timezone'));
            
            $tracking = UserLocationTracking::create([
                'user_id' => Auth::id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'session_id' => $request->session_id,
                'tracked_at' => $currentTime,
                'type' => $request->type ?? 'tracking',
                'address' => $request->address,
                'place_id' => $request->place_id,
                'additional_data' => $request->additional_data ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location tracked successfully',
                'data' => [
                    'id' => $tracking->id,
                    'user_id' => $tracking->user_id,
                    'latitude' => $tracking->latitude,
                    'longitude' => $tracking->longitude,
                    'accuracy' => $tracking->accuracy,
                    'session_id' => $tracking->session_id,
                    'tracked_at' => $tracking->formatted_tracked_at,
                    'type' => $tracking->type,
                    'address' => $tracking->address,
                    'place_id' => $tracking->place_id,
                    'created_at' => $tracking->created_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                    'updated_at' => $tracking->updated_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        $query = UserLocationTracking::where('user_id', Auth::id());

        if ($request->has('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('tracked_at', [$request->from, $request->to]);
        }

        $history = $query->orderBy('tracked_at', 'desc')
            ->paginate($request->get('per_page', 50));

        // تحويل التواريخ إلى timezone التطبيق
        $formattedHistory = $history->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'accuracy' => $item->accuracy,
                'session_id' => $item->session_id,
                'tracked_at' => $item->formatted_tracked_at,
                'type' => $item->type,
                'address' => $item->address,
                'place_id' => $item->place_id,
                'created_at' => $item->created_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'data' => $formattedHistory,
            'pagination' => [
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
            ]
        ]);
    }
}