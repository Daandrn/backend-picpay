<?php declare(strict_types=1);

namespace App\Services;

use App\DTO\TransferDTO;
use App\Interfaces\Authorizer;
use App\Interfaces\Mailer;
use App\Repositories\AccountRepository;

require_once __DIR__ . '/../Repositories/AccountRepository.php';
require_once __DIR__ . '/../Interfaces/Authorizer.php';

class TransferService
{
    protected AccountRepository $accountRepository;

    public function __construct()
    {
        $this->accountRepository = new AccountRepository;
    }

    public function makeTransfer(TransferDTO $transferDTO, Authorizer $authorizer, Mailer $mailer): array
    {
        $this->accountRepository->beginTransaction();

        if ($this->accountRepository->userType($transferDTO->payer) === 2) {
            return [
                'body' => [
                    'message' => null,
                    'error_message' => "Usuário logista não pode realizar transferencia!",
                ],
                'status' => [
                    'error_status' => true,
                    'code' => 401,
                ]
            ];
        };

        $payerAccount = $this->accountRepository->selectAccountForUpdate($transferDTO->payer);
        $payeeAcount = $this->accountRepository->selectAccountForUpdate($transferDTO->payee);

        if ($payerAccount === false || $payeeAcount === false) {
            return [
                'body' => [
                    'message' => null,
                    'error_message' => match (false) {
                        $payerAccount => "O usuario codigo {$transferDTO->payer} solicitado para pagar não possui conta!",
                        $payeeAcount => "O usuario  codigo {$transferDTO->payee} solicitado para receber não possui conta!",
                    },
                ],
                'status' => [
                    'error_status' => true,
                    'code' => 401,
                ]
            ];
        }

        $balanceIsUnavailable = $payerAccount->balance < $transferDTO->value;

        if ($balanceIsUnavailable) {
            $this->accountRepository->rollback();

            $difference = $transferDTO->value - $payerAccount->balance;
            
            return [
                'body' => [
                    'message' => null,
                    'error_message' => "Não há saldo disponível para realizar a transferencia! Saldo atual: R$ {$payerAccount->balance}. Valor da transação: R$ {$transferDTO->value}. Quanto falta: R$ {$difference}!",
                ],
                'status' => [
                    'error_status' => true,
                    'code' => 401,
                ]
            ];
        }

        if (! $authorizer->getAuthorization()) {
            $this->accountRepository->rollback();

            return [
                'body' => [
                    'message' => null,
                    'error_message' => "Transação não autorizada pelo emissor. Verifique!",
                ],
                'status' => [
                    'error_status' => true,
                    'code' => 401,
                ]
            ];
        }

        $transactionStatus = $this->accountRepository->insertTransaction(
            $payerAccount->id_user,
            $payeeAcount->id_user,
            $payerAccount->id,
            $payeeAcount->id,
            $transferDTO->value
        );

        $payerAccountHasUpdated = $this->accountRepository->updateAccountBalance(
            $this->accountRepository::PAYER, 
            $payerAccount->id, $transferDTO->value
        );

        $payeeAcountHasUpdated = $this->accountRepository->updateAccountBalance(
            $this->accountRepository::PAYEE, 
            $payeeAcount->id, 
            $transferDTO->value
        );

        if (
            $payerAccount !== false
            && $payeeAcount !== false
            && $transactionStatus
            && $payerAccountHasUpdated
            && $payeeAcountHasUpdated
        ) {
            $this->accountRepository->commit();
            
            $mailer->send($transferDTO->payer, 'Olá, Pagador! O seu pagamento foi realizado com sucesso.');
            $mailer->send($transferDTO->payee, 'Olá, Recebedor! O seu pagamento foi realizado com sucesso.');

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

        $this->accountRepository->rollback();

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