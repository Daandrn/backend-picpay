<?php declare(strict_types=1);

namespace App\Services;

use App\DTO\TransferDTO;
use App\Repositories\ContaRepository;
use PDO;

require_once __DIR__ . '/../Repositories/ContaRepository.php';

class TransferService
{
    protected ContaRepository $contaRepository;

    public function __construct()
    {
        $this->contaRepository = new ContaRepository;
    }

    public function fazerTransferencia(TransferDTO $transferDTO): array
    {
        $this->contaRepository->pdo->beginTransaction();

        $contaPagador = $this->contaRepository->selectContaForUpdate($transferDTO->pagador);
        $contaRecebedor = $this->contaRepository->selectContaForUpdate($transferDTO->recebedor);

        if ($contaPagador === false || $contaRecebedor === false) {
            return [
                'body' => [
                    'message' => null,
                    'error_message' => match (false) {
                        $contaPagador => "O usuario codido {$transferDTO->pagador} solicitado para pagar não possui conta!",
                        $contaRecebedor => "O usuario  codido {$transferDTO->recebedor} solicitado para receber não possui conta!",
                    },
                ],
                'status' => [
                    'error_status' => true,
                    'code' => 401,
                ]
            ];
        }

        $statusTransacao = $this->contaRepository->inserirTransacao(
            $contaPagador->id_usuario,
            $contaRecebedor->id_usuario,
            $contaPagador->id,
            $contaRecebedor->id,
            $transferDTO->valor
        );

        $statusUpdateContaPagador = $this->contaRepository->atualizaSaldoConta($this->contaRepository::TIPO_PAGADOR, $contaPagador->id, $transferDTO->valor);
        $statusUpdateContaRecebedor = $this->contaRepository->atualizaSaldoConta($this->contaRepository::TIPO_RECEBEDOR, $contaRecebedor->id, $transferDTO->valor);

        if (
            $contaPagador !== false
            && $contaRecebedor !== false
            && $statusTransacao
            && $statusUpdateContaPagador
            && $statusUpdateContaRecebedor
        ) {
            $this->contaRepository->pdo->commit();

            return [
                'body' => [
                    'message' => 'Transferencia realizada com sucesso!',
                    'error_message' => null,
                ],
                'status' => [
                    'error_status' => false,
                    'code' => 200,
                ]
            ];
        }

        $this->contaRepository->pdo->rollBack();

        return [
            'body' => [
                'message' => 'Erro ao realizar transferencia!',
                'error_message' => 'Erro ao realizar transferencia!',
            ],
            'status' => [
                'error_status' => true,
                'code' => 200,
            ]
        ];
    }
}