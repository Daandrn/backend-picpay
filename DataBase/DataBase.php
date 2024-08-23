<?php

namespace DataBase;

use Env;
use Exception;
use PDO;
use PDOException;

require_once __DIR__ . '/../env.php';

class DataBase
{
    use Env;

    public static function conn(
        string $drive = null,
        string $host = null,
        string $dataBase = null,
        string $user = null,
        string $password = null,
    ): PDO|Exception {
        $drive    ??= self::DRIVE;
        $host     ??= self::HOST;
        $dataBase ??= self::DATA_BASE;
        $user     ??= self::USER;
        $password ??= self::PASSWORD;

        try {
            return new PDO("{$drive}:host={$host};dbname={$dataBase}", $user, $password);
        } catch (PDOException $error) {
            throw new Exception("Erro na conexão ao bando de dados: " . $error->getMessage());
        }
    }
}
