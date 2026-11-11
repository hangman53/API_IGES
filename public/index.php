<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Shared\Http\Request;
use App\Shared\Kernel;

Kernel::criar(require __DIR__ . '/../config/modules.php')
    ->handle(Request::fromGlobals())
    ->send();
