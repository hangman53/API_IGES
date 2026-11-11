<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Domain;

use App\Modules\Enquadramento\Api\EnquadramentoApi;

final class Questionario
{
    public const NOTA_MINIMA = 0;
    public const NOTA_MAXIMA = 4;

    public const INSTRUMENTOS = [
        1  => ['Mínimo', 'Plano Municipal de Saneamento Básico'],
        2  => ['Mínimo', 'Política Municipal de Saneamento Básico'],
        3  => ['Mínimo', 'Contrato com o prestador dos serviços de esgotamento sanitário'],
        4  => ['Mínimo', 'Instituição de um órgão colegiado de caráter consultivo'],
        5  => ['Mínimo', 'Definição da entidade reguladora infranacional (ERI)'],
        6  => ['Mínimo', 'Licenciamento Ambiental'],
        7  => ['Mínimo', 'Responder o Sinisa anualmente'],
        8  => ['Mínimo', 'Sistema de informação'],
        9  => ['Conjunto', 'Atualização periódica do Plano Municipal de Saneamento'],
        10 => ['Conjunto', 'Audiência pública e consulta pública quanto ao edital de licitação da prestação de serviço'],
        11 => ['Conjunto', 'Estabelecimento do Fundo de Saneamento Municipal'],
        12 => ['Conjunto', 'Estudo da capacidade econômico-financeira da unidade regional'],
        13 => ['Conjunto', 'Estudo da capacidade econômico-financeira do prestador dos serviços'],
        14 => ['Conjunto', 'Instituição do sistema de pagamento pelo serviço pelo titular dos serviços (Lei)'],
        15 => ['Conjunto', 'Metas de universalização dos serviços de esgotamento sanitário'],
        16 => ['Conjunto', 'Normativa da ERI para a utilização de métodos alternativos e descentralizados'],
        17 => ['Conjunto', 'Normativa da ERI para o direcionamento em situações adversas'],
        18 => ['Conjunto', 'Normativa da ERI para regularização quanto a quantidade, qualidade e regularidade dos serviços de esgotamento sanitário'],
        19 => ['Conjunto', 'Normativa da ERI quanto as tarifas, subsídios e pagamento dos usuários quanto ao serviço prestado'],
        20 => ['Conjunto', 'Participação social no processo de elaboração do Plano de Saneamento Básico'],
        21 => ['Conjunto', 'Participação social (consultas ou audiências públicas) no processo de elaboração da cobrança pela prestação dos serviços'],
        22 => ['Conjunto', 'Plano de Investimento apresentado pelo prestador de serviço contratado'],
        23 => ['Conjunto', 'Plano Diretor Municipal Participativo'],
    ];

