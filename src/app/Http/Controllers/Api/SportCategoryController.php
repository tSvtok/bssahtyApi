<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SportCategory;
use Illuminate\Http\JsonResponse;

/**
 * Handles sport categories listing.
 */
class SportCategoryController extends Controller
{
    /**
     * List all sport categories.
     */
    public function index(): JsonResponse
    {
        $categories = SportCategory::withCount(['questions', 'users'])->get();

        return response()->json($categories);
    }
}
