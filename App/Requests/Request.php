<?php declare(strict_types=1);

namespace App\Requests;

use Exception;

require_once __DIR__ . '/CurlFuncs.php';

/**
 * Lida com as requisições http
 */
class Request
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

    public static function sendGet(string $url, ?array $body = null): string|false
    {
        $cUrl = new CurlFuncs;
        
        return $cUrl->makeGet($url);
    }

    public static function sendPost(string $url, ?array $body = []): string
    {
        $cUrl = new CurlFuncs;
        $cUrl->init();
        $cUrl->options($url, true, body: $body);
        $response = $cUrl->execute(null);
        $cUrl->close(null);
        
        return $response;
    }
}
