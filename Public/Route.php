<?php

use App\Controllers\AppController;

require __DIR__ . '/../App/Controllers/AppController.php';

class Route
{
    protected static array $routes;
    protected static array $clientRoute;

    public function __construct()
    {
        self::$routes = [
            'POST' => [
                'transfer' => 'transfer'
            ],
            'GET' => [
                'instalarApp' => 'instalarApp'
            ]
        ];

        self::$clientRoute = explode('/', $_SERVER['REQUEST_URI']);
    }

    public function routes(): void
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if (isset(self::$routes['POST'][strval(self::$clientRoute[1])])) {
                    $method = self::$routes['POST'][self::$clientRoute[1]];

                    (new AppController)->{$method}();

                    return;
                }

                http_response_code(404);
                echo json_encode([
                    'error' => "Acesso não autorizado!"
                ]);

                break;
            case 'GET':
                if (isset(self::$routes['GET'][strval(self::$clientRoute[1])])) {
                    $method = self::$routes['GET'][self::$clientRoute[1]];

                    (new AppController)->{$method}();

                    return;
                }

                http_response_code(404);

                echo json_encode([
                    'error' => "Acesso não autorizado!"
                ]);

                break;
            default:
                throw new Exception('Metódo inválido!', 500);
        }
    }
}
