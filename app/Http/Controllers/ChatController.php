<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function index()
{
    $user = auth()->user();
    $team = $user->currentTeam;

    $messages = ChatMessage::with('user')
        ->where('team_id', $team->id)
        ->orderBy('id', 'asc')
        ->take(100)
        ->get();

    return view('chat', compact('messages'));
}

public function send(Request $request)
{
    \Log::info('Chat send called with:', $request->all());

    // Log session ID and CSRF tokens for debugging
    \Log::info('Session ID: ' . session()->getId());
    \Log::info('CSRF Token from session: ' . session('_token'));
    \Log::info('CSRF Token from header: ' . $request->header('X-CSRF-TOKEN'));
    \Log::info('CSRF Token from request input: ' . $request->input('_token'));

    $request->validate([
        'message' => 'required|string|max:2000',
    ]);

    $user = auth()->user();
    $team = $user->currentTeam;

    $message = ChatMessage::create([
        'team_id' => $team->id,
        'user_id' => $user->id,
        'message' => $request->message,
    ]);

    // Return JSON response with the created message and success status
    return response()->json([
        'status' => 'success',
        'message' => $message->load('user'),
    ]);
}

public function fetchMessages(Request $request)
{
    $user = auth()->user();
    $team = $user->currentTeam;
    $lastMessageId = $request->query('lastMessageId', 0);

    return ChatMessage::with('user')
        ->where('team_id', $team->id)
        ->where('id', '>', $lastMessageId)
        ->orderBy('id', 'asc')
        ->get();
}


}

