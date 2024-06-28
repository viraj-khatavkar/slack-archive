<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function index(Channel $channel, Request $request)
    {
        $messages = Message::where('channel_id', $channel->id)
            ->whereNull('parent_id')
            ->with('user')
            ->withCount('children')
            ->orderBy('slack_timestamp', $request->get('sort_direction', 'desc'))
            ->paginate(25);

        return response()->json($messages);
    }
}
