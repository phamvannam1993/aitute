<?php

use App\Http\Controllers\Client\AffController;
use App\Http\Controllers\Client\AIImageController;
use App\Http\Controllers\Client\AlepayWebhookController;
use App\Http\Controllers\Client\CreditPackageController;
use App\Http\Controllers\Client\FacebookContentController;
use App\Http\Middleware\AlepayWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ZaloChatBotController;
use App\Http\Controllers\Client\MessengerController;

Route::post('/zalo-chatbot', [ZaloChatBotController::class, 'zaloChatBot']);

Route::post('/purchase-account', [AffController::class, 'purchaseAccount']);

Route::post('/register-keys', [AffController::class, 'generateKeys']);

Route::resource('credit-packages', CreditPackageController::class,['as' => 'api']);

Route::middleware([AlepayWebhook::class])->group(function () {
    Route::post('/alepay/webhook', AlepayWebhookController::class)
        ->name('alepay.webhook');
});

Route::get('/messenger-webhook', [MessengerController::class, 'handleGetWebhook'])->name('messenger-get-webhook');
Route::post('/messenger-webhook', [MessengerController::class, 'handlePostWebhook'])->name('messenger-post-webhook');
Route::post('/forward-messenger-webhook', [MessengerController::class, 'handleForWardPostWebhook'])->name('forward-messenger-post-webhook');

Route::post('/my-ai-images-webhook', [AIImageController::class, 'webhookMyAIImage']);
Route::post('/facebook-content/save', [FacebookContentController::class, 'apiSaveContent']);
Route::get('/facebook-content/post', [FacebookContentController::class, 'postFacebook']);
Route::get('/facebook-content/cron', [FacebookContentController::class, 'postFacebookCron']);