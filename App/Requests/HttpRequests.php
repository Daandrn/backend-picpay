<?php declare(strict_types=1);

namespace App\Requests;

use Exception;

/**
 * Lida com as requisições http
 */
final class HttpRequest
{
    public static function get(): object
    {
        if (
            isset($_SERVER['REQUEST_METHOD'])
            && $_SERVER['REQUEST_METHOD'] === 'GET'
        ) {
            $request = file_get_contents('php://input');

            return json_decode($request, false);
        }

        return throw new Exception("O metodo http não corresponde com a requisição!");
    }

    public static function post(): object
    {
        if (
            isset($_SERVER['REQUEST_METHOD'])
            && $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            $request = file_get_contents('php://input');

            return json_decode($request, false);
        }

        return throw new Exception("O metodo http não corresponde com a requisição!");
    }

    public static function send(string $method, ?array $body = null): array
    {
        $response = [];

        return $response;
    }
}