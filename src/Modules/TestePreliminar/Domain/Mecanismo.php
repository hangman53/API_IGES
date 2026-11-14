<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

enum Mecanismo: string
{
    case ESTRATEGIA = 'ESTRATEGIA';
    case LIDERANCA  = 'LIDERANCA';
    case CONTROLE   = 'CONTROLE';

    public function nome(): string
    {
        return match ($this) {
            self::ESTRATEGIA => 'Estratégia',
            self::LIDERANCA  => 'Liderança',
            self::CONTROLE   => 'Controle',
        };
    }

    public function componentes(): array
    {
        return array_values(array_filter(
            Componente::cases(),
            fn (Componente $c) => $c->mecanismo() === $this,
        ));
    }
}
