<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Queue;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Event;
use App\Events\NewNotificationEvent;

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->withoutMiddleware(['auth:sanctum']);

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->withoutMiddleware(['auth:sanctum']);

Route::get('/machine-files/{machineType?}/{modelNumber?}/{folderType?}', [App\Http\Controllers\AuthController::class, 'getFiles'])->withoutMiddleware(['auth:sanctum']);

Route::get('/login', function () {
    return response()->json(['error' => 'Unauthenticated.'], 401);
})->name('login');

Route::get('/reverb', function () {
    if (config('app.env') == "local") {
        event(new NewNotificationEvent('This is a real-time notification!'));
    }
    return response()->json(['success' => 'Successfully stored', 'time' => date('F d, Y h:i:s a', strtotime(now())) ], 200);
})->name('reverb.index');

Route::prefix('forgot')->group(function () {

    Route::post('/password-link', [App\Http\Controllers\PageController::class, 'sendForgotPassword']);

    Route::post('/reset-password', [App\Http\Controllers\PageController::class, 'resetPassword'])->name('password.reset');

});


Route::prefix('notify')->group(function () {
    Route::post('/store', [App\Http\Controllers\NotificationController::class, 'store']);
    Route::get('/{userId}/{notifyType}', [App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/update/{notifyID}', [App\Http\Controllers\NotificationController::class, 'update']);
    Route::get('/get/{userId}/unseen', [App\Http\Controllers\NotificationController::class, 'getUnseen']);
});


Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

    Route::prefix('user')->group(function () {
        // Route::get('/', function (Request $request) {
        //     return $request->user();
        // });
        Route::get('/', [App\Http\Controllers\AuthController::class, 'getUserData']);
        Route::post('/update', [App\Http\Controllers\AuthController::class, 'update']);
    });

    Route::prefix('schedule')->group(function () {
        Route::get('/', [App\Http\Controllers\ScheduleController::class, 'index']);
        Route::post('/store', [App\Http\Controllers\ScheduleController::class, 'store']);
    });

    Route::prefix('otp')->group(function () {
        Route::get('/send', [App\Http\Controllers\AuthController::class, 'otpSend']);
        Route::post('/store', [App\Http\Controllers\AuthController::class, 'otpStore']);
    });

});
