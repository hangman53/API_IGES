<?php
declare(strict_types=1);

$raiz      = dirname(__DIR__) . '/src';
$violacoes = [];

$arquivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($raiz, FilesystemIterator::SKIP_DOTS));
foreach ($arquivos as $arquivo) {
    if ($arquivo->getExtension() !== 'php') {
        continue;
    }
    $relativo = str_replace(DIRECTORY_SEPARATOR, '/', substr($arquivo->getPathname(), strlen($raiz) + 1));
    $codigo   = file_get_contents($arquivo->getPathname());

    preg_match_all('/App\\\\Modules\\\\(\w+)\\\\(\w+)/', $codigo, $usos, PREG_SET_ORDER);

    if (str_starts_with($relativo, 'Shared/')) {
        foreach ($usos as [$uso]) {
            $violacoes[] = "$relativo: o Shared não pode depender de módulos ($uso)";
        }
        continue;
    }

    if (!preg_match('~^Modules/(\w+)/~', $relativo, $m)) {
        continue;
    }
    $moduloAtual = $m[1];
    foreach ($usos as [$uso, $outroModulo, $camada]) {
        if ($outroModulo !== $moduloAtual && $camada !== 'Api') {
            $violacoes[] = "$relativo: $moduloAtual só pode usar $outroModulo pelo namespace Api ($uso)";
        }
    }
}

if ($violacoes) {
    fwrite(STDERR, implode(PHP_EOL, array_unique($violacoes)) . PHP_EOL);
    exit(1);
}
echo "Fronteiras entre módulos OK" . PHP_EOL;
