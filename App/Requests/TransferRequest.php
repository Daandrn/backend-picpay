<?php declare(strict_types=1);

namespace App\Requests;

use App\Requests\ApiResponse;
use App\Requests\Request;

class TransferRequest extends Request
{
    public function validation(object $request): ApiResponse|true
    {
        if (
            !isset($request->value)
            || !isset($request->payer)
            || !isset($request->payee)
        ) {
            ApiResponse::send([
                'body' => [
                    'message' => "Erro no corpo da requisição!",
                    'error_message' => "Todos os campos da requisição devem ser informados!",
                ]
            ], 400);

            exit;
        }
        
        if (! is_numeric($request->value)) {
            ApiResponse::send([
                'body' => [
                    'message' => "Erro no corpo da requisição!",
                    'error_message' => "O valor deve ser númerico!",
                ]
            ], 422);

            exit;
        }

        if (! is_int($request->payer)) {
            ApiResponse::send([
                'body' => [
                    'message' => "Erro no corpo da requisição!",
                    'error_message' => "O codigo do pagador é inválido!",
                ]
            ], 422);

            exit;
        }

        if (! is_int($request->payee)) {
            ApiResponse::send([
                'body' => [
                    'message' => "Erro no corpo da requisição!",
                    'error_message' => "O codigo do recebedor é inválido!",
                ]
            ], 422);

            exit;
        }

        return true;
    }
}