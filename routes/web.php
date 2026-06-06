<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SavedSearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ExternalOrderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\CrawlerController as AdminCrawler;
use App\Http\Controllers\Admin\SourceController as AdminSource;
use App\Http\Controllers\Admin\CategoryController as AdminCategory;
use App\Http\Controllers\Admin\SkillController as AdminSkill;
use App\Http\Controllers\Moderator\OrderController as ModeratorOrder;
use App\Http\Controllers\Moderator\ComplaintController as ModeratorComplaint;
use App\Http\Controllers\Moderator\DashboardController as ModeratorDashboard;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ───────────────────────────────────────────────────────────────────

Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/', [OrderController::class, 'index'])->name('home');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->where('order', '[0-9]+');
Route::get('/external-orders', [ExternalOrderController::class, 'index'])->name('external.index');
Route::get('/external-orders/{externalOrder}', [ExternalOrderController::class, 'show'])->name('external.show')->where('externalOrder', '[0-9]+');

// ─── AUTH (Breeze) ────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── AUTHENTICATED ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'blocked'])->group(function () {

    Route::get('/dashboard', fn() => redirect()->route('orders.index'))->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // ── FREELANCER ─────────────────────────────────────────────────────────────
    Route::middleware('role:freelancer')->group(function () {

        // Profile
        Route::get('/profile/freelancer', [ProfileController::class, 'editFreelancer'])->name('profile.freelancer.edit');
        Route::put('/profile/freelancer', [ProfileController::class, 'updateFreelancer'])->name('profile.freelancer.update');

        // Applications
        Route::post('/orders/{order}/apply', [ApplicationController::class, 'store'])->name('applications.store');
        Route::delete('/applications/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('applications.withdraw');
        Route::get('/my-applications', [ApplicationController::class, 'myApplications'])->name('applications.my');

        // Saved searches
        Route::resource('saved-searches', SavedSearchController::class);
    });

    // ── CLIENT ─────────────────────────────────────────────────────────────────
    Route::middleware('role:client')->group(function () {

        // Profile
        Route::get('/profile/client', [ProfileController::class, 'editClient'])->name('profile.client.edit');
        Route::put('/profile/client', [ProfileController::class, 'updateClient'])->name('profile.client.update');

        // Orders CRUD
        Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::patch('/orders/{order}/submit', [OrderController::class, 'submitForModeration'])->name('orders.submit');
        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Applications management
        Route::get('/orders/{order}/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::patch('/applications/{application}/accept', [ApplicationController::class, 'accept'])->name('applications.accept');
        Route::patch('/applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
    });

    // ── SHARED: Messages (client + freelancer on active order) ────────────────
    Route::middleware('role:freelancer,client')->group(function () {
        Route::get('/orders/{order}/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('/orders/{order}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::patch('/orders/{order}/work/status', [OrderController::class, 'updateWorkStatus'])->name('orders.work.status');
    });

    // Reviews & Complaints (any auth user)
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');

    // ── MODERATOR ──────────────────────────────────────────────────────────────
    Route::middleware('role:moderator,admin')->prefix('moderator')->name('moderator.')->group(function () {
        Route::get('/dashboard', [ModeratorDashboard::class, 'index'])->name('dashboard');
        Route::get('/orders', [ModeratorOrder::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [ModeratorOrder::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/approve', [ModeratorOrder::class, 'approve'])->name('orders.approve');
        Route::patch('/orders/{order}/reject', [ModeratorOrder::class, 'reject'])->name('orders.reject');
        Route::patch('/orders/{order}/revise', [ModeratorOrder::class, 'revise'])->name('orders.revise');
        Route::get('/complaints', [ModeratorComplaint::class, 'index'])->name('complaints.index');
        Route::patch('/complaints/{complaint}/resolve', [ModeratorComplaint::class, 'resolve'])->name('complaints.resolve');
        Route::patch('/complaints/{complaint}/dismiss', [ModeratorComplaint::class, 'dismiss'])->name('complaints.dismiss');
    });

    // ── ADMIN ──────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUser::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/role', [AdminUser::class, 'updateRole'])->name('users.role');
        Route::patch('/users/{user}/block', [AdminUser::class, 'block'])->name('users.block');
        Route::patch('/users/{user}/unblock', [AdminUser::class, 'unblock'])->name('users.unblock');

        // Categories & Skills
        Route::resource('categories', AdminCategory::class);
        Route::resource('skills', AdminSkill::class);

        // Crawler sources
        Route::resource('sources', AdminSource::class);
        Route::post('/crawler/run/{source}', [AdminCrawler::class, 'run'])->name('crawler.run');
        Route::post('/crawler/run-all', [AdminCrawler::class, 'runAll'])->name('crawler.runAll');
        Route::get('/crawler/logs', [AdminCrawler::class, 'logs'])->name('crawler.logs');

        // Reports
        Route::get('/reports', [AdminDashboard::class, 'reports'])->name('reports');
        Route::get('/reports/export', [AdminDashboard::class, 'export'])->name('reports.export');
    });
});