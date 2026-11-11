<?php
declare(strict_types=1);

namespace App\Shared\Auth;

use App\Shared\Http\HttpException;
use App\Shared\Http\Request;
use PDO;

final class Autenticador
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function exigirUsuario(Request $request): UsuarioAutenticado
    {
        $token = $request->bearerToken() ?? throw HttpException::naoAutenticado();

        $stmt = $this->pdo->prepare('SELECT id, username, email, role FROM usuarios WHERE token = :token');
        $stmt->execute([':token' => $token]);
        $linha = $stmt->fetch();

        if (!$linha) {
            throw HttpException::naoAutenticado('Sessão expirada. Faça login novamente.');
        }

        return new UsuarioAutenticado((int) $linha['id'], $linha['username'], $linha['email'], $linha['role']);
    }

    public function exigirAdmin(Request $request): UsuarioAutenticado
    {
        $usuario = $this->exigirUsuario($request);
        if (!$usuario->ehAdmin()) {
            throw HttpException::proibido();
        }
        return $usuario;
    }
}
