<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles events (sports sessions).
 */
class EventController extends Controller
{
    /**
     * List all upcoming events.
     */
    public function index(): JsonResponse
    {
        $events = Event::with(['user', 'spot', 'question'])
            ->withCount('participants')
            ->where('status', 'upcoming')
            ->where('date', '>=', now())
            ->orderBy('date')
            ->get();

        return response()->json($events);
    }

    /**
     * Create an event from a question.
     */
    public function storeFromQuestion(StoreEventRequest $request, Question $question): JsonResponse
    {
        if ($question->event) {
            return response()->json(['message' => 'This question already has an event'], 422);
        }

        $event = Event::create(array_merge(
            $request->validated(),
            [
                'question_id' => $question->id,
                'user_id' => $request->user()->id,
            ]
        ));

        // Auto-join the creator
        $event->participants()->attach($request->user()->id);
        $event->load(['user', 'spot', 'question', 'participants']);

        return response()->json($event, 201);
    }

    /**
     * Create a standalone event.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = Event::create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        $event->participants()->attach($request->user()->id);
        $event->load(['user', 'spot', 'participants']);

        return response()->json($event, 201);
    }

    /**
     * Show a single event.
     */
    public function show(Event $event): JsonResponse
    {
        $event->load(['user', 'spot', 'question', 'participants']);

        return response()->json($event);
    }

    /**
     * Join an event.
     */
    public function join(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already joined'], 422);
        }

        if ($event->isFull()) {
            return response()->json(['message' => 'Event is full'], 422);
        }

        $event->participants()->attach($user->id);
        $event->load('participants');

        return response()->json([
            'message' => 'Joined successfully',
            'participants_count' => $event->participants->count(),
        ]);
    }

    /**
     * Leave an event.
     */
    public function leave(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        if (!$event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Not a participant'], 422);
        }

        $event->participants()->detach($user->id);
        $event->load('participants');

        return response()->json([
            'message' => 'Left successfully',
            'participants_count' => $event->participants->count(),
        ]);
    }
}
