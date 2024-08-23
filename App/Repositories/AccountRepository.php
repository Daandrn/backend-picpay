<?php declare(strict_types=1);

namespace App\Repositories;

use App\Abstracts\PDOfunctions;
use PDO;

require_once __DIR__ . '/../Abstracts/PDOfunctions.php';

class AccountRepository extends PDOfunctions
{
    public const PAYER = 1;
    public const PAYEE = 2;

    public function selectAccountForUpdate(int $userId): object|false
    {
        $accountUser = <<<SQL
            SELECT * 
            FROM accounts 
            WHERE id_user = ?
            FOR UPDATE;
        SQL;

        $accountUser = $this->prepare($accountUser);
        $accountUser->bindValue(1, $userId, PDO::PARAM_INT);
        $accountUser->execute();

        if ($accountUser->rowCount() > 0) {
            return $accountUser->fetch(PDO::FETCH_OBJ);
        }

        return false;
    }

    public function insertTransaction(
        int $payerId,
        int $payeeId,
        int $accountPayerId,
        int $accountPaueeId,
        float $value
    ): bool {
        $insert_transaction = <<<SQL
        INSERT INTO transactions (
            payer, 
            payee, 
            id_payer_account, 
            id_payee_account, 
            date, 
            value
            ) VALUES (?, ?, ?, ?, ?, ?);
        SQL;

        $transaction = $this->prepare($insert_transaction);
        $transaction->bindValue(1, $payerId, PDO::PARAM_INT);
        $transaction->bindValue(2, $payeeId, PDO::PARAM_INT);
        $transaction->bindValue(3, $accountPayerId, PDO::PARAM_INT);
        $transaction->bindValue(4, $accountPaueeId, PDO::PARAM_INT);
        $transaction->bindValue(5, date_create('now')->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $transaction->bindValue(6, $value);
        return $transaction->execute();
    }

    public function updateAccountBalance(int $payer_payee, int $accountId, float $value): bool
    {    
        $updateAccountPayer = <<<SQL
            UPDATE accounts 
            SET 
                balance = (balance - ?)::numeric(12, 2)
            WHERE id = ?;
        SQL;

        $updateAccountPayee = <<<SQL
            UPDATE accounts 
            SET 
                balance = (balance + ?)::numeric(12, 2)
            WHERE id = ?;
        SQL;
        
        $updateAccount = $this->prepare(
            match($payer_payee) {
                1 => $updateAccountPayer,
                2 => $updateAccountPayee
        });

        $updateAccount->bindValue(1, $value);
        $updateAccount->bindValue(2, $accountId, PDO::PARAM_INT);

        return $updateAccount->execute();
    }

    public function hasAccount(int $userId): bool
    {
        $userAccount = <<<SQL
            SELECT 1
            FROM accounts 
            WHERE id_user = ?
            limit 1;
        SQL;

        $userAccount = $this->prepare($userAccount);
        $userAccount->bindValue(1, $userId, PDO::PARAM_INT);
        $userAccount->execute();

        return $userAccount->rowCount() > 0;
    }

    public function userType(int $userId): int
    {
        $userType = <<<SQL
            SELECT type
            FROM users
            WHERE id = ?;
        SQL;

        $userType = $this->prepare($userType);
        $userType->bindValue(1, $userId, PDO::PARAM_INT);
        $userType->execute();
        $userType = $userType->fetch(PDO::FETCH_OBJ);

        return $userType->type;
    }
}
