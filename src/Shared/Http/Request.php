<?php
declare(strict_types=1);

namespace App\Shared\Http;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        private readonly array $query = [],
        private readonly array $body = [],
        private readonly array $headers = [],
        private readonly array $params = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $body = json_decode(file_get_contents('php://input') ?: '', true);

        $headers = function_exists('getallheaders') ? array_change_key_case(getallheaders()) : [];
        $headers['authorization'] ??= $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            rtrim($path, '/') ?: '/',
            $_GET,
            is_array($body) ? $body : [],
            $headers,
        );
    }

    public function comParams(array $params): self
    {
        return new self($this->method, $this->path, $this->query, $this->body, $this->headers, $params);
    }

    public function param(string $nome): ?string
    {
        return $this->params[$nome] ?? null;
    }

    public function query(string $nome, mixed $padrao = null): mixed
    {
        return $this->query[$nome] ?? $padrao;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function header(string $nome): string
    {
        return (string) ($this->headers[strtolower($nome)] ?? '');
    }

    public function bearerToken(): ?string
    {
        return preg_match('/Bearer\s+(\S+)/i', $this->header('authorization'), $m) ? $m[1] : null;
    }
}
