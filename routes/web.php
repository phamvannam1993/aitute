<?php

use App\Constants\FeatureConstants;
use App\Http\Controllers\Admin\BackgroundImageController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\SlidePasteController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Client\AIBackgroundController;
use App\Http\Controllers\Client\AiBusinessResolveController;
use App\Http\Controllers\Client\AIPersonalController;
use App\Http\Controllers\Client\AIVirtualController;
use App\Http\Controllers\Client\FacebookAppController;
use App\Http\Controllers\Client\FacebookContentController;
use App\Http\Controllers\Client\AIAudioController;
use App\Http\Controllers\Client\AIBusinessProjectController;
use App\Http\Controllers\Client\AIProjectExpertController;
use App\Http\Controllers\Client\MessengerController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ImageController;
use App\Http\Controllers\Client\ImageProxyController;
use App\Http\Controllers\Client\JobsController;
use App\Http\Controllers\Client\KeyStatisticsController;
use App\Http\Controllers\Client\OperationController;
use App\Http\Controllers\Client\ShareLinkController;
use App\Http\Controllers\Client\SpeechToTextController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\SocialPostController;
use App\Http\Controllers\Client\ShortVideoController;
use App\Http\Controllers\Client\VideoController;
use App\Http\Controllers\Client\CreditController;
use App\Http\Controllers\Client\AIVideoController;
use App\Http\Controllers\Client\AIChatController;
use App\Http\Controllers\Client\AIImageController;
use App\Http\Controllers\Client\AiTextController;
use App\Http\Controllers\Client\PageManagementController;
use App\Http\Controllers\Client\CalendarController;
use App\Http\Controllers\Client\FacebookFanpageController;
use App\Http\Controllers\Client\FavoriteController;
use App\Http\Controllers\Client\PopularController;
use App\Http\Controllers\Client\ThanSoHocController;
use App\Http\Controllers\Client\TimeLineController;
use App\Http\Controllers\Client\AiImageAdvertisementController;
use App\Http\Controllers\Client\ProductImageController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\CreatomateController;
use App\Http\Controllers\Client\CreditHistoryController;
use App\Http\Controllers\Client\FacebookController;
use App\Http\Controllers\Client\MyAICollectionController;
use App\Http\Controllers\Client\MyAssistantsController;
use App\Http\Controllers\Client\PricingController;
use App\Http\Controllers\Client\SocialController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\PostController;
use App\Http\Controllers\Client\RulesController;
use App\Http\Controllers\Client\VideoToTextController;
use App\Http\Controllers\Client\TextToVideoController;
use App\Http\Controllers\Client\TextToMusicController;
use App\Http\Controllers\Client\TextToSongController;
use App\Http\Controllers\Client\AIDifyController;
use App\Http\Controllers\Client\ChatTrainingDocumentController;
use App\Http\Controllers\Client\CustomerCareController;
use App\Http\Controllers\Client\FacebookConfigController;
use App\Http\Controllers\Client\ImageCombineController;
use App\Http\Controllers\Client\SearchImageController;
use App\Http\Controllers\Client\UserSettingController;
use App\Http\Controllers\Client\VoiceTypeController;
use App\Http\Controllers\YoutubeController;
use App\Http\Middleware\AffMiddleware;
use App\Http\Middleware\CheckFacebookApp;
use App\Http\Controllers\Client\GoogleSearchController;
use App\Http\Controllers\Client\ManagementController;

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/others-ai', [HomeController::class, 'othersAi'])->name('home.others-ai');
Route::get('/auth-check', [AuthenticatedSessionController::class, 'checkAuthStatus']);

Route::get('/terms', [RulesController::class, 'terms'])->name('rules.terms');
Route::get('/privacy', [RulesController::class, 'privacy'])->name('rules.privacy');
Route::post('background-images', [BackgroundImageController::class, 'store'])->name('background-images.store');
Route::get('background-images', [BackgroundImageController::class, 'index'])->name('background-images.index');
Route::get('background-images-component', [BackgroundImageController::class, 'component'])->name('background-images.component');
Route::controller(HomeController::class)->group(function () {
    Route::post('/load-more', [HomeController::class, 'loadMore'])->name('load-more');
    Route::match(['get', 'post'], '/search', [HomeController::class, 'search'])->name('search');
    Route::match(['get', 'post'], '/search-assistants', [HomeController::class, 'searchAssistants'])->name('search-assistants');
    Route::match(['get', 'post'], '/search-occupations', [HomeController::class, 'searchOccupations'])->name('search-occupations');
    Route::post('/enterprise-field',  [HomeController::class, 'updateEnterpriseField'])->name('home.enterprise-field');
});

Route::get('/check-package-permission', [UserSettingController::class, 'checkPackagePermission'])->name('checkPackagePermission');

