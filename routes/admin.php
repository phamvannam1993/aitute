<?php

use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AIAssistantController;
use App\Http\Controllers\Admin\OperationController;
use App\Http\Controllers\Client\SpeechToTextController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\OccupationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminAiTextController;
use App\Http\Controllers\Admin\AdminMyAiImageCollectionController;
use App\Http\Controllers\Admin\AdminMyAiImageTemplateCategoryController;
use App\Http\Controllers\Admin\AdminMyAiImageTemplateController;
use App\Http\Controllers\Admin\KeyActiveController;

Route::prefix('admin')->middleware(['auth:admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminHomeController::class, 'index'])->name('index');
    Route::get('/job', action: [OccupationController::class, 'index'])->name('job.index');
    Route::get('/job-create', action: [OccupationController::class, 'create'])->name('job.create');
    Route::post('/job', [OccupationController::class, 'store'])->name('job.store');
    Route::get('/job/{id}/edit', [OccupationController::class, 'edit'])->name('job.edit');
    Route::post('/job/{id}/update', [OccupationController::class, 'update'])->name('job.update');
    Route::get('/job/{id}/listOperationsAndAis', [OccupationController::class, 'listOperationsAndAis'])->name('job.listOperationsAndAis');
    Route::delete('/job/{id}', [OccupationController::class, 'destroy'])->name('job.destroy');
    Route::get('/load-job-occupations', action: [OccupationController::class, 'getOccupations'])->name('job.load');

    Route::get('/operation', action: [OperationController::class, 'index'])->name('operation.index');
    Route::get('/operation-create', action: [OperationController::class, 'create'])->name('operation.create');
    Route::post('/operation', [OperationController::class, 'store'])->name('operation.store');
    Route::get('/operation/{id}/edit', [OperationController::class, 'edit'])->name('operation.edit');
    Route::post('/operation/{id}/update', [OperationController::class, 'update'])->name('operation.update');
    Route::get('/operation/{id}/listAis', [OperationController::class, 'listAis'])->name('operation.listAis');
    Route::delete('/operation/{id}', [OperationController::class, 'destroy'])->name('operation.destroy');
    Route::get('/load-job-operations', action: [OperationController::class, 'getOperations'])->name('operation.load');


    Route::get('/ai-assistants', action: [AIAssistantController::class, 'index'])->name('ai-assistants.index');
    Route::post('/ai-assistants', [AIAssistantController::class, 'store'])->name('ai-assistants.store');
    Route::get('/ai-assistants-create', action: [AIAssistantController::class, 'create'])->name('ai-assistants.create');
    Route::get('/ai-assistants/{id}/edit', [AIAssistantController::class, 'edit'])->name('ai-assistants.edit');
    Route::delete('/ai_assistants/{id}', [AiAssistantController::class, 'destroy'])->name('ai_assistants.destroy');
    Route::post('/ai-assistants/{id}/update', [AIAssistantController::class, 'update'])->name('ai-assistants.update');

    Route::get('/ai-assistants/{id}/run', [AIAssistantController::class, 'run'])->name('ai-assistants.run');
    Route::post('admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::post('/generate-step', [AIAssistantController::class, 'generateStep'])->name('generateStep');

    Route::prefix('ai-text')->name('ai-text.')->group(function () {
        Route::post('/docs', [AdminAiTextController::class, 'handleConversation'])->name('docs');
        Route::get('/docs', [AdminAiTextController::class, 'getConversation'])->name('docs');
        Route::post('/send-ask-stream-claude', [AdminAiTextController::class, 'handleAskClaudeStream'])->name('askClaudeStream');
        Route::post('/send-ask-stream-gpt', [AdminAiTextController::class, 'handleAskGptStream'])->name('askGptStream');
        Route::post('/send-ask-multi-stream-claude', [AdminAiTextController::class, 'handleMultiStepProcess'])->name('askMultiClaudeStream');
        Route::post('/send-ask-multi-stream-gpt', [AdminAiTextController::class, 'handleMultiStepProcessGpt'])->name('askMultiGptStream');
    });
    Route::post('/speech-to-text', [SpeechToTextController::class, 'callGoogleSpeechToText']);

    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
    Route::resource('users', AdminUserController::class);
    Route::post('/users/add-credit', [AdminUserController::class, 'addCredit'])->name('users.add-credit');
    Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/errors/403', function () {
        return Inertia::render('Admin/Errors/403');
    })->name('errors.403');

    Route::prefix('keys')->name('keys.')->group(function () {
        Route::get('/', [KeyActiveController::class, 'index'])->name('index');
        Route::get('/create', [KeyActiveController::class, 'create'])->name('create');
        Route::post('/store', [KeyActiveController::class, 'store'])->name('store');
        Route::delete('/{key_id}', [KeyActiveController::class, 'destroy'])->name('destroy');
        Route::get('/{key_id}', [KeyActiveController::class, 'edit'])->name('edit');
        Route::post('/keys/{key_id}/update', [KeyActiveController::class, 'update'])->name('update');
        Route::get('admin/keys/export', [KeyActiveController::class, 'exportCsv'])->name('export');
    });
    Route::post('/my-ai-image-collections/update/{id}', [AdminMyAiImageCollectionController::class, 'update'])->name('my-ai-image-collections.update');
    Route::resource('my-ai-image-collections', AdminMyAiImageCollectionController::class)->except(['update']);
    Route::post('/my-ai-image-template-categories/update/{id}', [AdminMyAiImageTemplateCategoryController::class, 'update'])->name('my-ai-image-template-categories.update');
    Route::resource('my-ai-image-template-categories', AdminMyAiImageTemplateCategoryController::class)->except(['update']);
    Route::delete('/my-ai-image-templates/{id}', [AdminMyAiImageTemplateController::class, 'destroy'])->name('my-ai-image-templates.destroy');
});
