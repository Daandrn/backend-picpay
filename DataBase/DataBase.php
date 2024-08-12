<?php

namespace DataBase;

use Exception;
use PDO;
use PDOException;

class DataBase
{
    private static string $drive = 'pgsql';
    private static string $host = 'localhost';
    private static string $dataBase = 'postgres';
    private static string $user = 'postgres';
    private static string $password = '';

    public static function conn(
        string $drive = null,
        string $host = null,
        string $dataBase = null,
        string $user = null,
        string $password = null,
    ): PDO|Exception {
        $drive ??= self::$drive;
        $host ??= self::$host;
        $dataBase ??= self::$dataBase;
        $user ??= self::$user;
        $password ??= self::$password;

        try {
            return new PDO("{$drive}:host={$host};dbname={$dataBase}", $user, $password);
        } catch (PDOException $error) {
            throw new Exception("Erro na conecção ao bando de dados: " . $error->getMessage());
        }
    }
}
