<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    // Demo auth shortcut: ensure a dispatcher user exists and hand the SPA a
    // fresh Sanctum token. This stands in for a real login screen, which is
    // intentionally out of scope (see README "Auth").
    $user = User::firstOrCreate(
        ['email' => 'dispatcher@winch.test'],
        ['name' => 'WINCH Dispatcher', 'password' => bcrypt(Str::random(40))],
    );

    $user->tokens()->where('name', 'web-demo')->delete();
    $apiToken = $user->createToken('web-demo')->plainTextToken;

    return view('app', ['apiToken' => $apiToken]);
});
