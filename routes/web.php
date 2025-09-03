<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-mail', function () {
    Mail::raw('Testing ZeptoMail SMTP ', function ($message) {
        $message->to('admin@gmail.com')
            ->subject('ZeptoMail Test');
    });

    return 'Mail Sent!';
});
