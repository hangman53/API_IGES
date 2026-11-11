<?php
declare(strict_types=1);

namespace App\Shared\Http;

use RuntimeException;

final class HttpException extends RuntimeException
{
    public function __construct(public readonly int $status, string $mensagem)
    {
        parent::__construct($mensagem);
    }

    public static function badRequest(string $mensagem = 'Dados inválidos'): self
    {
        return new self(400, $mensagem);
    }

    public static function naoAutenticado(string $mensagem = 'Não autenticado'): self
    {
        return new self(401, $mensagem);
    }

    public static function proibido(string $mensagem = 'Acesso negado'): self
    {
        return new self(403, $mensagem);
    }

    public static function naoEncontrado(string $mensagem = 'Recurso não encontrado'): self
    {
        return new self(404, $mensagem);
    }
}
