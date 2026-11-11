<?php
declare(strict_types=1);

namespace App\Shared\Auth;

final class UsuarioAutenticado
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $email,
        public readonly string $role,
    ) {
    }

    public function ehAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
