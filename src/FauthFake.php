<?php

declare(strict_types=1);

namespace Bright\Fauth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kreait\Firebase\Auth\UserQuery;
use Kreait\Firebase\Auth\UserRecord;
use ReflectionClass;
use RuntimeException;
use Stringable;

/**
 * @phpstan-type CallRecord array{method: string, parameters: array<int, mixed>}
 */
class FauthFake
{
    /** @var list<CallRecord> */
    private array $calls = [];

    /** @var array<string, UserRecord> */
    private array $users = [];

    // --------------------------------------------------------------------------
    // Support methods
    // --------------------------------------------------------------------------

    /**
     * @param  non-empty-string  $uid
     * @param  array<string,mixed>  $data
     */
    public static function userRecord(string $uid, array $data = []): UserRecord
    {
        $ref = new ReflectionClass(UserRecord::class);
        /** @var UserRecord $record */
        $record = $ref->newInstanceWithoutConstructor();

        foreach (['uid' => $uid] + $data as $prop => $val) {
            if ($ref->hasProperty($prop)) {
                $p = $ref->getProperty($prop);
                $p->setValue($record, $val);
            }
        }

        return $record;
    }

    // --------------------------------------------------------------------------
    // Core methods mirroring Fauth with PHPStan types
    // --------------------------------------------------------------------------

    public function find(string $uid): ?UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());

        return $this->users[$uid] ?? null;
    }

    public function findByEmail(string $email): ?UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());

        return collect($this->users)->first(fn (UserRecord $u): bool => $u->email === $email);
    }

    public function findByPhone(string $phone): ?UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());

        return collect($this->users)->first(fn (UserRecord $u): bool => $u->phoneNumber === $phone);
    }

    public function check(string $email, string $password = ''): bool
    {
        $this->record(__FUNCTION__, func_get_args());

        return $email !== '' && $password !== '';
    }

    /**
     * @param  array<string,string>  $input
     */
    public function attempt(array $input): ?UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());
        $email = $input['email'] ?? '';

        return $this->findByEmail($email);
    }

    public function disabled(string $uid): ?UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());

        return $this->find($uid);
    }

    public function enabled(string $uid): ?UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());

        return $this->find($uid);
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function create(array $input = []): UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());

        $uid = trim((string) ($input['uid'] ?? uniqid('user_', true)));
        if ($uid === '') {
            $uid = uniqid('user_', true);
        }

        $record = self::userRecord($uid, $input);
        $this->users[$uid] = $record;

        return $record;
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function update(?string $uid, array $input = []): ?UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());
        if ($uid === null || $uid === '') {
            return null;
        }

        $existing = $this->users[$uid] ?? null;
        if (! $existing) {
            return null;
        }

        $record = self::userRecord($uid, array_merge((array) $existing, $input));
        $this->users[$uid] = $record;

        return $record;
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function upsert(?string $uid, array $input = []): UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());

        return $this->update($uid, $input) ?? $this->create($input);
    }

    /**
     * @param  string|array<int, string|Stringable>  $uids
     */
    public function delete(string|array $uids = ''): bool
    {
        $this->record(__FUNCTION__, func_get_args());
        $uids = (array) $uids;
        foreach ($uids as $id) {
            unset($this->users[(string) $id]);
        }

        return true;
    }

    public function deleteAllUsers(): int
    {
        $this->record(__FUNCTION__, func_get_args());
        $count = count($this->users);
        $this->users = [];

        return $count;
    }

    public function updatePassword(string $email, string $password): UserRecord
    {
        $this->record(__FUNCTION__, func_get_args());
        $user = $this->findByEmail($email);

        return $user ?? $this->create(['email' => $email, 'password' => $password]);
    }

    /**
     * @param  array<int, string|Stringable>  $uids
     * @return Collection<int, UserRecord|null>
     */
    public function findMany(array $uids = [], bool $cache = true): Collection
    {
        $this->record(__FUNCTION__, func_get_args());

        return collect($uids)->map(fn ($id) => $this->users[(string) $id] ?? null);
    }

    /**
     * @return Collection<int, UserRecord>
     */
    public function all(): Collection
    {
        $this->record(__FUNCTION__, func_get_args());

        return collect(array_values($this->users));
    }

    /**
     * @param  UserQuery|array<string, mixed>  $query
     * @return Collection<int, UserRecord>
     */
    public function query(UserQuery|array $query = [], bool $cache = true): Collection
    {
        $this->record(__FUNCTION__, func_get_args());

        return $this->all();
    }

    public function count(): int
    {
        $this->record(__FUNCTION__, func_get_args());

        return count($this->users);
    }

    /**
     * @return Collection<int, UserRecord>
     */
    public function search(?string $search = null, int $offset = 0, int $limit = 10): Collection
    {
        $this->record(__FUNCTION__, func_get_args());
        $term = mb_strtolower((string) $search);

        return collect($this->users)
            ->filter(fn (UserRecord $u): bool => $term === '' ||
                str_contains(mb_strtolower($u->displayName ?? ''), $term) ||
                str_contains(mb_strtolower($u->email ?? ''), $term)
            )
            ->slice($offset, $limit)
            ->values();
    }

    public function message(string $code, ?string $default = null): string
    {
        $this->record(__FUNCTION__, func_get_args());

        return $default ?? $code;
    }

    public function sendResetLink(string $email): string
    {
        $this->record(__FUNCTION__, func_get_args());

        return 'RESET_LINK_SENT';
    }

    /**
     * @param  array<string, mixed>  $action
     */
    public function sendVerificationEmail(Model $user, array $action = []): void
    {
        $this->record(__FUNCTION__, func_get_args());
    }

    /** @return list<CallRecord> */
    public function calls(): array
    {
        return $this->calls;
    }

    public function assertCalled(string $method): void
    {
        $called = collect($this->calls)->pluck('method')->contains($method);
        throw_unless($called, RuntimeException::class, "Expected [{$method}] to be called, but it wasn't.");
    }

    /**
     * Record a call for later assertions.
     *
     * @param  array<int, mixed>  $parameters
     */
    private function record(string $method, array $parameters): void
    {
        $this->calls[] = ['method' => $method, 'parameters' => $parameters];
    }
}
