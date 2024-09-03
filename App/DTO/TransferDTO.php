<?php declare(strict_types=1);

namespace App\DTO;

class TransferDTO
{
    public function __construct(
        public float $value,
        public int $payer,
        public int $payee,
    ) {
        //
    }

    public static function make(object $request): self
    {
        return new self(
            floatval($request->value),
            intval($request->payer),
            intval($request->payee),
        );
    }
}
