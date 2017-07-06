<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Marin\SteamAuth\Models\SteamAuth;
use Marin\SteamAuth\Models\SteamConfig;

Route::get('/marin/steamauth/login', function(\Illuminate\Http\Request $request) {

    $auth = new SteamAuth($request);

    return Redirect::to($auth->getAuthUrl());

});

Route::get('/marin/steamauth/response', function(\Illuminate\Http\Request $request) {

    $auth = new SteamAuth($request);
    $redirectUrl = SteamConfig::get('redirect');

    if ($auth->validate()) {
        return Redirect::to($redirectUrl)->with('steamuser', $auth->getUserInfo());
    }

    return Redirect::to($redirectUrl)->with('steamerror', $auth->getError());

});