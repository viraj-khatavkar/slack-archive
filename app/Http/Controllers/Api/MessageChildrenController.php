<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageChildrenController extends Controller
{
    public function index(Message $message)
    {
        $children = $message->children()
            ->with('user')
            ->orderBy('slack_timestamp', 'asc')
            ->get();

        return response()->json($children);
    }
}
