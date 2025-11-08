<?php

declare(strict_types=1);

use Bright\Fauth\Facades\Fauth;
use Bright\Fauth\FauthFake;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kreait\Firebase\Auth\UserRecord;

beforeEach(function () {
    Fauth::fake();
});

it('can find a user by uid', function () {

    $uid = 'user_123';

    $user = FauthFake::userRecord($uid);

    Fauth::shouldReceive('find')->once()
        ->with($uid)
        ->andReturn($user);

    expect(Fauth::find($uid))->toBeInstanceOf(UserRecord::class)
        ->uid->toBe($uid);
});

it('can find a user by email', function () {
    $email = 'test@example.com';
    $user = FauthFake::userRecord('findByEmail', ['email' => $email]);

    Fauth::shouldReceive('findByEmail')
        ->once()
        ->with($email)
        ->andReturn($user);

    expect(Fauth::findByEmail($email))->email->toBe($email);
});

it('can find a user by phone', function () {

    $phone = '1234567890';

    $user = FauthFake::userRecord('uid_phone', ['phoneNumber' => $phone]);

    Fauth::shouldReceive('findByPhone')->once()->with($phone)->andReturn($user);

    expect(Fauth::findByPhone($phone))->phoneNumber->toBe($phone);
});

it('can check user credentials', function () {
    $email = 'user@example.com';
    $password = 'secret';

    Fauth::shouldReceive('check')
        ->once()
        ->with($email, $password)
        ->andReturnTrue();

    expect(Fauth::check($email, $password))->toBeTrue();
});

it('can attempt login', function () {
    $input = ['email' => 'user@example.com', 'password' => 'secret'];

    $user = FauthFake::userRecord('attempt', $input);

    Fauth::shouldReceive('attempt')
        ->once()
        ->with($input)
        ->andReturn($user);

    expect(Fauth::attempt($input))->toBeInstanceOf(UserRecord::class);
});

it('can disable and enable users', function () {
    $uid = 'user_123';
    $user = FauthFake::userRecord($uid);

    Fauth::shouldReceive('disabled')->once()->with($uid)->andReturn($user);
    Fauth::shouldReceive('enabled')->once()->with($uid)->andReturn($user);

    expect(Fauth::disabled($uid))->toBeInstanceOf(UserRecord::class);
    expect(Fauth::enabled($uid))->toBeInstanceOf(UserRecord::class);
});

it('can create, update, and upsert a user', function () {
    $input = ['name' => 'John Doe'];
    $uid = 'user_456';
    $user = FauthFake::userRecord($uid);

    Fauth::shouldReceive('create')->once()->with($input)->andReturn($user);
    Fauth::shouldReceive('update')->once()->with($uid, $input)->andReturn($user);
    Fauth::shouldReceive('upsert')->once()->with($uid, $input)->andReturn($user);

    expect(Fauth::create($input))->toBeInstanceOf(UserRecord::class);
    expect(Fauth::update($uid, $input))->toBeInstanceOf(UserRecord::class);
    expect(Fauth::upsert($uid, $input))->toBeInstanceOf(UserRecord::class);
});

it('can delete users and all users', function () {
    $uids = ['user_1', 'user_2'];

    Fauth::shouldReceive('delete')->once()->with($uids)->andReturnTrue();
    Fauth::shouldReceive('deleteAllUsers')->once()->andReturn(2);

    expect(Fauth::delete($uids))->toBeTrue();
    expect(Fauth::deleteAllUsers())->toBe(2);
});

it('can update user password', function () {
    $email = 'reset@example.com';
    $password = 'new_secret';
    $user = FauthFake::userRecord('updatePassword', ['email' => $email]);

    Fauth::shouldReceive('updatePassword')
        ->once()
        ->with($email, $password)
        ->andReturn($user);

    expect(Fauth::updatePassword($email, $password))->toBeInstanceOf(UserRecord::class);
});

it('can query and count users', function () {
    $collection = collect([
        FauthFake::userRecord('user_1', []),
        FauthFake::userRecord('user_2', []),
    ]);

    Fauth::shouldReceive('query')->once()->with()->andReturn($collection);
    Fauth::shouldReceive('count')->once()->andReturn(2);

    expect(Fauth::query())->toBeInstanceOf(Collection::class);
    expect(Fauth::count())->toBe(2);
});

it('can search and find many users', function () {

    $collection = collect([
        FauthFake::userRecord('user_1'),
        FauthFake::userRecord('user_2'),
    ]);

    $uids = ['user_1', 'user_2'];

    Fauth::shouldReceive('search')->once()->withAnyArgs()->andReturn($collection);

    Fauth::shouldReceive('findMany')->once()->withAnyArgs()->andReturn($collection);

    expect(Fauth::search())->toHaveCount(2);
    expect(Fauth::findMany($uids))->toHaveCount(2);
});

it('can send reset/verification links', function () {

    $email = 'reset@example.com';

    $model = new class extends Model
    {
        protected $fillable = ['uid'];
    };

    $user = new $model(['uid' => 'test-uid']);

    Fauth::shouldReceive('sendResetLink')->once()->with($email)->andReturn('PHP is');

    Fauth::shouldReceive('sendVerificationEmail')->once()->with($user)->andReturnNull();

    expect(Fauth::sendResetLink($email))->toBe('PHP is');

    Fauth::sendVerificationEmail($user); // no exception, matches the mock
});
