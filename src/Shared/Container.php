<?php
declare(strict_types=1);

namespace App\Shared;

use RuntimeException;

final class Container
{
    private array $fabricas = [];

    private array $instancias = [];

    public function set(string $id, callable $fabrica): void
    {
        $this->fabricas[$id] = $fabrica;
        unset($this->instancias[$id]);
    }

    public function get(string $id): object
    {
        if (!isset($this->instancias[$id])) {
            if (!isset($this->fabricas[$id])) {
                throw new RuntimeException("Serviço não registrado: $id");
            }
            $this->instancias[$id] = ($this->fabricas[$id])($this);
        }
        return $this->instancias[$id];
    }
}
