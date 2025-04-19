<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Queue;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Event;
use App\Events\NewNotificationEvent;
use App\Http\Middleware\EnsureTokenIsNotExpired;

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->withoutMiddleware(['auth:sanctum']);

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->withoutMiddleware(['auth:sanctum']);

Route::get('/machine-files/{machineType?}/{brandName?}/{modelNumber?}/{folderType?}', [App\Http\Controllers\AuthController::class, 'getFiles'])->withoutMiddleware(['auth:sanctum']);

    
Route::prefix('chat')->group(function () {
    Route::post('/listings', [App\Http\Controllers\ChatController::class, 'index']);
    Route::post('/store', [App\Http\Controllers\ChatController::class, 'store']);
});

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

Route::prefix('firebase')->group(function () {
    Route::post('/store', [App\Http\Controllers\NotificationController::class, 'storeFirebaseToken']);
    Route::post('/notification', [App\Http\Controllers\NotificationController::class, 'callFirebaseNotification']);
});



// Route::middleware('auth:sanctum')->group(function () {
Route::middleware(['auth:sanctum', EnsureTokenIsNotExpired::class])->group(function () {
    
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

    Route::prefix('gig')->group(function () {
        Route::post('/report', [App\Http\Controllers\GigController::class, 'update']);
        Route::get('/quick/history/{client_id}', [App\Http\Controllers\GigController::class, 'indexQuickHistory']);
        Route::post('/send_sms/contact', [App\Http\Controllers\GigController::class, 'sendGigSMS']);
        Route::post('/travel-time', [App\Http\Controllers\GigController::class, 'getTravelTime']);
    });

    Route::prefix('temporary')->group(function () {
        Route::post('/image/upload', [App\Http\Controllers\GigController::class, 'storeTemporaryImage']);
        Route::post('/image/delete-image', [App\Http\Controllers\GigController::class, 'destroyTemporaryImage']);
    });

    Route::prefix('rating')->group(function () {
        Route::get('/rules', [App\Http\Controllers\RatingController::class, 'index']);
    });


    Route::prefix('machine')->group(function () {
        Route::get('/{modelNumber}', [App\Http\Controllers\GigController::class, 'getGigsPerMachine']);
    });
    
    Route::prefix('dax')->group(function () {
        Route::post('/chat', [App\Http\Controllers\DaxController::class, 'chat']);
        Route::post('/voice-chat', [App\Http\Controllers\DaxController::class, 'voiceChat']);
        Route::get('/openai/files', [App\Http\Controllers\DaxController::class, 'getFiles']);
        Route::get('/sync/files/{modelNumber?}', [App\Http\Controllers\DaxController::class, 'syncMachineFiles']);
    });

    Route::prefix('openai')->group(function () {
        Route::get('/vector_stores', [App\Http\Controllers\DaxController::class, 'getVectorListings']);
        Route::post('/vector_stores/{vectorID}', [App\Http\Controllers\DaxController::class, 'updateVectorID']);
    });

    Route::prefix('analytics')->group(function () {
        Route::get('/report/{techId?}', [App\Http\Controllers\AnalyticsController::class, 'index']);
    });


    


});
