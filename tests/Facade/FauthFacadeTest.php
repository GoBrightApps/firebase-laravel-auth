<?php

declare(strict_types=1);

use Bright\Fauth\Facades\Fauth;
use Bright\Fauth\FauthFake;

beforeEach(function () {
    $this->app->singleton('fauth', fn () => new FauthFake);
});

it('delegates calls to underlying fake', function () {
    $user = Fauth::create(['email' => 'f@example.com']);
    expect($user->email)->toBe('f@example.com');

    $found = Fauth::find($user->uid);
    expect($found->uid)->toBe($user->uid);

    app('fauth')->assertCalled('create');
    app('fauth')->assertCalled('find');
});
