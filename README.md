# API iGES

API do iGES em PHP 8.2 + PostgreSQL, organizada como um **monolito modular**: um único
deploy, mas o código dividido em módulos independentes com fronteiras explícitas.

## Estrutura

```
API_IGES/
├── public/index.php          # front controller: toda requisição entra por aqui
├── config/modules.php        # lista de módulos ativos
├── bin/
│   ├── migrate.php           # aplica as migrations do Shared e de cada módulo
│   └── check-boundaries.php  # verifica as regras de fronteira entre módulos
└── src/
    ├── Shared/               # núcleo compartilhado (não conhece nenhum módulo)
    │   ├── Kernel.php        # monta o app, carrega módulos, trata erros e CORS
    │   ├── Container.php     # injeção de dependências
    │   ├── Module/Module.php # contrato que todo módulo implementa
    │   ├── Http/             # Request, Response, Router, HttpException
    │   ├── Database/         # conexão PDO e Migrator
    │   └── Auth/             # autenticação por token Bearer (tabela usuarios)
    └── Modules/
        ├── Enquadramento/
        │   ├── Api/              # PÚBLICO: contrato usado por outros módulos
        │   ├── Application/      # casos de uso
        │   ├── Domain/           # regras de negócio, sem HTTP nem SQL
        │   ├── Infrastructure/   # implementações (PDO)
        │   ├── Http/             # controllers
        │   ├── migrations/       # SQL das tabelas do módulo
        │   └── EnquadramentoModule.php
        ├── Iges/                 # mesma estrutura
        └── TestePreliminar/      # mesma estrutura
```

## Regras de fronteira

1. **Um módulo só usa outro pelo namespace `Api`.** Exemplo: o `Iges` usa
   `Enquadramento\Api\EnquadramentoApi` e nunca o repositório ou as tabelas do Enquadramento.
2. **Cada módulo é dono das suas tabelas.** Só o próprio módulo lê e escreve nelas.
3. **O `Shared` não depende de módulos.**

O script `composer check-boundaries` verifica as regras 1 e 3 e falha se alguma for violada.
Vale colocá-lo no CI.

## Endpoints

| Método | Rota                          | Módulo        | Descrição                                   |
|--------|-------------------------------|---------------|---------------------------------------------|
| GET    | `/health`                     | Shared        | Verifica se a API está no ar                |
| GET    | `/enquadramento/questionario` | Enquadramento | Instrumentos, elementos e perguntas (público) |
| GET    | `/enquadramento/respostas`    | Enquadramento | Notas já enviadas pelo usuário logado       |
| POST   | `/enquadramento/respostas`    | Enquadramento | Grava um instrumento e devolve o cálculo dele |
| GET    | `/enquadramento/resultado`    | Enquadramento | Cálculo de todos os instrumentos respondidos |
| GET    | `/iges/progresso`             | IGES          | Instrumentos respondidos e percentual       |
| GET    | `/teste-preliminar/configuracao` | TestePreliminar | Componentes, pesos, instrumentos e níveis (público) |
| GET    | `/teste-preliminar/respostas` | TestePreliminar | Respostas salvas do usuário logado        |
| POST   | `/teste-preliminar/respostas` | TestePreliminar | Grava as respostas e devolve o iGES       |
| GET    | `/teste-preliminar/resultado` | TestePreliminar | iGES calculado a partir das respostas salvas |

Com exceção de `/health`, `/enquadramento/questionario` e `/teste-preliminar/configuracao`,
as rotas exigem `Authorization: Bearer <token>`.

### Cálculo do enquadramento

Reproduz a aba "Rank (f)" da planilha `Etapa 3 - Enquadramento.xlsx`, por instrumento:

- **Somatório**: soma das notas (0 a 4) das perguntas de cada elemento (PO, I, COR, P, CC, GRCI, A, MA).
- **Equivalência**: somatório ÷ 7 (divisor fixo em todos os elementos, igual à planilha).
- **Rank**: posição decrescente da equivalência entre os 8 elementos (`RANK.EQ`).
  Empates dividem a posição; elemento com equivalência 0 fica com `rank: null`.

Exemplo de `POST /enquadramento/respostas`:

