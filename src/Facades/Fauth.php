<?php

declare(strict_types=1);

namespace Bright\Fauth\Facades;

use Bright\Fauth\FauthFake;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Kreait\Firebase\Auth\UserQuery;
use Kreait\Firebase\Auth\UserRecord;

/**
 * @method static ?UserRecord find(string $uid)
 * @method static ?UserRecord findByEmail(string $email)
 * @method static ?UserRecord findByPhone(string $phone)
 * @method static bool check(string $email, string $password = '')
 * @method static ?UserRecord attempt(array<string, mixed> $input)
 * @method static ?UserRecord disabled(string $uid)
 * @method static ?UserRecord enabled(string $uid)
 * @method static UserRecord create(array<string, mixed> $input = [])
 * @method static ?UserRecord update(string $uid, array<string, mixed> $input = [])
 * @method static UserRecord upsert(?string $uid, array<string, mixed> $input = [])
 * @method static bool delete(string|array<int, string> $uids = '')
 * @method static int deleteAllUsers()
 * @method static UserRecord updatePassword(string $email, string $password)
 * @method static Collection<int, UserRecord> findMany(array<int, string> $uids = [])
 * @method static Collection<int, UserRecord> all()
 * @method static Collection<int, UserRecord> query(UserQuery|array<string, mixed> $query = [], bool $cache = true)
 * @method static int count()
 * @method static Collection<int, UserRecord> search(?string $search = null, int $offset = 0, int $limit = 10)
 * @method static string message(string $code, ?string $default = null)
 * @method static string sendResetLink(string $email)
 * @method static void sendVerificationEmail(Model $user, array<string, mixed> $action = [])
 *
 * @see \Bright\Fauth\Fauth
 */
class Fauth extends Facade
{
    /**
     * Replace the underlying instance with a fake for testing.
     */
    public static function fake(): FauthFake
    {
        $fake = new FauthFake;

        static::swap($fake);

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return 'fauth';
    }
}
