<?php

use Illuminate\Support\Facades\Route;
// use Modules\UserAvatar\Http\Controllers\UserAvatarController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::resource('useravatar', UserAvatarController::class)->names('useravatar');
});
