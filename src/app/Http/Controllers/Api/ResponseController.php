<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles responses (answers) to questions.
 */
class ResponseController extends Controller
{
    /**
     * Store a new response for a question.
     */
    public function store(Request $request, Question $question): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $response = $request->user()->responses()->create([
            'content' => $request->content,
            'question_id' => $question->id,
        ]);

        $response->load('user');

        return response()->json($response, 201);
    }

    /**
     * Delete a response (owner only).
     */
    public function destroy(Request $request, Response $response): JsonResponse
    {
        if ($request->user()->id !== $response->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $response->delete();

        return response()->json(['message' => 'Response deleted successfully']);
    }
}
