<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PitchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PitchController as AdminPitchController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\SettingController;

// Public
Route::get('/', [PitchController::class, 'index'])->name('home');
Route::get('/pitches/{pitch}', [PitchController::class, 'show'])->name('pitches.show');
Route::get('/pitches/{pitch}/monthly', [PitchController::class, 'monthly'])->name('pitches.monthly');
Route::get('/map', [PitchController::class, 'map'])->name('map');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');


// User (auth)
Route::middleware('auth')->group(function () {
    Route::get('/bookings/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/history', [BookingController::class, 'history'])->name('bookings.history');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/payments/qr', [PaymentController::class, 'qr'])->name('payments.qr');
    Route::get('/payments/result', [PaymentController::class, 'result'])->name('payments.result');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/renew/{monthlyBooking}', [BookingController::class, 'renew'])->name('bookings.renew');
    Route::post('/renew/{monthlyBooking}', [BookingController::class, 'renewStore'])->name('bookings.renew.store');
});

// Admin
Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/pitches', AdminPitchController::class);
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::patch('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::patch('/contracts/{lock}/release', [ContractController::class, 'releaseLock'])->name('contracts.release');
    Route::patch('/contracts/{lock}/relock', [ContractController::class, 'relockLock'])->name('contracts.relock');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-lock', [UserController::class, 'toggleLock'])->name('users.toggleLock');
    Route::get('/timeslots', [TimeSlotController::class, 'index'])->name('timeslots.index');
    Route::post('/timeslots/toggle', [TimeSlotController::class, 'toggle'])->name('timeslots.toggle');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

