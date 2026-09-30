<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * Create a new notification.
     *
     * Request body:
     * {
     *   "type": "patient_called",
     *   "title": "Patient Ready",
     *   "message": "Patient John Doe is ready for checkout",
     *   "reference_type": "visit",
     *   "reference_id": 123
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'title' => 'nullable|string',
            'message' => 'required|string',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        try {
            // Create notification for all reception staff
            $receptionUsers = \App\Models\User::where('role', 'reception')->get();
            
            foreach ($receptionUsers as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => $validated['type'],
                    'title' => $validated['title'] ?? 'New Notification',
                    'message' => $validated['message'],
                    'payload' => [
                        'reference_type' => $validated['reference_type'],
                        'reference_id' => $validated['reference_id'],
                    ],
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating notification: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     *
     * Query params:
     * - limit: max results (default 20)
     *
     * Response:
     * {
     *   "success": true,
     *   "notifications": [
     *     {
     *       "id": 1,
     *       "type": "next_patient_call",
     *       "message": "Patient John Doe completed consultation",
     *       "payload": {...},
     *       "is_read": false,
     *       "created_at": "2025-01-15 10:30:00"
     *     }
     *   ]
     * }
     */
    public function getUnread(Request $request)
    {
        $limit = $request->input('limit', 20);

        $notifications = auth()->user()
            ->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                $payload = $notification->payload ?? [];
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $payload['message'] ?? '',
                    'payload' => $payload,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => count($notifications),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * Response:
     * {
     *   "success": true,
     *   "message": "Notification marked as read"
     * }
     */
    public function markAsRead($notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->find($notificationId);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        auth()->user()
            ->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }
}