    public const PERGUNTAS = [
        111 => 'O instrumento avaliado define objetivos claros para os Serviços de Esgotamento Sanitário?',
        112 => 'O instrumento avaliado estabelece metas mensuráveis para os serviços de esgotamento sanitário?',
        113 => 'O instrumento avaliado define indicadores de desempenho verificáveis para acompanhamento dos Serviços de Esgotamento Sanitário?',
        114 => 'O instrumento avaliado estabelece ações planejadas para orientar o planejamento, organização e o desenvolvimento dos serviços e esgotamento sanitário?',
        115 => 'O instrumento prevê diagnóstico das condições do esgotamento sanitário considerando a realidade territorial?',
        116 => 'O instrumento avaliado vincula os planos e metas estabelecidos à alocação de recursos necessários para sua execução?',
        117 => 'O instrumento avaliado prevê mecanismos de acompanhamento e ajuste contínuo do planejamento dos Serviços de Esgotamento Sanitário?',
        121 => 'O instrumento avaliado formaliza juridicamente (por meio de leis, decretos ou normas) as regras que regem os Serviços de Esgotamento Sanitário?',
        122 => 'O instrumento avaliado define padrões técnicos a serem seguidos na prestação dos serviços de esgotamento sanitário?',
        123 => 'O instrumento avaliado formaliza contratos com prestadoras de serviço, com obrigações e responsabilidades claramente definidas?',
        124 => 'O instrumento avaliado prevê a atuação de agência reguladora (ou órgão equivalente) sobre o SES?',
        125 => 'O instrumento avaliado estabelece mecanismos de fiscalização regulatória sobre a prestação dos serviços de esgotamento sanitário?',
        126 => 'O instrumento define as atribuições e responsabilidades dos atores institucionais envolvidos nos serviços esgotamento sanitário?',
        127 => 'O instrumento estabelece mecanismos que tornam transparentes as atribuições e responsabilidades institucionais no esgotamento sanitário?',
        131 => 'O instrumento avaliado prevê ações de capacitação técnica para os profissionais envolvidos nos Serviços de Esgotamento Sanitário?',
        132 => 'O instrumento avaliado define uma estrutura organizacional (papéis, órgãos, responsabilidades) adequada à gestão dos Serviços de Esgotamento Sanitário?',
        133 => 'O instrumento avaliado identifica ou assegura recursos financeiros adequados para os Serviços de Esgotamento Sanitário?',
        134 => 'O instrumento avaliado identifica ou assegura recursos tecnológicos e materiais adequados à prestação dos serviços?',
        135 => 'O instrumento avaliado prevê o monitoramento contínuo dos recursos financeiros, humanos, materiais e tecnológicos disponíveis para o SES?',
        136 => 'O instrumento avaliado considera a adequação das soluções técnicas às realidades e especificidades locais do território?',
        137 => 'O instrumento estabelece diretrizes, metas ou mecanismos que visem garantir a operação eficiente e eficaz dos serviços de esgotamento sanitário?',
        211 => 'O instrumento avaliado inclui formalmente representantes do setor público, do setor privado, da sociedade civil e do terceiro setor nos processos relativos ao esgotamento sanitário?',
        212 => 'O instrumento avaliado prevê instâncias de participação social consultiva ou com poder deliberativo sobre as políticas e planejamentos dos serviços de esgotamento sanitário?',
        213 => 'O instrumento avaliado incorpora mecanismos para considerar o conhecimento e as demandas das comunidades locais na definição das ações dos serviços de esgotamento sanitário?',
        214 => 'O instrumento avaliado prevê ações de educação ambiental?',
        215 => 'O instrumento avaliado institui canais permanentes de comunicação, consulta e cooperação entre a gestão pública e a população no que tange sobre os serviços de esgotamento sanitário?',
        221 => 'O instrumento avaliado define claramente papéis, responsabilidades e processos entre os diferentes atores dos Serviços de Esgotamento Sanitário?',
        222 => 'O instrumento avaliado prevê instrumentos formais de cooperação interinstitucional entre os diferentes atores envolvidos nos serviços de esgotamento sanitário?',
        223 => 'O instrumento avaliado estabelece mecanismos de comunicação transparente entre os atores envolvidos nos Serviços de Esgotamento Sanitário?',
        224 => 'O instrumento avaliado define o alinhamento de estratégias e recursos entre os atores envolvidos nos Serviços de Esgotamento Sanitário?',
        225 => 'O instrumento avaliado prevê mecanismos que asseguram a continuidade operacional do SES diante de mudanças administrativas ou de gestão?',
        226 => 'O instrumento avaliado estabelece mecanismos de fiscalização institucional e de acompanhamento social das políticas de esgotamento sanitário?',
        311 => 'O instrumento avaliado prevê a identificação e o mapeamento de riscos e vulnerabilidades que possam comprometer os serviços de esgotamento sanitário?',
        312 => 'O instrumento avaliado estabelece planos de contingência para eventos que possam comprometer a operação dos serviços de esgotamento sanitário?',
        313 => 'O instrumento avaliado define controles internos formalizados para a gestão dos serviços de esgotamento sanitário?',
        314 => 'O instrumento avaliado prevê auditorias e protocolos de resposta a eventos de risco no âmbito dos serviços de esgotamento sanitário?',
        315 => 'O instrumento avaliado considera vulnerabilidades territoriais, socioambientais e institucionais específicas do município na gestão de riscos dos serviços de esgotamento sanitário?',
        316 => 'O instrumento avaliado prevê mecanismos de retroalimentação (aprendizado contínuo) a partir dos eventos de risco identificados?',
        321 => 'O instrumento avaliado prevê instrumentos de prestação de contas sobre os serviços de esgotamento sanitário?',
        322 => 'O instrumento avaliado estabelece mecanismos de transparência e divulgação de resultados sobre os serviços de esgotamento sanitário à sociedade?',
        323 => 'O instrumento avaliado prevê supervisão e/ou auditoria de contratos relacionados aos serviços de esgotamento sanitário?',
        324 => 'O instrumento avaliado define mecanismos de responsabilização para os atores envolvidos nos serviços de esgotamento sanitário?',
        325 => 'O instrumento avaliado estabelece canais de comunicação para que a população acompanhe e avalie a prestação de contas dos serviços de esgotamento sanitário?',
        331 => 'O instrumento avaliado prevê a coleta e análise contínua de dados sobre insumos, atividades, produtos e impactos dos diferentes atores e etapas dos serviços de esgotamento sanitário?',
        332 => 'O instrumento avaliado define indicadores de desempenho, ambientais e socioeconômicos para o acompanhamento dos serviços de esgotamento sanitário?',
        333 => 'O instrumento avaliado estabelece mecanismos para identificar falhas e propor melhorias a partir do monitoramento realizado quanto aos atores e etapas dos serviços de esgotamento sanitário',
        334 => 'O instrumento avaliado prevê o uso dos dados de monitoramento para subsidiar decisões estratégicas, táticas e operacionais referente aos serviços de esgotamento sanitário?',
        335 => 'O instrumento avaliado prevê ações educativas que permitam à população compreender os impactos sociais e ambientais dos serviços de esgotamento sanitário a partir dos dados de monitoramento?',
    ];

