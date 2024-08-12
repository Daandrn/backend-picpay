<?php

namespace DataBase;

use PDOException;

require_once __DIR__ . '/DataBase.php';

class InstalarBd
{
    public static function exec(): void
    {
        $dropTableUsuarios = <<<SQL
            DROP TABLE IF EXISTS usuarios;
        SQL;

        $createTableUsuarios = <<<SQL
            CREATE TABLE IF NOT EXISTS usuarios (
                id  smallserial unique primary key,
                nome varchar(100) not null,
                cnpj varchar(14) unique,
                cpf varchar(11) unique,
                email varchar(50) unique not null,
                senha text
            );
        SQL;

        $dropTableTransacoes = <<<SQL
            DROP TABLE IF EXISTS transacoes;
        SQL;

        $dropTableConta = <<<SQL
            DROP TABLE IF EXISTS contas;
        SQL;

        $createTableConta = <<<SQL
            CREATE TABLE IF NOT EXISTS contas (
                id smallserial unique unique primary key,
                id_usuario smallint not null,
                saldo decimal(12, 2),

                FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
            );
        SQL;

        $createTableTransacoes = <<<SQL
            CREATE TABLE IF NOT EXISTS transacoes (
                id bigserial unique primary key,
                pagador smallint,
                recebedor smallint,
                id_conta_pagador smallint,
                id_conta_recebedor smallint,
                data timestamp not null,
                valor decimal(12, 2),
                ativa boolean default(true),
                data_estorno timestamp,
                id_transcao_estorno bigint,

                FOREIGN KEY (id_conta_pagador) REFERENCES contas(id),
                FOREIGN KEY (id_conta_recebedor) REFERENCES contas(id)
            );
        SQL;

        $insereUsusarios = <<<SQL
            INSERT INTO usuarios (id, nome, cnpj, cpf, email, senha) VALUES
            (4, 'Joao da silva', null, '12345678910', 'joao@teste.com', ''),
            (15, 'Mercearia cesar', '12345678910112', null, 'mercearia@teste.com', '');
        SQL;

        $criaConta = <<<SQL
            INSERT INTO contas (id_usuario, saldo) VALUES
            (4, 0.0),
            (15, 0.0);
        SQL;

        try {
            $statment = DataBase::conn();
            $statment->beginTransaction();
            $statment->query($dropTableUsuarios);
            $statment->query($createTableUsuarios);
            $statment->query($dropTableTransacoes);
            $statment->query($dropTableConta);
            $statment->query($createTableConta);
            $statment->query($createTableTransacoes);
            $statment->query($insereUsusarios);
            $statment->query($criaConta);
            $statment->commit();
        } catch (PDOException $error) {
            echo "Erro ao instalar dados da aplicação: " . $error->getMessage();
            exit;
        }
    }
}
