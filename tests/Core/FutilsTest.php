<?php

declare(strict_types=1);

use Bright\Fauth\Futils;

it('returns mapped message', function () {
    expect(Futils::message('USER_DISABLED'))->toContain('The user account has been disabled.');
});

it('returns default for unknown code', function () {
    $msg = Futils::message('auth/unknown', 'PHP is ');
    expect($msg)->toBe('PHP is ');
});