Route::middleware(['auth'])->group(function () {
    Route::post('/speech-to-text', [SpeechToTextController::class, 'callGoogleSpeechToText']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('ai-chat')->name('ai-chat.')->middleware('checkFeature:' . FeatureConstants::AI_CHAT_BOT)->group(function () {
        Route::get('/{id?}', [AIChatController::class, 'index'])->name('index');
        Route::post('/send-message', [AIChatController::class, 'chat'])->name('chat');
        Route::get('/message-histories/{sessionId}', [AIChatController::class, 'messageHistories'])->name('message-histories');
        Route::get('/delete/{id?}', [AIChatController::class, 'deleteSession'])->name('delete-session');
        Route::get('/delete-message/{id?}', [AIChatController::class, 'deleteMessage'])->name('delete-message');
        Route::get('/chat/history', [AIChatController::class, 'getChatHistory']);
        Route::get('/histories', [AIChatController::class, 'getMessageHistories'])->name('histories');
        Route::post('/check-all-messages-status', [AIChatController::class, 'checkAllMessagesStatus'])->name('check-all-messages-status');
        Route::get('/chat/get-session', [AIChatController::class, 'getSessionId'])->name('getSessionId');
    });

    Route::prefix('credit')->name('credit.')->group(function () {
        Route::post('/check-credit', [CreditController::class, 'check_credit'])->name('check_credit');
    });

    Route::prefix('ai-text')->name('ai-text.')->middleware('checkFeature:' . FeatureConstants::AI_TEXT)->group(function () {
        Route::post('/docs', [AiTextController::class, 'handleConversation'])->name('docs');
        Route::get('/docs', [AiTextController::class, 'getConversation'])->name('docs');
        Route::post('/send-ask-stream-claude', [AiTextController::class, 'handleAskClaudeStream'])->name('askClaudeStream');
        Route::post('/send-ask-stream-gpt', [AiTextController::class, 'handleAskGptStream'])->name('askGptStream');
        Route::post('/send-ask-multi-stream-claude', [AiTextController::class, 'handleMultiStepProcess'])->name('askMultiClaudeStream');
        Route::post('/send-ask-multi-stream-gpt', [AiTextController::class, 'handleMultiStepProcessGpt'])->name('askMultiGptStream');
        Route::post('/send-chat', [AiTextController::class, 'handleSendChat'])->name('send-chat');

        Route::get('/list-chat', [AiTextController::class, 'getListChat'])->name('list-chat');
        Route::post('/delete-chat', [AiTextController::class, 'deleteChat'])->name('delete-chat');
        Route::get('/list-chat-item', [AiTextController::class, 'getListChatItem'])->name('list-chat-item');

        Route::get('/{id}', [AiTextController::class, 'index'])->name('index');
        Route::get('/detail/{assistantId}', [AiTextController::class, 'detail'])->name('detail');
        Route::post('/send-data', [AiTextController::class, 'send_text'])->name('send');
        Route::post('/send-gpt', [AiTextController::class, 'send_gpt'])->name('send_gpt');
        Route::post('/send-gpt-stream', [AiTextController::class, 'stream_gpt'])->name('stream_gpt');
        Route::post('/claude-stream', [AiTextController::class, 'claudeStream'])->name('claudeStream');
        Route::post('/claude-music', [AiTextController::class, 'generateMusic'])->name('music');
        Route::post('/claude', [AiTextController::class, 'claude'])->name('claude');
        Route::post('/stream', [AiTextController::class, 'stream'])->name('stream');
        Route::post('/store-data', [AiTextController::class, 'store'])->name('store');
        Route::post('/delete/{id}', [AiTextController::class, 'deleteText'])->name('delete-text');
        Route::get('/{id}/load', [AiTextController::class, 'loadMore'])->name('load');
    });

    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobsController::class, 'index'])->name('jobs.index');
        Route::post('/load-all-more', [JobsController::class, 'loadAllMore'])->name('jobs.load-all-more');
        Route::get('/{id}', [JobsController::class, 'show'])->name('jobs.show');
        Route::post('/load-more', [JobsController::class, 'loadMore'])->name('jobs.load-more');
        Route::post('/search', [JobsController::class, 'search'])->name('jobs.search');
        Route::get('/{id}', [JobsController::class, 'show'])->name('jobs.show');
    });
    Route::prefix('operation')->group(function () {
        Route::get('/', [OperationController::class, 'index'])->name('operation.index');
        Route::get('/{id}', [OperationController::class, 'show'])->name('operation.show');
        Route::post('/search', [OperationController::class, 'search'])->name('operation.search');
        Route::post('/search-pb', [OperationController::class, 'search_pb'])->name('operation.search_pb');

        Route::post('/load-more', [OperationController::class, 'loadMore'])->name('operation.load-more');
        Route::post('/{operation_id}/interest', [OperationController::class, 'toggleInterest'])->name('operation.interest');
    });

    // AI Image routes
    Route::prefix('ai-image')->name('ai-image.')->group(function () {
        // Swap Face routes - check quyền AI_SWAP_FACE
        Route::middleware('checkFeature:' . FeatureConstants::AI_SWAP_FACE)->group(function () {
            Route::post('/swap-face', [AIImageController::class, 'generateFaceSwap'])->name('generate-swap-face');
            Route::get('/swap-face', [AIImageController::class, 'faceSwap'])->name('faceSwap');
            Route::get('/swap-face-history', [AIImageController::class, 'historyfaceSwap'])->name('historyfaceSwap');
        });

        // AI Image routes - check quyền AI_IMAGE
        Route::middleware('checkFeature:' . FeatureConstants::AI_IMAGE)->group(function () {
            Route::get('/', [AIImageController::class, 'index'])->name('index');
            Route::post('/generate-image', [AIImageController::class, 'generateImage'])->name('generate');
            Route::post('/generate-image-for-post', [AIImageController::class, 'generateImageForPost'])->name('generate-image-for-post');
            Route::get('/history', [AIImageController::class, 'history'])->name('history');
            Route::get('/get-list-media', [AIImageController::class, 'listMedia'])->name('list-media');
            Route::get('/history/beauty-image', [AIImageController::class, 'historyBeautyImage'])->name('historyBeautyImage');
            Route::get('/photo-beauty', [AIImageController::class, 'photoBeauty'])->name('photoBeauty');
        });
        Route::middleware('checkFeature:' . FeatureConstants::AI_IMAGE_ROOT)->group(function () {
            Route::get('/image', [AIImageController::class, 'imageToImage'])->name('imageToImage');
            Route::post('/generate-image-to-image', [AIImageController::class, 'generateImageToImage'])->name('generateImageToImage');
        });
        Route::post('/generate-photo-beauty', [AIImageController::class, 'generatePhotoBeauty'])->name('generatePhotoBeauty');
        Route::get('/get-image/{id}', [AIImageController::class, 'getImage'])->name('getImage');
        Route::get('/show-image', [AIImageController::class, 'showImage'])->name('showImage');
        // Api nay được dùng chung ở AI_IMAGE và AI_IMAGE_TO_VIDEO
        Route::get('/suggest-content', [AIImageController::class, 'suggestContent'])->name('suggest-content');
        Route::get('/get-list-media', [AIImageController::class, 'listMedia'])->name('list-media');
        Route::get('/get-list-media-by-model', [AIImageController::class, 'getMediaListByType'])->name('list-media-by-model');
        Route::middleware('checkFeature:' . FeatureConstants::MY_AI_IMAGE)->group(function () {
            Route::post('/my-ai-image', [AIImageController::class, 'generateMyAIImage'])->name('generate-my-ai-image');
            Route::get('/my-ai-image', [AIImageController::class, 'myAIImage'])->name('my-ai-image');
            Route::get('/my-ai-image-history', [AIImageController::class, 'historyMyAIImage'])->name('history-my-ai-image');

            Route::get('/training-my-ai-image', [AIImageController::class, 'showTrainModel'])->name('training-my-ai-image');
            Route::post('/fine-tune', [AiImageController::class, 'fineTune'])->name('fine-tune');
            Route::get('/has-fine-tune-model', [AIImageController::class, 'hasFineTuneModel'])->name('has-fine-tune-model');
        });
        // những api full quyền
        Route::get('/get-latest-my-ai-image', [AIImageController::class, 'getLatestMyAIImage'])->name('get-latest-my-ai-image');
        Route::post('/delete/{id}', [AIImageController::class, 'delete'])->name('delete');
        Route::get('/has-my-ai-image', [AIImageController::class, 'hasMyAIImage'])->name('has-my-ai-image');
        Route::get('/get-my-ai-image-history', [AIImageController::class, 'getHistoryMyAIImage'])->name('get-history-my-ai-image');
        Route::post('/improve-quality-image', [AIImageController::class, 'improveQualityImage'])->name('improve-quality-image');
        Route::post('/get-image-description', [AIImageController::class, 'getImageDescription'])->name('get-image-description');
        Route::get('/api-ai-image-history', [AIImageController::class, 'apiHistoryMyAIImage'])->name('api-ai-image-history');
    });

    Route::prefix('ai-image-advertisement')->name('ai-image-advertisement.')->middleware('checkFeature:' . FeatureConstants::AI_BANNER_POSTER)->group(function () {
        Route::get('/', [AiImageAdvertisementController::class, 'index'])->name('index');
        Route::post('/generate-image', [AiImageAdvertisementController::class, 'generateImage'])->name('generate');
        Route::get('/history', [AiImageAdvertisementController::class, 'history'])->name('history');
        Route::post('/delete/{id}', [AiImageAdvertisementController::class, 'delete'])->name('delete');
    });

    Route::prefix('ai-background')->name('ai-background.')->middleware('checkFeature:' . FeatureConstants::AI_BACKGROUND)->group(function () {
        Route::get('/', [AIBackgroundController::class, 'index'])->name('index');
        Route::get('/v2', [AIBackgroundController::class, 'indexV2'])->name('indexV2');
        Route::get('/history', [AIBackgroundController::class, 'history'])->name('history');
        Route::post('/generate-ai-background', [AIBackgroundController::class, 'generateAiBackground'])->name('generate-ai-background');
        Route::post('/delete/{id}', [AIBackgroundController::class, 'delete'])->name('delete');
        Route::post('/generate-ai-background-v2', [AIBackgroundController::class, 'generateAiBackgroundV2'])->name('generate-ai-background-v2');
        Route::post('/generate-ai-background-v2-with-file', [AIBackgroundController::class, 'generateAiBackgroundV2WithFile'])->name('generate-ai-background-v2-with-file');
        Route::get('/api-history', [AIBackgroundController::class, 'apiHistory'])->name('api-history');
    });

    Route::prefix('time-line')->name('time-line.')->group(function () {
        Route::get('/', [TimeLineController::class, 'index'])->name('index');
    });

    Route::prefix('credit')->name('credit.')->group(function () {
        Route::get('/history', [CreditHistoryController::class, 'index'])->name('history');
        Route::get('/ajax-get-data', [CreditHistoryController::class, 'ajaxGetData'])->name('ajaxGetData');
    });

    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::post('/update-favorite', [HomeController::class, 'favorite'])->name('update');
    });

    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
    });

    Route::prefix('ai-audio')->name('ai-audio.')->middleware('checkFeature:' . FeatureConstants::AI_AUDIO)->group(function () {
        Route::get('/', [AIAudioController::class, 'index'])->name('index');
        Route::get('/load', [AIAudioController::class, 'loadMore'])->name('load');
        Route::post('/send', [AIAudioController::class, 'send_data'])->name('send');
        Route::post('/send', [AIAudioController::class, 'textToSpeech'])->name('send');
        Route::get('/audio-to-text', [AIAudioController::class, 'audioToText'])->name('audioToText');
        Route::post('/upload', [AIAudioController::class, 'upload'])->name('upload');
        Route::post('/upload/v2', [AIAudioController::class, 'uploadV2'])->name('uploadV2');
        Route::post('/delete/{id}', [AIAudioController::class, 'delete'])->name('delete');
        Route::post('/vbee-callback', [AIAudioController::class, 'handleCallback'])->name('vbee.callback');
        Route::delete('/delete-voice/{id}', [AIAudioController::class, 'deleteVoice'])->name('delete-voice');

        Route::post('/upload-to-s3', [AIAudioController::class, 'uploadToS3'])->name('upload-s3');
        Route::post('/clone-voice', [AIAudioController::class, 'cloneVoice'])->name('clone-voice');
        Route::post('/text-to-speech', [AIAudioController::class, 'textToSpeechWithVoiceId'])->name('text-to-speech');
        Route::post('/text-to-speech-google', [AIAudioController::class, 'textToSpeechWithGoogle'])->name('text-to-speech-google');
        Route::get('list-voices', [AIAudioController::class, 'listVoices'])->name('list-voices');
    });

    Route::post('/ai-generate-content', [AIImageController::class, 'generateContent'])->name('generate-content-with-ai');

    Route::prefix('page-management')->name('page-management.')->group(function () {
        Route::get('/', [PageManagementController::class, 'index'])->name('index');
    });

    Route::prefix('social')->name('social.')->group(function () {
        Route::post('/store', [SocialPostController::class, 'store'])->name('store');
    });

    Route::middleware([CheckFacebookApp::class])->group(function () {
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
        Route::get('/get-calendar', [CalendarController::class, 'getCalendar'])->name('get-calendar');
        Route::get('/get-facebooks-fanpages-user', [CalendarController::class, 'getFacebooksFanpagesUser'])->name('get-facebooks-fanpages-user');
        Route::get('/get-social-postable-type', [CalendarController::class, 'getSocialPostableType'])->name('get-social-postable-type');

        Route::prefix('social')->name('social.')->group(function () {
            Route::post('/upload-image', [SocialPostController::class, 'uploadImage'])->name('upload-image');
            //social.post-to-facebook
            Route::post('/post-to-facebook', [SocialPostController::class, 'postToFanpage'])->name('post-to-facebook');
            //social.ajax-post-to-facebook
            Route::post('/ajax-post-to-facebook', [SocialPostController::class, 'ajaxPostToFanpage'])->name('ajax-post-to-facebook');
            //social.ajax-store-social-post
            Route::post('/ajax-store-social-post', [SocialPostController::class, 'ajaxStoreSocialPost'])->name('ajax-store-social-post');
            //social.ajax-update-social-post
            Route::post('/ajax-update-social-post', [SocialPostController::class, 'ajaxUpdateSocialPost'])->name('ajax-update-social-post');
            //social.multi-post-to-facebook
            Route::post('/multi-post-to-facebook', [SocialPostController::class, 'multiPostToFanpage'])->name('multi-post-to-facebook');
            Route::post('/update-to-facebook', [SocialPostController::class, 'updateToFanpage'])->name('update-to-facebook');
            Route::delete('/{id}', [SocialPostController::class, 'destroy'])->name('destroy');
            Route::delete('/ajax-destroy/{id}', [SocialPostController::class, 'ajaxDestroy'])->name('ajaxDestroy');


            Route::post('/post-to-tiktok', [SocialPostController::class, 'postToTikTok']);
            //social.job-create-content-ai
            Route::post('/job-create-content-ai', [SocialPostController::class, 'jobCreateContentAI'])->name('job-create-content-ai');
            Route::post('/ajax-job-create-content-ai', [SocialPostController::class, 'ajaxJobCreateContentAI'])->name('ajax-job-create-content-ai');

            Route::prefix('facebook')->name('facebook.')->group(function () {
                //social.facebook.fanpages
                Route::post('/fanpages-facebook/delete', [FacebookFanpageController::class, 'deletePage'])->name('deletePage');
                Route::get('/fanpages-facebook-api', [FacebookFanpageController::class, 'getFanpagesFacebookApi'])->name('fanpages');
                Route::resource('facebook-fanpages', FacebookFanpageController::class);
                Route::group(['prefix' => 'access-token'], function () {
                    //social.facebook.request-access-token
                    Route::get('/', [SocialController::class, 'redirectToProvider'])->name('request-access-token');
                    //social.facebook.callback-access-token
                    Route::get('/callback-access-token', [SocialController::class, 'handleProviderCallback'])->name('callback-access-token');
                });
            });
        });
    });

    Route::prefix('facebook')->name('social.facebook.')->group(function () {
        //social.facebook.create
        Route::get('/create', [FacebookController::class, 'create'])->name('create');
        //social.facebook.document
        Route::get('/document', [FacebookController::class, 'document'])->name('document');
        //social.facebook.store
        Route::post('/store', [FacebookController::class, 'store'])->name('store');
        Route::get('/list-content', [FacebookContentController::class, 'getList'])->name('list-content');
        Route::get('/total-content', [FacebookContentController::class, 'getTotal'])->name('total-content');
        Route::get('/facebook-content/detail', [FacebookContentController::class, 'getDetail'])->name('facebook-content-detail');
        Route::post('/facebook-content/update', [FacebookContentController::class, 'update'])->name('update-facebook-content');
        Route::get('/facebook-content/delete/{id}', [FacebookContentController::class, 'delete'])->name('delete-facebook-content');
        Route::post('/facebook-content/post', [FacebookContentController::class, 'postFacebook'])->name('post-facebook');
        Route::post('/facebook-content/confirm-post-facebook', [FacebookContentController::class, 'confirmPostFacebook'])->name('confirm-post-facebook');
    });



    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::post('/search', [FavoriteController::class, 'search'])->name('search');
        Route::post('/load-more', [FavoriteController::class, 'loadMore'])->name('load-more');
    });
    Route::prefix('popular')->name('popular.')->group(function () {
        Route::get('/', [PopularController::class, 'index'])->name('index');
        Route::post('/search', [PopularController::class, 'search'])->name('search');
        Route::post('/load-more', [PopularController::class, 'loadMore'])->name('load-more');
        Route::post('/get-list', [PopularController::class, 'getAllAssistants'])->name('get-all');
    });
    Route::prefix('short-video')->name('short-video.')->group(function () {
        Route::get('/get-video-url-run/{shortVideoId}', [ShortVideoController::class, 'getVideoURLRun'])->name('getVideoURLRun');
        Route::get('/detail/{id}', [ShortVideoController::class, 'detail'])->name('detail');
        Route::post('/generate-AI-video-audio', [ShortVideoController::class, 'generateVideoAudio'])->name('generate-video-audio');
        Route::post('/merge-audio-video-queue', [ShortVideoController::class, 'mergeAudioVideoQueue'])->name('mergeAudioVideoQueue');
        Route::post('/merge-video-queue', [ShortVideoController::class, 'mergeVideoQueue'])->name('mergeVideo');
        Route::post('/check-file-audio', [ShortVideoController::class, 'checkAudio'])->name('checkAudio');
        Route::post('/check-size-image', [ShortVideoController::class, 'checkSizeImage'])->name('checkSizeImage');
        Route::post('/upload-image-to-s3', [ShortVideoController::class, 'uploadImageToS3'])->name('uploadImageToS3');
        Route::post('/create-video-prediction', [ShortVideoController::class, 'createVideoPrediction'])->name('create-video-prediction');
        Route::post('/merge-video-auto', [ShortVideoController::class, 'mergeVideoAuto'])->name('merge-video-auto');
        Route::post('/get-audio-des', [ShortVideoController::class, 'getAudioDes'])->name('getAudioDes');
        Route::post('/create-video-ai', [ShortVideoController::class, 'createShortVideoAIV2'])->name('create-video-ai');
        Route::post('/create-video-ai-v3', [ShortVideoController::class, 'createShortVideoAIV3'])->name('create-video-ai-v3');
        Route::post('/merge-video-auto-v2', [ShortVideoController::class, 'mergeVideoAutoV2'])->name('merge-video-auto-v2');
        Route::post('/merge-video-auto-v3', [ShortVideoController::class, 'mergeVideoAutoV3'])->name('merge-video-auto-v3');
    });

    Route::prefix('video')->name('video.')->group(function () {
        Route::post('/upload-image', [VideoController::class, 'uploadImage'])->name('uploadImage');
        Route::get('/history', [VideoController::class, 'history'])->name('history');
        Route::get('/edit/{id}', [VideoController::class, 'edit'])->name('edit');
        Route::get('/history/{id}', [VideoController::class, 'historyDetail'])->name('historyDetail');
        Route::get('/api-history', [VideoController::class, 'ApiGetHistory'])->name('ApiGetHistory');
        Route::get('/detail/{id}', [VideoController::class, 'detail'])->name('detail');
        Route::post('/delete/{id}', [VideoController::class, 'delete'])->name('delete');
        Route::post('/merge-video-queue', [VideoController::class, 'mergeVideoQueue'])->name('mergeVideo');
        Route::post('/merge-audio-video-queue', [VideoController::class, 'mergeAudioVideoQueue'])->name('mergeAudioVideoQueue');
        Route::post('/create-video-with-transcription', [VideoController::class, 'generateVideoWithTranscriptionWithSegmind'])->name('create-video-with-transcription');
        Route::post('/read-file', [VideoController::class, 'readFile'])->name('read-file');
        Route::get('/get-xfade', [VideoController::class, 'getXface'])->name('getXface');
        Route::post('/upload-video-to-s3', [VideoController::class, 'uploadVideoToS3'])->name('uploadVideoToS3');
    });

    Route::prefix('ai-video')->name('ai-video.')->group(function () {
        Route::get('/get-video-url/{taskId}', [AIVideoController::class, 'getVideoURL'])->name('getVideoURL');
        Route::get('/get-video-url-run/{aiMediaId}', [AIVideoController::class, 'getVideoURLRun'])->name('getVideoURLRun');
        Route::post('/merge-image-queue', [AIVideoController::class, 'mergeImageVideoQueue'])->name('mergeImageVideoQueue');
        Route::get('/api/get-history', [AIVideoController::class, 'ApiGetHistory'])->name('ApiGetHistory');
        Route::get('/get-all-video', [AIVideoController::class, 'getAllVideo'])->name('getAllVideo');
        Route::get('/detail/{id}', [AIVideoController::class, 'detail'])->name('detail');
        Route::post('/merge-audio-video-queue', [AIVideoController::class, 'mergeAudioVideoQueue'])->name('mergeAudioVideoQueue');
        Route::get('/history/{id}', [AIVideoController::class, 'historyDetail'])->name('historyDetail');
        Route::post('/delete/{id}', [AIVideoController::class, 'delete'])->name('delete');
        Route::post('/merge-video-audio', [AIVideoController::class, 'mergeVideoAudio'])->name('merge-video-audio');
        Route::post('/convert-ratio-queue', [AIVideoController::class, 'convertRatioQueue'])->name('convertRatioQueue');
        Route::get('/ImgToVideoHistory', [AIVideoController::class, 'ImgToVideoHistory'])->name('ImgToVideoHistory');
        Route::post('/store', [AIVideoController::class, 'store'])->name('store');
        Route::get('/ImgToVideoHistory/{id}', [AIVideoController::class, 'ImgToVideoHistoryDetail'])->name('ImgToVideoHistoryDetail');
        Route::get('/list-Img-To-Video', [AIVideoController::class, 'getListImgToVideo'])->name('getListImgToVideo');
        // Route::post('/create-video-with-transcription', [AIVideoController::class, 'createVideoWithTranscription'])->name('create-video-with-transcription');

        Route::post('/create-video-with-transcription', [AIVideoController::class, 'generateVideoWithTranscriptionWithSegmind'])->name('create-video-with-transcription');
        Route::post('/generate-video-with-transcription-segmind', [AIVideoController::class, 'generateVideoWithTranscriptionWithSegmind'])->name('generate-video-with-transcription-segmind');
        Route::get('/faceswap', [AIVideoController::class, 'faceSwap'])->name('faceSwap');
        Route::post('/generate-face-swap-video', [AIVideoController::class, 'generateSwapFaceVideo'])->name('generate-face-swap-video');
        Route::get('/history-faceswap', [AIVideoController::class, 'historyFaceSwap'])->name('historyFaceSwap');
        Route::post('/delete-faceswap/{id}', [AIVideoController::class, 'deleteFaceswap'])->name('delete-faceswap');
        Route::get('/download-file', [AIVideoController::class, 'downloadFile'])->name('downloadFile');
        Route::get('/suggest-content-video', [AIVideoController::class, 'suggestContentVideo'])->name('suggest-content-video');


        // AI_MOVIE routes - check quyền AI_MOVIE
        Route::middleware('checkFeature:' . FeatureConstants::AI_MERGE_VIDEO)->group(function () {
            Route::get('/history', [AIVideoController::class, 'history'])->name('history');
            Route::post('/merge-video', [AIVideoController::class, 'mergeVideo'])->name('mergeVideo');
        });

        // AI_MOVIE routes - check quyền AI_MOVIE
        Route::middleware('checkFeature:' . FeatureConstants::AI_MOVIE)->group(function () {
            Route::get('/', [AIVideoController::class, 'index'])->name('index');
            Route::post('/generate-AI-video-audio', [AIVideoController::class, 'generateVideoAudio'])->name('generate-video-audio');
        });

        // AI Image routes - check quyền AI_IMAGE
        Route::middleware('checkFeature:' . FeatureConstants::AI_IMAGE_TO_VIDEO)->group(function () {
            Route::get('/img2Video', [AIVideoController::class, 'img2Video'])->name('img2Video');
            Route::post('/generate-img-to-video', [AIVideoController::class, 'generateImg2Video'])->name('generate-img-to-video');
        });

        Route::prefix('url-to-video')->name('url-to-video.')->middleware('checkFeature:' . FeatureConstants::AI_LINK_TO_VIDEO)->group(function () {
            Route::get('/', [AIVideoController::class, 'UrlToVideo'])->name('index');
            Route::get('/create', [AIVideoController::class, 'createUrlToVideo'])->name('create');
            Route::post('/', [AIVideoController::class, 'createVideoPictory'])->name('create-video-pictory');
            Route::get('/edit', [AIVideoController::class, 'editUrlToVideo'])->name('edit');
            Route::post('/update-story-board', [AIVideoController::class, 'updateVideo'])->name('update-story-board');
            Route::post('/create-video', [AIVideoController::class, 'createVideo'])->name('create-video');
            Route::get('/history', [AIVideoController::class, 'historyUrlToVideo'])->name('history');
            Route::post('/delete/{id}', [AIVideoController::class, 'deleteUrlToVideo'])->name('delete');
            Route::post('/translate-content', [AIVideoController::class, 'translateContent'])->name('translate-content');
            Route::post('/merge-audio', [AIVideoController::class, 'queueMergeAudioToVideo'])->name('merge-audio');
            Route::get('/queue-status/{id}', [AIVideoController::class, 'checkQueueStatus'])->name('queue-status');
            Route::get('/suggest-content', [AIVideoController::class, 'suggestContent'])->name('suggest-content');
        });
    });

    Route::prefix('mc-virtual')->name('mc-virtual.')->group(function () {
        // AI Image routes - check quyền AI_VIRTUAL
        Route::middleware('checkFeature:' . FeatureConstants::AI_VIRTUAL)->group(function () {
            Route::get('/', [AIVirtualController::class, 'index'])->name('index');
            Route::get('/ajax-fetch-video/{id}', [AIVirtualController::class, 'ajaxFetchVideo'])->name('ajaxFetchVideo');
            Route::post('/generate-video', [AIVirtualController::class, 'generateVideo'])->name('generate-video');
            Route::post('/generate-clips', [AIVirtualController::class, 'generateClips'])->name('generate-clips');
            Route::post('/generate-clips-heygen', [AIVirtualController::class, 'generateClipsHeyGen'])->name('generate-clips-heygen');
            Route::get('/history', [AIVirtualController::class, 'history'])->name('history');
            Route::get('/history-api', [AIVirtualController::class, 'historyApi'])->name('history-api');
            Route::get('/get-histories', [AIVirtualController::class, 'getHistories'])->name('get-histories');
            Route::post('/apply-real-voice', [AIVirtualController::class, 'mergeVoice'])->name('merge-voice-with-audio');
            Route::post('/delete/{id}', [AIVirtualController::class, 'delete'])->name('delete');
            Route::get('/detail/{id}', [AIVirtualController::class, 'detail'])->name('detail');
            Route::post('/create-video-with-transcription', [AIVirtualController::class, 'generateVideoWithTranscriptionWithSegmind'])->name('create-video-with-transcription');

            Route::get("/history", [AIVirtualController::class, 'historyView'])->name('historyView');
            Route::get('/history/{id}', [AIVirtualController::class, 'historyDetail'])->name('historyDetail');
            Route::get('/get-video-process', [AIVirtualController::class, 'getVideoProcessing'])->name('get-video-process');
        });

        Route::post('/merge-audio-video-queue', [AIVirtualController::class, 'mergeAudioVideoQueue'])->name('mergeAudioVideoQueue');
        Route::get('/suggest-content', [AIVirtualController::class, 'suggestContent'])->name('suggest-content');
        Route::get('/suggest-content-imagevideo', [AIVirtualController::class, 'suggestContentImageAndVideo'])->name('suggest-content-imagevideo');
        Route::get('/suggest-content-image', [AIVirtualController::class, 'suggestContentImage'])->name('suggest-content-image');
        Route::get('/suggest-content-audio', [AIVirtualController::class, 'suggestContentAudio'])->name('suggest-content-audio');
        Route::get('/suggest-content-music', [AIVirtualController::class, 'suggestContentMusic'])->name('suggest-content-music');
        Route::get('/suggest-content-film', [AIVirtualController::class, 'suggestContentFilm'])->name('suggest-content-film');
        Route::get('/suggest-content-film-narration', [AIVirtualController::class, 'suggestNarrativeFilmContent'])->name('suggest-content-film-narration');
        Route::get('/suggest-content-virtual-mc', [AIVirtualController::class, 'suggestVirtualMC'])->name('suggest-content-virtual-mc');
        Route::get('/suggest-content-for-video-creatomate', [AIVirtualController::class, 'suggestContentForCreatomate'])->name('suggest-content-for-creatomate');
        //heygen
        Route::get('/heygen-avatars', [AIVirtualController::class, 'getHeygenAvatars'])->name('heygen-avatars');
        Route::get('/heygen-talking-photos', [AIVirtualController::class, 'getHeygenTalkingPhotos'])->name('heygen-talking-photos');
    });

    Route::prefix('text-to-speech')->name('voice-type.')->group(function () {
        Route::get('/list', [VoiceTypeController::class, 'getUserVoiceTypes'])->name('list');
    });

    Route::get('/image-search-api', [AIVideoController::class, 'searchImg'])->name('image.search');


    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [UserSettingController::class, 'index'])->name('index');
        Route::post('/account/update', [UserSettingController::class, 'updateAccount'])->name('update');
        Route::post('/password/forgot', [UserSettingController::class, 'sendResetLinkEmail']);
        Route::get('/check-package', [UserSettingController::class, 'checkUserPackage'])->name('checkUserPackage');
    });

    Route::name('text-to-video.')->group(function () {
        Route::middleware('checkFeature:' . FeatureConstants::AI_CREATE_VIDEO)->group(function () {
            Route::get('/create-video', [TextToVideoController::class, 'index'])->name('index');
        });
        Route::post('/choose-theme-video', [TextToVideoController::class, 'chooseTheme'])->name('choose-theme-video');
        Route::post('/mapping-video', [TextToVideoController::class, 'mapping'])->name('mapping-with-components-video');
        Route::get('/create-video-stream', [TextToVideoController::class, 'createSlideStream'])->name('createVideoStream');
        Route::post('/create-video-stream-v2', [TextToVideoController::class, 'createSlideStreamV2'])->name('createVideoStreamV2');
        Route::post('/find-images-for-video', [TextToVideoController::class, 'findImages'])->name('find-images-for-video');
        Route::post('/mapping-preview', [TextToVideoController::class, 'previewMapping'])->name('mapping-preview');
        Route::post('/read-file', [TextToVideoController::class, 'readFile'])->name('read-file');

        Route::post('/generate-image', [AIImageController::class, 'generateImage'])->name('generateImage');
        Route::get('/get-histories', [TextToVideoController::class, 'getHistories'])->name('get-histories');
        Route::get('/edit-video/{id}', [TextToVideoController::class, 'editVideo'])->name('edit');
        Route::post('/update-video', [TextToVideoController::class, 'updateVideo'])->name('update-video');
        Route::get('/delete-video/{id}', [TextToVideoController::class, 'deleteVideo'])->name('delete-video');

        Route::post('/generate-mc-by-image', [TextToVideoController::class, 'generateMcByImage'])->name('generate-mc-by-image');
        Route::get('/get-virtual-by-id/{id}', [TextToVideoController::class, 'getVirtualById'])->name('get-virtual-by-id');
        Route::get('/get-list-presenter', [TextToVideoController::class, 'getListPresenters'])->name('get-list-presenter');

        // Route::post('/remove-background', [CreateAIImageController::class, 'removeBackground'])->name('removeBackground');
        Route::post('/remove-background', [TextToVideoController::class, 'removeBackground'])->name('removeBackground');
    });

    Route::prefix('music')->name('text-to-music.')->middleware('checkFeature:' . FeatureConstants::AI_TEXT_TO_AUDIO_MUSIC)->group(function () {
        Route::get('/create-music', [TextToMusicController::class, 'index'])->name('index');
        Route::get('/history', [TextToMusicController::class, 'history'])->name('history');
        Route::post('/delete/{id}', [TextToMusicController::class, 'delete'])->name('delete');
        Route::get('/music/all', [AiTextController::class, 'getAllMusic'])->name('getAllMusic');
    });

    Route::prefix('lipsync')->name('lipsync.')->middleware('checkFeature:' . FeatureConstants::AI_VIDEO_VOICE_OVERLAY)->group(function () {
        Route::post('/get-duration', [AIVideoController::class, 'getTotalDuration'])->name('getDuration');
        Route::get('/', [AIVideoController::class, 'lipsync'])->name('lipsync');
        Route::post('/merge-audio-video-lipsyn-queue', [AIVideoController::class, 'mergeAudioVideoLipsyncQueue'])->name('mergeAudioVideoLipsyncQueue');

        Route::post('/convert-to-mp4', [AIVideoController::class, 'convertFileToMp4'])->name('convertFileToMp4');
        Route::get('/get-file-convert', [AIVideoController::class, 'getFileConvert'])->name('getFileConvert');
        Route::post('/check-status/{id}', [AIVideoController::class, 'checkLipSyncStatus'])->name('check-status');
        Route::post('/delete/{id}', [AIVideoController::class, 'deleteLipsync'])->name('delete');
        Route::get('/detail/{id}', [AIVideoController::class, 'detailLipsync'])->name('detailLipsync');
        Route::post('/generate', [AIVideoController::class, 'generateLipSync'])->name('generate');
        Route::get('/history', [AIVideoController::class, 'historyLipSync'])->name('historyLipSync');
        Route::get('/lipsync/all', [AIVideoController::class, 'getAllLipSyncs'])->name('getAllLipSyncs');
        // Route::get('/music/all', [AiTextController::class, 'getAllMusic'])->name('getAllMusic');

    });

    // Route text to song
    Route::prefix('song')->name('text-to-song.')->middleware('checkFeature:' . FeatureConstants::AI_LYRIC_TO_MUSIC)->group(function () {
        Route::get('/create-music', [TextToSongController::class, 'index'])->name('index');
        Route::get('/history', [TextToSongController::class, 'history'])->name('history');
        Route::get('/song/all', [TextToSongController::class, 'getAllMusic'])->name('getAllMusic');
        Route::post('/store', [TextToSongController::class, 'store'])->name('store');
        Route::post('/topmediai-song', [TextToSongController::class, 'generateSong'])->name('song');
        Route::post('/summaryPromt', [TextToSongController::class, 'summaryPrompt'])->name('summary');
        Route::get('/suggest-content-music', [TextToSongController::class, 'suggestContentMusic'])->name('suggest-content-music');
        Route::post('/generate-song', [TextToSongController::class, 'generateSunoSong'])->name('generate-song');
        Route::post('/convert-audio-to-video', [TextToSongController::class, 'convertAudioToVideo'])->name('convert-audio-to-video');
    });

    Route::prefix('ai-personal')->name('ai-personal.')->middleware('checkFeature:' . FeatureConstants::AI_PERSONAL)->group(function () {
        Route::get('/', [AIPersonalController::class, 'index'])->name('index');
        Route::get('/get-list-media', [AIPersonalController::class, 'getListMedia'])->name('get-list-media');
    });

    Route::get('/youtube/login', [YoutubeController::class, 'login']);
    Route::get('/youtube/upload', [YoutubeController::class, 'store']);

    Route::prefix('business')->name('ai-business.')->group(function () {
        Route::get('/', [AIDifyController::class, 'index'])->name('index');
        Route::get('/music-for-business', [AIBusinessProjectController::class, 'musicForBusiness'])->name('music-for-business');
        Route::post('/send-chat', [AIDifyController::class, 'sendChat'])->name('sendChat');
        Route::post('/send-chat-streaming', [AIDifyController::class, 'sendChatStreaming'])->name('sendChatStreaming');
        Route::post('/create-project', [AIBusinessProjectController::class, 'createProject'])->name('create-project');
        Route::post('/update-project', [AIBusinessProjectController::class, 'updateProject'])->name('update-project');
        Route::get('/get-list-project', [AIBusinessProjectController::class, 'getListProject'])->name('get-list-project');
        Route::get('/get-project-by-id', [AIBusinessProjectController::class, 'getProjectById'])->name('get-project-by-id');
        Route::post('ai-business-resolves', [AiBusinessResolveController::class, 'store'])->name('save-result');
        Route::post('/upload-document', [AIBusinessProjectController::class, 'uploadDocument'])->name('upload-document');
        Route::post('/calculate', [AIBusinessProjectController::class, 'calculateStreamCredit'])
            ->name('credit.calculate-stream');
        Route::delete('/projects/{id}', [AIBusinessProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('/grade-content', [AIBusinessProjectController::class, 'gradeContent'])->name('grade-content');
        Route::post('/summarize-content', [AIBusinessProjectController::class, 'summarizeContent'])->name('summarize-content');
        Route::post('/clean-content', [AIBusinessProjectController::class, 'cleanContent'])->name('clean-content');
        Route::post('/improve-content', [AIBusinessProjectController::class, 'improveContent'])->name('improve-content');
        Route::get('/trial-count', [AIBusinessProjectController::class, 'trialCount'])->name('trial-count');
        Route::post('/send-ai-business-stream', [AIBusinessProjectController::class, 'handleAIBusinessStream'])->name('send-ai-business-stream');
        Route::get('/image-search-api', [SearchImageController::class, 'index'])->name('search-image');
        Route::post('/facebook-content/delete', [FacebookContentController::class, 'deleteFacebookContent'])->name('delete-facebook-content');
        Route::get('/business-history', [AIBusinessProjectController::class, 'businessHistory'])->name('business-history');
        Route::get('/get-list-project-history', [AIBusinessProjectController::class, 'getListProjectHistories'])->name('get-list-project-history');

        Route::post('/product-image/upload', [ProductImageController::class, 'upload'])->name('product-image-upload');
        Route::get('/product-image/list', [ProductImageController::class, 'apiHistory'])->name('product-image-list');

        Route::post('/combine-images', [ImageCombineController::class, 'combineImages'])->name('combine-images');
        Route::post('/generate-image-background', [ImageCombineController::class, 'generateImageBackground'])->name('generate-image-background');
        Route::post('/generate-image-background-with-file', [ImageCombineController::class, 'generateImageBackgroundWithFile'])->name('generate-image-background-with-file');
        Route::post('/upscale-image', [ImageCombineController::class, 'upscaleImage'])->name('upscale-image');
        Route::post('/generate-prompt-image', [ImageCombineController::class, 'generatePromptImage'])->name('generate-prompt-image');
        Route::post('/generate-prompt-image-with-file', [ImageCombineController::class, 'generatePromptImageWithFile'])->name('generate-prompt-image-with-file');

        // mode chuyên sâu
        Route::post('/create-project-expert', [AIProjectExpertController::class, 'createProject'])->name('create-project-expert');
        Route::post('/update-project-expert', [AIProjectExpertController::class, 'updateProject'])->name('update-project-expert');
        Route::get('/get-list-project-expert', [AIProjectExpertController::class, 'getListProject'])->name('get-list-project-expert');
        Route::post('/extract-tags-from-content', [AIProjectExpertController::class, 'extractTagsFromContent'])->name('extractTagsFromContent');
        Route::post('/send-chat-streaming-expert', [AIDifyController::class, 'sendChatStreamingExpert'])->name('sendChatStreamingExpert');
        Route::get('project-expert/detail/{id}', [AIProjectExpertController::class, 'getDetail'])->name('project-expert-detail');
        Route::delete('/projects-expert/{id}', [AIProjectExpertController::class, 'destroy'])->name('projects-expert.destroy');
        Route::get('/business-history', [AIBusinessProjectController::class, 'businessHistory'])->name('business-history');
        Route::get('/get-list-project-history', [AIBusinessProjectController::class, 'getListProjectHistories'])->name('get-list-project-history');

        Route::post('/combine-images', [ImageCombineController::class, 'combineImages'])->name('combine-images');
        Route::post('/generate-prompt-image', [ImageCombineController::class, 'generatePromptImage'])->name('generate-prompt-image');
        Route::post('/create-image', [ImageCombineController::class, 'createImage'])->name('create-image');
        Route::post('/create-image-by-replicate', [ImageCombineController::class, 'createImageReplicate'])->name('create-image-by-replicate');
        Route::post('/create-image-background-by-replicate', [ImageCombineController::class, 'crateImageBackgroundWithReplica'])->name('create-image-background-by-replicate');
    });

    Route::prefix('video-to-text')->name('ai-video-to-text.')->group(function () {
        Route::post('/send-chat-streaming', [VideoToTextController::class, 'sendChatStreaming'])->name('sendChatStreaming');
        Route::post('/translate-content', [VideoToTextController::class, 'translateContent'])->name('translate-content');
        Route::post('/translate-content-stream', [VideoToTextController::class, 'translateContentStream'])->name('translate-content-stream');
        Route::post('/export-to-doc', [VideoToTextController::class, 'exportToDoc'])->name('export-to-doc');
        Route::post('/save-history', [VideoToTextController::class, 'saveHistory'])->name('save-history');
        Route::get('/history-list', [VideoToTextController::class, 'getListHistory'])->name('history-list');
        Route::get('/delete/{id}', [VideoToTextController::class, 'delete'])->name('delete');
        Route::post('/convert-mp3', [VideoToTextController::class, 'convertMp3'])->name('convert-mp3');
        Route::get('/download-mp3', [VideoToTextController::class, 'downloadMp3'])->name('download-mp3');
    });
    Route::post('/than-so-hoc', [ThanSoHocController::class, 'getThanSoHocInfo'])->name('than-so-hoc');
    Route::post('/than-so-hoc/chat', [ThanSoHocController::class, 'chat'])->name('than-so-hoc-chat');
    Route::post('/send-promotion-message/{user_page_id}', [MessengerController::class, 'sendPromotionMessage'])
    ->name('api.send-promotion-message');

    Route::post('/get-promotion-message/{user_page_id}', [MessengerController::class, 'getPromotionMessage'])
        ->name('api.get-promotion-message');

        Route::post('/facebook-fanpage/save-config', [MessengerController::class, 'saveFacebookConfig'])
        ->name('facebook-fanpage.save-config');
    Route::get('/facebook-fanpage/get-config', [MessengerController::class, 'getFacebookConfig'])
        ->name('facebook-fanpage.get-config');

    // find customer
    Route::prefix('google-search')->name('google-search.')->group(function () {
        Route::get('/', [GoogleSearchController::class, 'index'])->name('index');
        Route::get('/google-search/search-key', [GoogleSearchController::class, 'searchKey'])->name('searchKey');
        Route::get('/google-search/search-key/note', [GoogleSearchController::class, 'note'])->name('note');
        Route::get('/get-data', [GoogleSearchController::class, 'search'])->name('getData');
    });

    Route::prefix('chat-training-documents')->name('chat-training-documents.')->group(function () {
        Route::get('/agent', [ChatTrainingDocumentController::class, 'settingAgent'])->name('setting-agent');
        Route::post('/save-chat-config', [ChatTrainingDocumentController::class, 'saveChatConfig'])->name('save-chat-config');
        Route::post('/upload-document', [ChatTrainingDocumentController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/chat-training-documents', [ChatTrainingDocumentController::class, 'chatTrainingDocuments'])->name('chat-training-documents');
        Route::post('/delete-document', [ChatTrainingDocumentController::class, 'deleteDocument'])->name('delete-document');
        Route::get('/documents/download/{id}', [ChatTrainingDocumentController::class, 'downloadDocument'])->name('download-document');
        Route::post('/generate-chat-box-iframe', [ShareLinkController::class, 'generateChatBoxIframeUrl'])->name('generate-chat-box-iframe');
    });

    Route::prefix('embed-config')->name('embed-config.')->group(function () {
        Route::get('/fetchData', [FacebookConfigController::class, 'fetchData'])->name('fetch-data');
        Route::get('/facebook', [FacebookConfigController::class, 'index'])->name('facebook.index');
        Route::post('/facebook/update/{id}', [FacebookConfigController::class, 'update'])->name('facebook.update');
        Route::post('/facebook/delete', [FacebookConfigController::class, 'delete'])->name('facebook.delete');
        Route::post('/facebook/store', [FacebookConfigController::class, 'store'])->name('facebook.store');
    });

    Route::prefix('ai-tu-te')->name('ai-tu-te.')->group(function () {
    });

    Route::middleware(['auth:web'])->prefix('management')->name('management.')->group(function () {
        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/', [ManagementController::class, 'index'])->name('index');
            Route::get('/{id}', [ManagementController::class, 'detail'])->name('detail');
            Route::post('/update-user-bank', [ManagementController::class, 'updateUserBank'])->name('updateUserBank');
            Route::post('/update-user-sale', [ManagementController::class, 'updateUserSale'])->name('updateUserSale');
            Route::post('/delete/{id}', [ManagementController::class, 'delete'])->name('delete');
            Route::post('/{id}', [ManagementController::class, 'update'])->name('update');
            Route::get('/message-histories/{conversationId}', [ManagementController::class, 'messageHistories'])->name('messageHistories');
        });
    });

    // Facebook Authentication Routes
    Route::get('/auth/facebook/redirect', [FacebookConfigController::class, 'redirectToFacebook'])->name('facebook.auth.redirect');
    Route::get('/auth/facebook/callback', [FacebookConfigController::class, 'handleFacebookCallback'])->name('facebook.callback');
    Route::get('/support', [RulesController::class, 'support'])->name('rules.support');
});

