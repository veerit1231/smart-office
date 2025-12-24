<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentWorkflowController;
use App\Http\Controllers\DocumentAttachmentController;
use App\Http\Controllers\ExternalDocumentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('documents.index');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Protected Routes (login แล้ว)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 📄 Documents (Internal / Outgoing)
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', fn () => redirect()->route('documents.index'));

    Route::get('/documents/index', [DocumentController::class, 'index'])
        ->name('documents.index');

    Route::get('/documents/create', [DocumentController::class, 'create'])
    ->name('documents.create');

    Route::post('/documents', [DocumentController::class, 'store'])
        ->name('documents.store');

    Route::get('/documents/{document}', [DocumentController::class, 'show'])
        ->name('documents.show');

    /*
    |--------------------------------------------------------------------------
    | 📎 Attachments
    |--------------------------------------------------------------------------
    */
    Route::post('/documents/{document}/attachments', [
        DocumentAttachmentController::class,
        'store',
    ])->name('documents.attachments.store');

    /*
    |--------------------------------------------------------------------------
    | 🔄 Workflow Actions
    |--------------------------------------------------------------------------
    */
    Route::post('/documents/{document}/submit-user', [
        DocumentWorkflowController::class,
        'submitFromUser',
    ])->name('documents.submit-user');

    Route::post('/documents/{document}/submit', [
        DocumentWorkflowController::class,
        'submit',
    ])->name('documents.submit');

    Route::post('/documents/{document}/approve', [
        DocumentWorkflowController::class,
        'approve',
    ])->name('documents.approve');

    Route::post('/documents/{document}/reject', [
        DocumentWorkflowController::class,
        'reject',
    ])->name('documents.reject');

    Route::post('/documents/{document}/distribute', [
        DocumentWorkflowController::class,
        'distribute',
    ])->name('documents.distribute');

    Route::post('/documents/{document}/cancel',
    [DocumentWorkflowController::class, 'cancel']
)->name('documents.cancel');

    /*
    |--------------------------------------------------------------------------
    | 📥 Incoming / External Documents (CLERK เท่านั้น)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:clerk')->group(function () {

        // หน้า index รับนอก
        Route::get('/documents/incoming', [
            ExternalDocumentController::class,
            'index',
        ])->name('documents.incoming.index');

        // เตรียมข้อมูล (departments) สำหรับ modal / form
        Route::get('/documents/incoming/create', [
            ExternalDocumentController::class,
            'create',
        ])->name('documents.incoming.create');

        // บันทึกรับเอกสารภายนอก
        Route::post('/documents/incoming', [
            ExternalDocumentController::class,
            'store',
        ])->name('documents.incoming.store');

    });

            /*
        |--------------------------------------------------------------------------
        | 🏢 Department (master data สำหรับ incoming)
        |--------------------------------------------------------------------------
        */
    Route::resource('departments', DepartmentController::class)
    ->except(['show', 'create', 'edit']);
    /*
    |--------------------------------------------------------------------------
    | 👤 Admin : User Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::patch('/users/{user}/toggle-status', [
            UserController::class,
            'toggleStatus',
        ])->name('users.toggle-status');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');
    });

    /*
    |--------------------------------------------------------------------------
    | 🔔 Notifications
    |--------------------------------------------------------------------------
    */
    Route::post('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();
        return back();
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/documents', [ReportController::class, 'documents'])
        ->name('reports.documents');
});

