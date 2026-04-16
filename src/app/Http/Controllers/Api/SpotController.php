<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpotRequest;
use App\Models\Spot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles CRUD for spots with geo-filtering and favorites.
 */
class SpotController extends Controller
{
    /**
     * List all approved spots, optionally filtered by proximity.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Spot::with(['sportCategory', 'creator'])
            ->where('status', 'approved');

        if ($request->has('sport_category_id')) {
            $query->where('sport_category_id', $request->sport_category_id);
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        $spots = $query->latest()->get();

        return response()->json($spots);
    }

    /**
     * Get spots near a location (lat, lng, radius in km).
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:1|max:100',
        ]);

        $spots = Spot::with(['sportCategory', 'creator'])
            ->where('status', 'approved')
            ->nearby(
                $request->lat,
                $request->lng,
                $request->input('radius', 10)
            )
            ->get();

        return response()->json($spots);
    }

    /**
     * Create a new spot (pending approval).
     */
    public function store(StoreSpotRequest $request): JsonResponse
    {
        $spot = Spot::create(array_merge(
            $request->validated(),
            ['created_by' => $request->user()->id]
        ));

        $spot->load(['sportCategory', 'creator']);

        return response()->json($spot, 201);
    }

    /**
     * Show a single spot with reviews.
     */
    public function show(Spot $spot): JsonResponse
    {
        $spot->load(['sportCategory', 'creator', 'reviews.user', 'questions']);

        return response()->json($spot);
    }

    /**
     * Update a spot (creator only).
     */
    public function update(Request $request, Spot $spot): JsonResponse
    {
        if ($request->user()->id !== $spot->created_by && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'sport_category_id' => 'nullable|integer|exists:sport_categories,id',
        ]);

        $spot->update($request->only([
            'name', 'description', 'latitude', 'longitude',
            'address', 'city', 'sport_category_id',
        ]));

        $spot->load(['sportCategory', 'creator']);

        return response()->json($spot);
    }

    /**
     * Delete a spot (creator or admin only).
     */
    public function destroy(Request $request, Spot $spot): JsonResponse
    {
        if ($request->user()->id !== $spot->created_by && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $spot->delete();

        return response()->json(['message' => 'Spot deleted successfully']);
    }

    /**
     * Toggle favorite on a spot.
     */
    public function toggleFavorite(Request $request, Spot $spot): JsonResponse
    {
        $user = $request->user();

        if ($user->favoriteSpots()->where('spot_id', $spot->id)->exists()) {
            $user->favoriteSpots()->detach($spot->id);
            $favorited = false;
        } else {
            $user->favoriteSpots()->attach($spot->id);
            $favorited = true;
        }

        return response()->json(['favorited' => $favorited]);
    }

    /**
     * Add a review to a spot.
     */
    public function addReview(Request $request, Spot $spot): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = $spot->reviews()->create([
            'content' => $request->content,
            'rating' => $request->rating,
            'user_id' => $request->user()->id,
        ]);

        $review->load('user');

        return response()->json($review, 201);
    }
}
