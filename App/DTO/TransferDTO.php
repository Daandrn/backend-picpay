<?php declare(strict_types=1);

namespace App\DTO;
use App\Requests\TransferRequest;

require_once __DIR__ . "/../Requests/TransferRequest.php";

class TransferDTO
{
    public function __construct(
        public float $value,
        public int $payer,
        public int $payee,
    ) {
        //
    }

    public static function make(TransferRequest $request): self
    {
        return new self(
            $request->value,
            $request->payer,
            $request->payee,
        );
    }
}
