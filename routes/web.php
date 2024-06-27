<?php

use App\Http\Controllers\Api\MessageChildrenController;
use App\Http\Controllers\Api\MessagesController;
use App\Http\Controllers\ChannelsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('/channels/momentum');
});

Route::get('/api/messages/{channel}', [MessagesController::class, 'index']);
Route::get('/api/messages/{message}/children', [MessageChildrenController::class, 'index']);
Route::get('/channels/{channel}', [ChannelsController::class, 'index']);
