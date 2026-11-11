<?php
declare(strict_types=1);

namespace App\Shared\Http;

final class Response
{
    private function __construct(
        public readonly int $status,
        public readonly string $corpo,
        public readonly array $headers,
    ) {
    }

    public static function json(int $status, mixed $dados): self
    {
        return new self(
            $status,
            json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json; charset=utf-8'],
        );
    }

    public static function vazia(int $status = 204): self
    {
        return new self($status, '', []);
    }

    public function comHeader(string $nome, string $valor): self
    {
        return new self($this->status, $this->corpo, [...$this->headers, $nome => $valor]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $nome => $valor) {
            header("$nome: $valor");
        }
        echo $this->corpo;
    }
}
