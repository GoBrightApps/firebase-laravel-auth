<?php

declare(strict_types=1);

namespace Bright\Fauth;

use Bright\Fauth\Facades\Fauth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array<string, mixed>  $credentials
     * @return (Authenticatable&\Illuminate\Database\Eloquent\Model)|null
     */
    public function retrieveByCredentials(array $credentials)
    {

        // If email/password login is attempted
        if (isset($credentials['email'], $credentials['password'])) {

            $model = $this->createModel();

            throw_if(
                ! method_exists($model, 'findByEmail'),
                'The model static findByEmail  was not found, it may missed to using trait HasFauth.'
            );

            /** @var (Authenticatable&\Illuminate\Database\Eloquent\Model)|null */
            return $model->findByEmail((string) $credentials['email']);
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  array<string, mixed>  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // If email/password login is attempted
        if (isset($credentials['email'], $credentials['password'])) {
            return (bool) Fauth::attempt($credentials);
        }

        return false;
    }

    /**
     * Rehash the user's password if required and supported.
     *
     * @return void
     */
    /** @phpstan-ignore-next-line */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        //
    }
}