```json
{ "instrumento": 1, "respostas": { "111": 4, "112": 3, "...": 0, "335": 2 } }
```

Resposta:

```json
{
  "mensagem": "Respostas salvas",
  "resultado": {
    "instrumento": 1,
    "nome": "Plano Municipal de Saneamento Básico",
    "elementos": [
      { "elemento": "PO", "somatorio": 21, "equivalencia": 3, "rank": 1 },
      { "elemento": "I",  "somatorio": 0,  "equivalencia": 0, "rank": null }
    ]
  }
}
```

A regra está em [CalculadoraEnquadramento.php](src/Modules/Enquadramento/Domain/CalculadoraEnquadramento.php)
e os testes em `tests/` (`composer test`).

### Teste preliminar (cálculo do iGES)

Reproduz a planilha `Teste preliminares v9.xlsx`. São 8 componentes agrupados em 3 mecanismos:

| Mecanismo  | Componentes (peso)                                   |
|------------|------------------------------------------------------|
| Estratégia | PO, I, COR (1/9 cada)                                |
| Liderança  | P, CC (1/6 cada)                                     |
| Controle   | GR, A, MA (1/9 cada)                                 |

Cada um dos 23 instrumentos básicos tem um nível de 1 a 8 em alguns componentes
([CatalogoInstrumentos.php](src/Modules/TestePreliminar/Domain/CatalogoInstrumentos.php)).
O fator de cada nível cai 10% a cada nível: `fator(n) = 1 × 0,9^(n-1)`.

Para cada componente:

- **Máximo** = soma dos fatores de todos os instrumentos básicos do componente
  (menos os caso a caso desconsiderados).
- **Nota básica** = 100 × (fatores dos básicos que o município possui) ÷ máximo.
- **Nota básico + extras** = 100 × (básicos do município + extras) ÷ (máximo + extras).

Depois:

- **iGES** = produto de `nota ^ peso` de cada componente (média geométrica ponderada, 0 a 100).
  Se faltar o instrumento mínimo de algum componente, o iGES vale 0, `calculavel` fica `false`
  e a falta aparece em `pendencias`.
- **Nota do mecanismo** = média das notas dos seus componentes (0 a 100).
- **Categoria**: Excelente (> 90), Bom (> 70), Médio (> 50), Ruim (> 25), Muito ruim (0 a 25).

Exemplo de `POST /teste-preliminar/respostas` (ids conforme `/teste-preliminar/configuracao`):

```json
{
  "instrumentos": [1, 2, 3, 4, 5, 6, 7, 11, 12, 16, 17],
  "desconsiderados": [],
  "minimos": ["PO", "I", "COR", "P", "CC", "GR", "A", "MA"],
  "extras": [
    { "nome": "Instrumento extra 1", "niveis": { "PO": 1, "I": 2, "COR": 3 } }
  ]
}
```

- `instrumentos`: ids dos instrumentos básicos que o município possui.
- `desconsiderados`: instrumentos caso a caso (1, 10, 12, 16) que não se aplicam ao município.
- `minimos`: componentes em que o município tem o instrumento mínimo.
- `extras`: até 10 instrumentos encontrados no município. Os níveis vão de 1 a 8, não se repetem
  entre componentes e não pulam números (para ter nível 3 é preciso ter 1 e 2).

A resposta traz `iges.basico` e `iges.basicoMaisExtras` (valor, categoria, calculavel),
a nota de cada mecanismo e de cada componente, e as pendências. O teste
[CalculadoraIgesTest.php](tests/Modules/TestePreliminar/CalculadoraIgesTest.php) reproduz o
município de exemplo da planilha (iGES básico 48,64 e básico + extras 54,34).

## Rodando

Copie o `.env.example` para `.env` e preencha `DB_PASSWORD`:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec api php bin/migrate.php
curl http://localhost:8080/health
```

Sem Docker: `composer install`, `composer migrate` e `composer serve`.

## Criando um módulo novo

1. Crie `src/Modules/<Nome>/` com as pastas `Api`, `Application`, `Domain`,
   `Infrastructure`, `Http` e `migrations`.
2. Crie `<Nome>Module.php` implementando `App\Shared\Module\Module`.
3. Acrescente a classe em `config/modules.php`.
