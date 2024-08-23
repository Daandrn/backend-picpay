<?php

namespace dataBase;

use PDOException;

require_once __DIR__ . '/dataBase.php';

class AppInstall
{
    public static function exec(): void
    {
        $dropTableUsers = <<<SQL
            DROP TABLE IF EXISTS users;
        SQL;

        $createTableUsers = <<<SQL
            CREATE TABLE IF NOT EXISTS users (
                id  smallserial unique primary key,
                name varchar(100) not null,
                cnpj varchar(14) unique,
                cpf varchar(11) unique,
                email varchar(50) unique not null,
                password text,
                type smallint
            );
        SQL;

        $dropTableTransactions = <<<SQL
            DROP TABLE IF EXISTS transactions;
        SQL;

        $dropTableAccount = <<<SQL
            DROP TABLE IF EXISTS accounts;
        SQL;

        $createTableAccount = <<<SQL
            CREATE TABLE IF NOT EXISTS accounts (
                id smallserial unique unique primary key,
                id_user smallint not null,
                balance decimal(12, 2),

                FOREIGN KEY (id_user) REFERENCES users(id)
            );
        SQL;

        $createTableTransactions = <<<SQL
            CREATE TABLE IF NOT EXISTS transactions (
                id bigserial unique primary key,
                payer smallint,
                payee smallint,
                id_payer_account smallint,
                id_payee_account smallint,
                date timestamp not null,
                value decimal(12, 2),
                active boolean default(true),
                refund_date timestamp,
                id_refund_transaction bigint,

                FOREIGN KEY (id_payer_account) REFERENCES accounts(id),
                FOREIGN KEY (id_payee_account) REFERENCES accounts(id)
            );
        SQL;

        $insertUsers = <<<SQL
            INSERT INTO users (id, name, cnpj, cpf, email, password, type) VALUES
            (4, 'Joao da silva', null, '12345678910', 'joao@teste.com', '', 1),
            (15, 'Mercearia cesar', '12345678910112', null, 'mercearia@teste.com', '', 2);
        SQL;

        $insertAccount = <<<SQL
            INSERT INTO accounts (id_user, balance) VALUES
            (4, 1500.0),
            (15, 1600.0);
        SQL;

        try {
            $statment = dataBase::conn();
            $statment->beginTransaction();
            $statment->query($dropTableTransactions);
            $statment->query($dropTableAccount);
            $statment->query($dropTableUsers);
            $statment->query($createTableUsers);
            $statment->query($createTableAccount);
            $statment->query($insertUsers);
            $statment->query($createTableTransactions);
            $statment->query($insertAccount);
            $statment->commit();
        } catch (PDOException $error) {
            echo "Erro ao instalar dados da aplicação: " . $error->getMessage();
            exit;
        }
    }
}
