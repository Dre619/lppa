<?php

use App\Http\Controllers\PrintSchedulesController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


    Volt::route('/', 'auth.login')->name('home');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::post('print/schedule',[PrintSchedulesController::class,'index'])->name('print.schedule');
Route::post('print/notices',[PrintSchedulesController::class,'print_notices'])->name('print.notices');
Route::post('print/schedule-mpdf',[PrintSchedulesController::class,'printSchedule'])->name('print.schedule.mpdf');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('admin/users', 'admin.users')->name('admin.users');
    Volt::route('admin/roles', 'admin.roles')->name('admin.roles');
    Volt::route('admin/districts', 'admin.districts')->name('admin.districts');
    Volt::route('admin/registration-areas', 'admin.registration-areas')->name('admin.registration-areas');
    Volt::route('admin/development-areas', 'admin.development-areas')->name('admin.development-areas');
    Volt::route('admin/resolutions', 'admin.resolutions')->name('admin.resolutions');
    Volt::route('admin/application-classes', 'admin.application-class')->name('admin.application-classes');
    Volt::route('admin/registration-types', 'admin.registration-type')->name('admin.registration-types');
    Volt::route('admin/sub-areas', 'admin.sub-areas')->name('admin.sub-areas');
    Volt::route('admin/titles', 'admin.titles')->name('admin.titles');
    Volt::route('admin/construction-stages', 'admin.construction-stages')->name('admin.construction-stages');
    Volt::route('admin/property-types', 'admin.property-types')->name('admin.property-types');
    Volt::route('admin/change-use-stages', 'admin.change-use-stages')->name('admin.change-use-stages');
    Volt::route('admin/land-uses', 'admin.land-use')->name('admin.land-uses');
    Volt::route('admin/executive-signatures', 'admin.executive-signature')->name('admin.executive-signatures');
    Volt::route('admin/registration-organizations', 'admin.registration-organization')->name('admin.registration-organizations');
    Volt::route('admin/applications/{registrationTypeId}', 'admin.applications')
        ->name('admin.applications');
    Volt::route('admin/schedules/{registrationTypeId}', 'admin.schedules')
    ->name('admin.schedules');
    Volt::route('admin/stoporders','admin.building-controls')->name('admin.building-controls');
});

require __DIR__.'/auth.php';