Route::prefix('ai-image')->name('ai-image.')->group(function () {
    Route::get('/get-my-ai-image-template', [AIImageController::class, 'getMyAIImageTemplate'])->name('get-my-ai-image-template');
    Route::get('/get-list-collections', [MyAICollectionController::class, 'getListCollection'])->name('get-list-collections');
    Route::get('/get-list-template-by-category-id', [MyAICollectionController::class, 'getListTemplateByCategory'])->name('get-list-template-by-category');
});

Route::get('/product-image/history', [ProductImageController::class, 'history'])->name('product-image-history');
Route::post('/product-image/delete/{id}', [ProductImageController::class, 'deleteImage'])->name('product-image-delete');
Route::get('/youtube/callback', [YoutubeController::class, 'loginCallback']);
Route::get('/youtube/list-token', [YoutubeController::class, 'getListAccessToken']);

Route::middleware([AffMiddleware::class])->group(function () {
    Route::prefix('pricing')->name('pricing.')->group(function () {
        Route::get('/', [PricingController::class, 'index'])->name('index');
    });
});

Route::middleware(['auth:web', AffMiddleware::class])->prefix('pricing')->name('pricing.')->group(function () {
    Route::post('/alepay', [PricingController::class, 'alepay'])->name('alepay');
    Route::get('/alepay-success', [PricingController::class, 'alepaySuccess'])->name('alepay.success');
    Route::get('/alepay-cancel', [PricingController::class, 'alepayCancel'])->name('alepay.cancel');
    Route::get('/payment-finish', [PricingController::class, 'paymmentFinish'])->name('alepay.finish');
});

