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
            ->when($request->channel_id, function ($query) use ($request) {
                if ($request->channel_id > 0) {
                    return $query->where('channel_id', $request->channel_id);
                }

                return $query;
            })
            ->with('user', 'channel', 'parent.user')
            ->withCount('children');

        if ($request->sort_by === 'children_count') {
            $messages = $messages
                ->whereNull('parent_id')
                ->orderBy('children_count', $request->sort_direction);
        } else {
            $messages = $messages->orderBy($request->sort_by, $request->sort_direction);
        }


        $messages = $messages->paginate(25);

        return response()->json($messages);
    }
}
