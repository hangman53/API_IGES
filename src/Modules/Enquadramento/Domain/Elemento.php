<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Domain;

use InvalidArgumentException;

enum Elemento: string
{
    case PO   = 'PO';
    case I    = 'I';
    case COR  = 'COR';
    case P    = 'P';
    case CC   = 'CC';
    case GRCI = 'GRCI';
    case A    = 'A';
    case MA   = 'MA';

    public function codigos(): array
    {
        return match ($this) {
            self::PO   => range(111, 117),
            self::I    => range(121, 127),
            self::COR  => range(131, 137),
            self::P    => range(211, 215),
            self::CC   => range(221, 226),
            self::GRCI => range(311, 316),
            self::A    => range(321, 325),
            self::MA   => range(331, 335),
        };
    }

    public static function todosOsCodigos(): array
    {
        return array_merge(...array_map(fn (self $e) => $e->codigos(), self::cases()));
    }

    public static function doCodigo(int $codigo): self
    {
        foreach (self::cases() as $elemento) {
            if (in_array($codigo, $elemento->codigos(), true)) {
                return $elemento;
            }
        }
        throw new InvalidArgumentException("Pergunta desconhecida: $codigo");
    }
}