    public static function nomeInstrumento(int $instrumento): string
    {
        return self::INSTRUMENTOS[$instrumento][1];
    }

    public static function validarInstrumento(mixed $instrumento): int
    {
        $valor = filter_var($instrumento, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => EnquadramentoApi::TOTAL_INSTRUMENTOS],
        ]);
        if ($valor === false) {
            throw new RespostasInvalidas('Instrumento inválido');
        }
        return $valor;
    }

    public static function validarNotas(mixed $respostas): array
    {
        if (!is_array($respostas)) {
            throw new RespostasInvalidas('Respostas inválidas');
        }

        $notas = [];
        foreach (Elemento::todosOsCodigos() as $codigo) {
            $nota = filter_var($respostas[$codigo] ?? null, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => self::NOTA_MINIMA, 'max_range' => self::NOTA_MAXIMA],
            ]);
            if ($nota === false) {
                throw new RespostasInvalidas("Resposta ausente ou inválida na pergunta $codigo");
            }
            $notas[$codigo] = $nota;
        }
        return $notas;
    }

    public static function paraArray(): array
    {
        $instrumentos = [];
        foreach (self::INSTRUMENTOS as $numero => [$tipo, $nome]) {
            $instrumentos[] = ['numero' => $numero, 'tipo' => $tipo, 'nome' => $nome];
        }

        $elementos = [];
        foreach (Elemento::cases() as $elemento) {
            $elementos[] = [
                'sigla'     => $elemento->value,
                'perguntas' => array_map(
                    fn (int $codigo) => ['codigo' => $codigo, 'pergunta' => self::PERGUNTAS[$codigo]],
                    $elemento->codigos(),
                ),
            ];
        }

        return [
            'escala'       => ['minima' => self::NOTA_MINIMA, 'maxima' => self::NOTA_MAXIMA],
            'instrumentos' => $instrumentos,
            'elementos'    => $elementos,
        ];
    }
}
