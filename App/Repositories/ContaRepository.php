<?php declare(strict_types=1);

namespace App\Repositories;

use DataBase\DataBase;
use PDO;

class ContaRepository
{
    public PDO $pdo;
    
    public const TIPO_PAGADOR = 1;
    public const TIPO_RECEBEDOR = 2;
    
    public function __construct()
    {
        $this->pdo = DataBase::conn();
    }

    public function selectContaForUpdate(int $id_usuario): object|false
    {
        $conta_usuario = <<<SQL
            SELECT * 
            FROM contas 
            WHERE id_usuario = ?
            FOR UPDATE;
        SQL;

        $conta_usuario = $this->pdo->prepare($conta_usuario);
        $conta_usuario->bindValue(1, $id_usuario, PDO::PARAM_INT);
        $conta_usuario->execute();

        if ($conta_usuario->rowCount() > 0) {
            return $conta_usuario->fetch(PDO::FETCH_OBJ);
        }

        return false;
    }

    public function inserirTransacao(
        int $id_pagador,
        int $id_recebedor,
        int $id_conta_pagador,
        int $id_conta_recebedor,
        float $valor_transferencia
    ): bool {
        $insert_transacao = <<<SQL
        INSERT INTO transacoes (
            pagador, 
            recebedor, 
            id_conta_pagador, 
            id_conta_recebedor, 
            data, 
            valor
            ) VALUES (?, ?, ?, ?, ?, ?);
        SQL;

        $transacao = $this->pdo->prepare($insert_transacao);
        $transacao->bindValue(1, $id_pagador, PDO::PARAM_INT);
        $transacao->bindValue(2, $id_recebedor, PDO::PARAM_INT);
        $transacao->bindValue(3, $id_conta_pagador, PDO::PARAM_INT);
        $transacao->bindValue(4, $id_conta_recebedor, PDO::PARAM_INT);
        $transacao->bindValue(5, date_create('now')->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $transacao->bindValue(6, $valor_transferencia);
        return $transacao->execute();
    }

    public function atualizaSaldoConta(int $pagadorRecebedor, int $id_conta, float $valor): bool
    {    
        $update_conta_pagador = <<<SQL
            UPDATE contas 
            SET 
                saldo = (saldo - ?)::numeric(12, 2)
            WHERE id = ?;
        SQL;

        $update_conta_recebedor = <<<SQL
            UPDATE contas 
            SET 
                saldo = (saldo + ?)::numeric(12, 2)
            WHERE id = ?;
        SQL;
        
        $updateConta = $this->pdo->prepare(
            match($pagadorRecebedor) {
                1 => $update_conta_pagador,
                2 => $update_conta_recebedor
        });

        $updateConta->bindValue(1, $valor);
        $updateConta->bindValue(2, $id_conta, PDO::PARAM_INT);

        return $updateConta->execute();
    }

    public function usuarioPussuiConta(int $id_usuario): bool
    {
        $conta_usuario = <<<SQL
            SELECT 1
            FROM contas 
            WHERE id_usuario = ?
            limit 1;
        SQL;

        $conta_usuario = $this->pdo->prepare($conta_usuario);
        $conta_usuario->bindValue(1, $id_usuario, PDO::PARAM_INT);
        $conta_usuario->execute();

        return $conta_usuario->rowCount() > 0;
    }
}
