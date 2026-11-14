<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

enum Componente: string
{
    case PO  = 'PO';
    case I   = 'I';
    case COR = 'COR';
    case P   = 'P';
    case CC  = 'CC';
    case GR  = 'GR';
    case A   = 'A';
    case MA  = 'MA';

    public function nome(): string
    {
        return match ($this) {
            self::PO  => 'Planos e Objetivos',
            self::I   => 'Institucionalização',
            self::COR => 'Capacidade Organizacional e de Recursos',
            self::P   => 'Participação',
            self::CC  => 'Coordenação e Coerência',
            self::GR  => 'Gestão de Riscos e Controle Interno',
            self::A   => 'Accountability',
            self::MA  => 'Monitoramento e Avaliação',
        };
    }

    public function mecanismo(): Mecanismo
    {
        return match ($this) {
            self::PO, self::I, self::COR => Mecanismo::ESTRATEGIA,
            self::P, self::CC            => Mecanismo::LIDERANCA,
            self::GR, self::A, self::MA  => Mecanismo::CONTROLE,
        };
    }

    public function peso(): float
    {
        return match ($this->mecanismo()) {
            Mecanismo::LIDERANCA => 1 / 6,
            default              => 1 / 9,
        };
    }

    public function instrumentosMinimos(): array
    {
        return match ($this) {
            self::PO  => [
                'Plano Municipal de Saneamento Básico',
                'Plano Municipal de Saneamento Básico Simplificado',
                'Plano Regional de Saneamento Básico',
            ],
            self::I   => ['Política Municipal de Saneamento Básico'],
            self::COR => ['Contrato com o prestador dos serviços de esgotamento sanitário'],
            self::P   => ['Instituição de um órgão colegiado de caráter consultivo'],
            self::CC  => ['Definição da entidade reguladora infranacional (ERI)'],
            self::GR  => ['Licenciamento Ambiental'],
            self::A   => ['Responder o Sinisa anualmente'],
            self::MA  => ['Sistema de informação'],
        };
    }
}
