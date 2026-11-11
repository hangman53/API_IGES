<?php
declare(strict_types=1);

namespace App\Shared\Database;

use PDO;

final class Connection
{
    public static function fromEnv(): PDO
    {
        $url = getenv('DATABASE_URL');
        if ($url) {
            $partes   = parse_url($url);
            $host     = $partes['host'];
            $port     = $partes['port'] ?? 5432;
            $dbname   = ltrim($partes['path'], '/');
            $user     = urldecode($partes['user']);
            $password = urldecode($partes['pass'] ?? '');
        } else {
            $host     = getenv('DB_HOST') ?: 'db';
            $port     = getenv('DB_PORT') ?: 5432;
            $dbname   = getenv('DB_NAME') ?: 'iges';
            $user     = getenv('DB_USER') ?: 'iges_user';
            $password = getenv('DB_PASSWORD') ?: '';
        }

        return new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