Route::middleware(['auth:web'])->prefix('creatomate')->name('creatomate.')->middleware('checkFeature:' . FeatureConstants::AI_VIDEO)->group(function () {
    Route::get('/template', [CreatomateController::class, 'index'])->name('index');
    Route::get('/filter', [CreatomateController::class, 'getTemplates'])->name('getTemplates');
    Route::get('/create', [CreatomateController::class, 'create'])->name('create');
    Route::post('/create-content', [CreatomateController::class, 'creatomateCreateContent'])->name('create-content');
    Route::post('/create-video', [CreatomateController::class, 'createVideo'])->name('create-video');
    Route::get('/history', [CreatomateController::class, 'history'])->name('history');
    Route::post('/delete/{id}', [CreatomateController::class, 'delete'])->name('delete');
    Route::get('/edit-video/{id}', [CreatomateController::class, 'edit'])->name('edit');
});

Route::middleware(['auth:web'])->prefix('share-link')->name('share-link.')->group(function () {
    //share-link.store
    Route::post('/', [ShareLinkController::class, 'store'])->middleware('auth')->name('store');
    Route::get('/{token}', [ShareLinkController::class, 'show'])->name('show');
});

Route::prefix('slide')->name('slide.')->group(function () {
    Route::post('/upload-image', [ImageController::class, 'uploadImage'])->name('uploadImage');
});

