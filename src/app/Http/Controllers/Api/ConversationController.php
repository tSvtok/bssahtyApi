<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles private conversations and messaging.
 */
class ConversationController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $conversations = $request->user()
            ->conversations()
            ->with(['users', 'latestMessage.user'])
            ->latest('conversations.updated_at')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Start or get an existing conversation with another user.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $otherUserId = $request->user_id;
        $currentUserId = $request->user()->id;

        if ($otherUserId === $currentUserId) {
            return response()->json(['message' => 'Cannot start a conversation with yourself'], 422);
        }

        // Check if conversation already exists between these two users
        $existingConversation = Conversation::whereHas('users', function ($q) use ($currentUserId) {
            $q->where('users.id', $currentUserId);
        })->whereHas('users', function ($q) use ($otherUserId) {
            $q->where('users.id', $otherUserId);
        })->first();

        if ($existingConversation) {
            $existingConversation->load(['users', 'latestMessage']);
            return response()->json($existingConversation);
        }

        $conversation = Conversation::create();
        $conversation->users()->attach([$currentUserId, $otherUserId]);
        $conversation->load(['users', 'latestMessage']);

        return response()->json($conversation, 201);
    }

    /**
     * Get messages for a conversation.
     */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        // Verify user is part of this conversation
        if (!$conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        $conversation->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        // Verify user is part of this conversation
        if (!$conversation->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $message = $conversation->messages()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        $conversation->touch();
        $message->load('user');

        return response()->json($message, 201);
    }
}
