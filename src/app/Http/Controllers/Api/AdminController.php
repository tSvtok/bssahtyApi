<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Question;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin dashboard and moderation endpoints.
 */
class AdminController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'users_count' => User::count(),
            'questions_count' => Question::count(),
            'spots_count' => Spot::where('status', 'approved')->count(),
            'events_count' => Event::where('status', 'upcoming')->count(),
            'pending_spots_count' => Spot::where('status', 'pending')->count(),
            'top_spots' => Spot::where('status', 'approved')
                ->withCount('questions')
                ->orderByDesc('questions_count')
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * List spots pending approval.
     */
    public function pendingSpots(): JsonResponse
    {
        $spots = Spot::with(['sportCategory', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json($spots);
    }

    /**
     * Approve a pending spot.
     */
    public function approveSpot(Spot $spot): JsonResponse
    {
        $spot->update(['status' => 'approved']);

        return response()->json(['message' => 'Spot approved', 'spot' => $spot]);
    }

    /**
     * Reject a pending spot.
     */
    public function rejectSpot(Spot $spot): JsonResponse
    {
        $spot->update(['status' => 'rejected']);

        return response()->json(['message' => 'Spot rejected', 'spot' => $spot]);
    }
}