Route::prefix('my-assistant')->name('my-assistant.')->group(function () {
    Route::get('/', [MyAssistantsController::class, 'index'])->name('index');
});

Route::get('/notification', [NotificationController::class, 'index'])->name('notification.index');

Route::get('/url-posts', [PostController::class, 'show'])->name('posts.show');

Route::get('/password/reset/{token}', [UserSettingController::class, 'showResetForm'])->name('settings.password.reset');
Route::post('/password/reset', [UserSettingController::class, 'reset'])->name('password.reset.change');
Route::get('/error/permission', [UserSettingController::class, 'showPermissionForm'])->name('error.permission');
Route::post('/activate-account', [UserSettingController::class, 'activateAccount'])->name('account.activate');
Route::get('/activate', [UserSettingController::class, 'activateAccountIndex'])->name('account.activate-index');
Route::get('/agent-keys-statistics/{code}', [KeyStatisticsController::class, 'getAgentKeyStatistics']);
Route::post('/transfer-keys', [KeyStatisticsController::class, 'transferKeys']);
Route::get('/sync-agents', [KeyStatisticsController::class, 'syncAgents']);
Route::post('/sync-store-key', [KeyStatisticsController::class, 'syncStoreKey']);

Route::prefix('customer-care')->name('customer-care.')->middleware('checkFeature:' . FeatureConstants::AI_CUSTOMER_CARE)->group(function () {
    Route::get('/', [CustomerCareController::class, 'index'])->name('index');
});

Route::post('/get-available-keys', [KeyStatisticsController::class, 'getAvailableKeys']);

Route::get('/proxy-image', function (Illuminate\Http\Request $request) {
    $url = $request->query('url');

    try {
        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0',
            ],
        ]);

        $res = $client->get($url);
        return response($res->getBody(), 200)
            ->header('Content-Type', $res->getHeaderLine('Content-Type'));
    } catch (\Exception $e) {
        return response("Image proxy failed", 400);
    }
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
