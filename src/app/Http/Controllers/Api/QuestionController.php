<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles CRUD for questions with likes and filtering.
 */
class QuestionController extends Controller
{
    /**
     * List all questions with eager-loaded relations.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Question::with(['user', 'spot', 'sportCategory'])
            ->withCount('responses');

        if ($request->has('sport_category_id')) {
            $query->where('sport_category_id', $request->sport_category_id);
        }

        if ($request->has('spot_id')) {
            $query->where('spot_id', $request->spot_id);
        }

        $questions = $query->latest()->get();

        return response()->json($questions);
    }

    /**
     * Create a new question.
     */
    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $question = $request->user()->questions()->create(
            $request->validated()
        );

        $question->load(['user', 'spot', 'sportCategory']);

        return response()->json($question, 201);
    }

    /**
     * Show a single question with responses.
     */
    public function show(Question $question): JsonResponse
    {
        $question->load(['user', 'spot', 'sportCategory', 'responses.user', 'event']);

        return response()->json($question);
    }

    /**
     * Update a question (owner only).
     */
    public function update(Request $request, Question $question): JsonResponse
    {
        if ($request->user()->id !== $question->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'spot_id' => 'nullable|integer|exists:spots,id',
            'sport_category_id' => 'nullable|integer|exists:sport_categories,id',
        ]);

        $question->update($request->only(['title', 'content', 'spot_id', 'sport_category_id']));
        $question->load(['user', 'spot', 'sportCategory']);

        return response()->json($question);
    }

    /**
     * Delete a question (owner only).
     */
    public function destroy(Request $request, Question $question): JsonResponse
    {
        if ($request->user()->id !== $question->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete();

        return response()->json(['message' => 'Question deleted successfully']);
    }

    /**
     * Toggle like on a question.
     */
    public function toggleLike(Request $request, Question $question): JsonResponse
    {
        $user = $request->user();

        if ($user->likedQuestions()->where('question_id', $question->id)->exists()) {
            $user->likedQuestions()->detach($question->id);
            $question->decrement('likes_count');
        } else {
            $user->likedQuestions()->attach($question->id);
            $question->increment('likes_count');
        }

        return response()->json([
            'liked' => $user->likedQuestions()->where('question_id', $question->id)->exists(),
            'likes_count' => $question->fresh()->likes_count,
        ]);
    }
}
