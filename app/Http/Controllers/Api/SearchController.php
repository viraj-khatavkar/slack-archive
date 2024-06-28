<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $messages = Message::where('content', 'like', '%' . request('q') . '%')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->where('slack_timestamp', '>=', $request->from_date);
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->where('slack_timestamp', '<=', $request->to_date);
            })
            ->with('user', 'channel', 'parent.user')
            ->withCount('children')
            ->orderBy('slack_timestamp', 'desc')
            ->paginate(10);

        return response()->json($messages);
    }
}
