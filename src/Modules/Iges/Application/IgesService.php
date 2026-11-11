<?php
declare(strict_types=1);

namespace App\Modules\Iges\Application;

use App\Modules\Enquadramento\Api\EnquadramentoApi;
use App\Modules\Iges\Api\IgesApi;
use App\Modules\Iges\Api\ProgressoDto;

final class IgesService implements IgesApi
{
    public function __construct(private readonly EnquadramentoApi $enquadramento)
    {
    }

    public function progresso(int $usuarioId): ProgressoDto
    {
        return new ProgressoDto(
            $this->enquadramento->instrumentosRespondidos($usuarioId),
            EnquadramentoApi::TOTAL_INSTRUMENTOS,
        );
    }
}
