<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelsController extends Controller
{
    public function index(Channel $channel)
    {
        return view('app.channels.index', ['channel' => $channel]);
    }
}
