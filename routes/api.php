<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->post('/chat/send', [ChatController::class, 'send'])
    ->name('api.chat.send');
