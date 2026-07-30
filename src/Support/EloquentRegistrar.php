<?php

declare(strict_types=1);

namespace Kurt\Modules\AuthKit\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Kurt\Modules\AuthKit\Contracts\Registrar;
use Kurt\Modules\Core\Contracts\UserResolver;

final class EloquentRegistrar implements Registrar
{
    public function __construct(
        private readonly UserResolver $users,
        private readonly Hasher $hasher,
        private readonly Repository $config,
    ) {}

    public function register(array $data): Authenticatable
    {
        $class = $this->users->modelClass();

        /** @var Model&Authenticatable $user */
        $user = new $class;

        /** @var array<int, string> $fields */
        $fields = (array) $this->config->get('auth-kit.register.fields', ['name', 'email']);

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $user->setAttribute($field, $data[$field]);
            }
        }

        $user->setAttribute('password', $this->hasher->make((string) $data['password']));
        $user->save();

        return $user;
    }
}
