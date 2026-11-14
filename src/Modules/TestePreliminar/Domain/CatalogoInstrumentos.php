<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

final class CatalogoInstrumentos
{
    public const BASICOS = [
        1  => ['nome' => 'Plano de Saneamento Básico', 'casoACaso' => true, 'niveis' => ['PO' => 1, 'I' => 2, 'COR' => 3, 'P' => 5, 'CC' => 4, 'GR' => 6, 'A' => 8, 'MA' => 7]],
        2  => ['nome' => 'Política Municipal de Saneamento Básico', 'casoACaso' => false, 'niveis' => ['PO' => 2, 'I' => 1, 'COR' => 3, 'CC' => 5, 'GR' => 4, 'A' => 7, 'MA' => 6]],
        3  => ['nome' => 'Contrato com o prestador dos serviços de esgotamento sanitário', 'casoACaso' => false, 'niveis' => ['I' => 2, 'COR' => 1, 'CC' => 6, 'GR' => 3, 'A' => 4, 'MA' => 5]],
        4  => ['nome' => 'Licenciamento Ambiental', 'casoACaso' => false, 'niveis' => ['I' => 2, 'COR' => 5, 'CC' => 3, 'GR' => 1, 'MA' => 4]],
        5  => ['nome' => 'Responder o Sinisa anualmente', 'casoACaso' => false, 'niveis' => ['PO' => 4, 'P' => 3, 'CC' => 5, 'A' => 1, 'MA' => 2]],
        6  => ['nome' => 'Sistema de informação', 'casoACaso' => false, 'niveis' => ['PO' => 5, 'P' => 2, 'CC' => 4, 'A' => 3, 'MA' => 1]],
        7  => ['nome' => 'Instituição de um órgão colegiado de caráter consultivo', 'casoACaso' => false, 'niveis' => ['I' => 4, 'COR' => 6, 'P' => 1, 'CC' => 5, 'GR' => 7, 'A' => 2, 'MA' => 3]],
        8  => ['nome' => 'Definição da entidade reguladora infranacional (ERI)', 'casoACaso' => false, 'niveis' => ['I' => 2, 'COR' => 6, 'P' => 5, 'CC' => 1, 'GR' => 7, 'A' => 3, 'MA' => 4]],
        9  => ['nome' => 'Atualização periódica do Plano Municipal de Saneamento', 'casoACaso' => false, 'niveis' => ['PO' => 1, 'I' => 2, 'COR' => 6, 'P' => 7, 'CC' => 4, 'GR' => 5, 'MA' => 3]],
        10 => ['nome' => 'Audiência pública e consulta pública quanto ao edital de licitação', 'casoACaso' => true, 'niveis' => ['COR' => 4, 'P' => 1, 'GR' => 3, 'A' => 2]],
        11 => ['nome' => 'Estabelecimento do Fundo de Saneamento Municipal', 'casoACaso' => false, 'niveis' => ['PO' => 4, 'I' => 2, 'COR' => 1, 'CC' => 3]],
        12 => ['nome' => 'Estudo da capacidade econômico-financeira da unidade regional', 'casoACaso' => true, 'niveis' => ['PO' => 4, 'COR' => 1, 'CC' => 2, 'A' => 3]],
        13 => ['nome' => 'Estudo da capacidade econômico-financeira do prestador dos serviços', 'casoACaso' => false, 'niveis' => ['PO' => 4, 'COR' => 1, 'CC' => 2, 'A' => 3]],
        14 => ['nome' => 'Instituição do sistema de pagamento pelo serviço pelo titular dos serviços (Lei)', 'casoACaso' => false, 'niveis' => ['I' => 1, 'COR' => 2]],
        15 => ['nome' => 'Metas de universalização dos serviços de esgotamento sanitário', 'casoACaso' => false, 'niveis' => ['PO' => 1, 'I' => 2, 'CC' => 3, 'MA' => 4]],
        16 => ['nome' => 'Normativa da ERI para a utilização de métodos alternativos e descentralizados', 'casoACaso' => true, 'niveis' => ['PO' => 3, 'I' => 2, 'CC' => 1, 'GR' => 4]],
        17 => ['nome' => 'Normativa da ERI para o direcionamento em situações adversas', 'casoACaso' => false, 'niveis' => ['PO' => 3, 'I' => 4, 'CC' => 2, 'GR' => 1]],
        18 => ['nome' => 'Normativa da ERI para regularização quanto a quantidade, qualidade e regularidade dos serviços de esgotamento sanitário', 'casoACaso' => false, 'niveis' => ['I' => 1, 'COR' => 4, 'CC' => 3, 'GR' => 5, 'MA' => 2]],
        19 => ['nome' => 'Normativa da ERI quanto as tarifas, subsídios e pagamento dos usuários quanto ao serviço prestado', 'casoACaso' => false, 'niveis' => ['I' => 3, 'COR' => 1, 'CC' => 4, 'GR' => 5, 'A' => 2]],
        20 => ['nome' => 'Participação social no processo de elaboração do Plano de Saneamento Básico', 'casoACaso' => false, 'niveis' => ['PO' => 3, 'P' => 1, 'CC' => 5, 'A' => 2, 'MA' => 4]],
        21 => ['nome' => 'Participação social (consultas ou audiências públicas) no processo de elaboração da cobrança pela prestação dos serviços', 'casoACaso' => false, 'niveis' => ['COR' => 3, 'P' => 1, 'CC' => 4, 'A' => 2, 'MA' => 5]],
        22 => ['nome' => 'Plano de Investimento apresentado pelo prestador de serviço contratado', 'casoACaso' => false, 'niveis' => ['PO' => 3, 'COR' => 2, 'CC' => 4, 'A' => 1]],
        23 => ['nome' => 'Plano Diretor Municipal', 'casoACaso' => false, 'niveis' => ['PO' => 1, 'I' => 4, 'COR' => 2, 'P' => 6, 'CC' => 3, 'GR' => 8, 'A' => 5, 'MA' => 7]],
    ];

    public static function existe(int $id): bool
    {
        return isset(self::BASICOS[$id]);
    }

    public static function ehCasoACaso(int $id): bool
    {
        return self::BASICOS[$id]['casoACaso'] ?? false;
    }

    public static function nivel(int $id, Componente $componente): ?int
    {
        return self::BASICOS[$id]['niveis'][$componente->value] ?? null;
    }

    public static function paraArray(): array
    {
        $lista = [];
        foreach (self::BASICOS as $id => $instrumento) {
            $lista[] = ['id' => $id, ...$instrumento];
        }
        return $lista;
    }
}
