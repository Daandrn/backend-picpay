<?php declare(strict_types=1);

namespace App\DTO;

class TransferDTO
{
    public function __construct(
        public float $valor,
        public int $pagador,
        public int $recebedor,
    ) {
        //
    }

    public static function make(object $httpRequest): self
    {
        return new self(
            $httpRequest->value,
            $httpRequest->payer,
            $httpRequest->payee,
        );
    }
}